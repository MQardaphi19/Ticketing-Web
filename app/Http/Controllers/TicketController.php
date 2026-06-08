<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketComment;
use App\Models\User;
use App\Notifications\TicketResolvedNotification;
use App\Notifications\TicketStatusChangedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketController extends Controller
{
    public function my(): View
    {
        $tickets = Ticket::with(['user', 'category', 'assignedTechnician'])
            ->withCount('attachments')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('tickets.my', compact('tickets'));
    }

    public function create(): View
    {
        $categories = Category::all();

        return view('tickets.create', compact('categories'));
    }

    public function index(Request $request): View
    {
        $query = Ticket::with(['user', 'category', 'assignedTechnician'])
            ->withCount('attachments');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tickets = $query->latest()->get();
        $categories = Category::all();
        $technicians = User::where('role', 'teknisi')->get();

        return view('tickets.index', compact('tickets', 'categories', 'technicians'));
    }

    public function show(int $id): View
    {
        $ticket = Ticket::with(['user', 'category', 'assignedTechnician', 'attachments.user', 'messages.user'])
            ->findOrFail($id);

        $technicians = User::where('role', 'teknisi')->get();

        return view('tickets.show', compact('ticket', 'technicians'));
    }

    public function assigned(): View
    {
        $tickets = Ticket::with(['user', 'category'])
            ->withCount('attachments')
            ->where('assigned_to', Auth::id())
            ->latest()
            ->get();

        return view('tickets.my', compact('tickets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $category = Category::findOrFail($validated['category_id']);
        $files = $request->file('attachments', []);
        unset($validated['attachments']);

        $validated['user_id'] = Auth::id();
        $validated['ticket_number'] = 'TIX-'.now()->format('Ym').'-'.str_pad(Ticket::count() + 1, 3, '0', STR_PAD_LEFT);
        $validated['status'] = 'open';
        $validated['sla_due_date'] = now()->addHours($category->sla_hours);

        $storedFiles = [];

        try {
            DB::transaction(function () use ($validated, $files, &$storedFiles): void {
                $ticket = Ticket::create($validated);

                foreach ($files as $file) {
                    $path = $file->store("tickets/{$ticket->id}/attachments", 'local');

                    if (! $path) {
                        throw new \RuntimeException('Attachment file could not be stored.');
                    }

                    $storedFiles[] = $path;

                    $ticket->attachments()->create([
                        'user_id' => Auth::id(),
                        'disk' => 'local',
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'stored_name' => basename($path),
                        'mime_type' => $file->getMimeType(),
                        'extension' => strtolower($file->getClientOriginalExtension()),
                        'size' => $file->getSize(),
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            foreach ($storedFiles as $path) {
                Storage::disk('local')->delete($path);
            }

            report($exception);

            return back()
                ->withInput()
                ->withErrors(['attachments' => 'Lampiran gagal disimpan. Silakan coba lagi.']);
        }

        return redirect()->route('tickets.my')->with('success', 'Tiket berhasil dibuat');
    }

    public function downloadAttachment(Ticket $ticket, TicketAttachment $attachment): StreamedResponse
    {
        abort_unless($attachment->ticket_id === $ticket->id, 404);

        $user = Auth::user();
        $canDownload = $ticket->user_id === $user->id
            || (string) $ticket->assigned_to === (string) $user->id
            || $user->isAdmin()
            || $user->isStaff();

        abort_unless($canDownload, 403);
        abort_unless(Storage::disk($attachment->disk)->exists($attachment->path), 404);

        return Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $ticket = Ticket::with('user')->findOrFail($id);
        $previousStatus = $ticket->status;

        $validated = $request->validate([
            'assigned_to' => 'sometimes|nullable|exists:users,id',
            'status' => 'sometimes|required|in:open,in_progress,resolved,closed',
        ]);

        if ($request->filled('assigned_to') && $ticket->status === 'open') {
            $validated['status'] = 'in_progress';
        }

        $newStatus = $validated['status'] ?? $ticket->status;

        if ($newStatus === 'resolved' && ! $ticket->resolved_at) {
            $validated['resolved_at'] = now();
        }

        if ($newStatus === 'closed' && ! $ticket->resolved_at) {
            $validated['resolved_at'] = now();
        }

        $ticket->update($validated);
        $ticket->refresh();

        $this->notifyTicketStatusChangedIfNeeded($ticket, $previousStatus);

        return back()->with('success', 'Tiket berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->delete();

        return back()->with('success', 'Tiket berhasil dihapus');
    }

    public function messages(Request $request, Ticket $ticket): JsonResponse
    {
        abort_unless($this->canAccessTicketChat($ticket), 403);

        $afterId = max((int) $request->query('after_id', 0), 0);

        $messages = $ticket->messages()
            ->with('user')
            ->when($afterId > 0, fn ($query) => $query->where('id', '>', $afterId))
            ->orderBy('id')
            ->limit(50)
            ->get();

        return response()->json([
            'data' => $messages->map(fn (TicketComment $message) => $this->formatMessage($message))->values(),
            'meta' => [
                'last_id' => $messages->last()?->id ?? $afterId,
            ],
        ]);
    }

    public function storeMessage(Request $request, Ticket $ticket): JsonResponse
    {
        abort_unless($this->canAccessTicketChat($ticket), 403);

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'is_internal' => 'nullable|boolean',
        ]);

        $message = $ticket->messages()->create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'is_internal' => $request->boolean('is_internal', false),
        ])->load('user');

        return response()->json([
            'message' => 'Pesan berhasil dikirim',
            'data' => $this->formatMessage($message),
        ], 201);
    }

    private function canAccessTicketChat(Ticket $ticket): bool
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isKepalaDiskominfo()) {
            return true;
        }

        return $user->isPegawaiDinas() && $ticket->user_id === $user->id;
    }

    private function formatMessage(TicketComment $message): array
    {
        return [
            'id' => $message->id,
            'ticket_id' => $message->ticket_id,
            'user_id' => $message->user_id,
            'user_name' => $message->user?->name,
            'content' => $message->content,
            'is_internal' => $message->is_internal,
            'created_at' => $message->created_at?->toISOString(),
            'created_at_time' => $message->created_at?->format('H:i'),
        ];
    }

    public function bulkAssign(Request $request): RedirectResponse
    {
        $this->normalizeTicketIds($request);

        $validated = $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:tickets,id',
            'assigned_to' => 'required|string|max:255',
        ]);

        $tickets = Ticket::with('user')
            ->whereIn('id', $validated['ticket_ids'])
            ->get();

        foreach ($tickets as $ticket) {
            $previousStatus = $ticket->status;

            $ticket->update([
                'assigned_to' => $validated['assigned_to'],
                'status' => 'in_progress',
            ]);

            $ticket->refresh();

            $this->notifyTicketStatusChangedIfNeeded($ticket, $previousStatus);
        }

        return back()->with('success', 'Teknisi berhasil ditugaskan');
    }

    public function bulkStatus(Request $request): RedirectResponse
    {
        $this->normalizeTicketIds($request);

        $validated = $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:tickets,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $tickets = Ticket::with('user')
            ->whereIn('id', $validated['ticket_ids'])
            ->get();

        foreach ($tickets as $ticket) {
            $previousStatus = $ticket->status;
            $updates = [
                'assigned_to' => $validated['assigned_to'] ?? null,
                'status' => $validated['status'],
            ];

            if (in_array($validated['status'], ['resolved', 'closed'], true) && ! $ticket->resolved_at) {
                $updates['resolved_at'] = now();
            }

            $ticket->update($updates);
            $ticket->refresh();

            $this->notifyTicketStatusChangedIfNeeded($ticket, $previousStatus);
        }

        return back()->with('success', 'Status tiket berhasil diperbarui');
    }

    private function normalizeTicketIds(Request $request): void
    {
        if (! is_string($request->input('ticket_ids'))) {
            return;
        }

        $ticketIds = collect(explode(',', $request->input('ticket_ids')))
            ->map(fn (string $ticketId): string => trim($ticketId))
            ->filter()
            ->values()
            ->all();

        $request->merge(['ticket_ids' => $ticketIds]);
    }

    private function notifyTicketStatusChangedIfNeeded(Ticket $ticket, string $previousStatus): void
    {
        if ($previousStatus === $ticket->status || ! in_array($ticket->status, ['in_progress', 'resolved', 'closed'], true)) {
            return;
        }

        $ticket->loadMissing('user');
        $user = $ticket->user;

        if (! $user?->isPegawaiDinas() || blank($user->email)) {
            return;
        }

        try {
            $notification = $ticket->status === 'resolved'
                ? new TicketResolvedNotification($ticket)
                : new TicketStatusChangedNotification($ticket, $ticket->status);

            $user->notify($notification);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
