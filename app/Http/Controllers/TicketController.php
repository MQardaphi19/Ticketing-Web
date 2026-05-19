<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function my(): \Illuminate\View\View
    {
        $tickets = Ticket::with(['user', 'category', 'assignedTechnician'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('tickets.my', compact('tickets'));
    }

    public function create(): \Illuminate\View\View
    {
        $categories = Category::all();

        return view('tickets.create', compact('categories'));
    }

    public function index(Request $request): \Illuminate\View\View
    {
        $query = Ticket::with(['user', 'category', 'assignedTechnician']);

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

    public function show(int $id): \Illuminate\View\View
    {
        $ticket = Ticket::with(['user', 'category', 'assignedTechnician', 'messages.user'])
            ->findOrFail($id);

        $technicians = User::where('role', 'teknisi')->get();

        return view('tickets.show', compact('ticket', 'technicians'));
    }

    public function assigned(): \Illuminate\View\View
    {
        $tickets = Ticket::with(['user', 'category'])
            ->where('assigned_to', Auth::id())
            ->latest()
            ->get();

        return view('tickets.my', compact('tickets'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high',
        ]);

        $category = Category::findOrFail($validated['category_id']);

        $validated['user_id'] = Auth::id();
        $validated['ticket_number'] = 'TIX-' . now()->format('Ym') . '-' . str_pad(Ticket::count() + 1, 3, '0', STR_PAD_LEFT);
        $validated['status'] = 'open';
        $validated['sla_due_date'] = now()->addHours($category->sla_hours);

        Ticket::create($validated);

        return redirect()->route('admin.tickets.my')->with('success', 'Tiket berhasil dibuat');
    }

    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $ticket = Ticket::findOrFail($id);

        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        if ($request->filled('assigned_to') && $ticket->status === 'open') {
            $validated['status'] = 'in_progress';
        }

        if ($validated['status'] === 'resolved' && ! $ticket->resolved_at) {
            $validated['resolved_at'] = now();
        }

        if ($validated['status'] === 'closed' && ! $ticket->resolved_at) {
            $validated['resolved_at'] = now();
        }

        $ticket->update($validated);

        return back()->with('success', 'Tiket berhasil diperbarui');
    }

    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->delete();

        return back()->with('success', 'Tiket berhasil dihapus');
    }

    public function storeMessage(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal' => 'nullable|boolean',
        ]);

        $validated['ticket_id'] = $id;
        $validated['user_id'] = Auth::id();
        $validated['is_internal'] = $request->boolean('is_internal', false);

        $message = TicketMessage::create($validated);

        return response()->json(['message' => 'Pesan berhasil dikirim', 'data' => $message->load('user')]);
    }

    public function bulkAssign(Request $request): \Illuminate\Http\RedirectResponse
    {

        Ticket::where('id', $request->ticket_ids)->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_progress',
        ]);

        return back()->with('success', 'Teknisi berhasil ditugaskan');
    }

    public function bulkStatus(Request $request): \Illuminate\Http\RedirectResponse
    {
        Ticket::where('id', $request->ticket_ids)->update([
            'assigned_to' => $request->assigned_to,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status tiket berhasil diperbarui');
    }
}
