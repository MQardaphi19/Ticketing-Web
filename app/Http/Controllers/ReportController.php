<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $totalTickets = Ticket::count();
        $resolvedTickets = Ticket::whereNotNull('resolved_at')->count();
        $openTickets = Ticket::whereIn('status', ['open', 'in_progress'])->count();

        $resolvedWithSLA = Ticket::whereNotNull('resolved_at')
            ->whereColumn('resolved_at', '<=', 'sla_due_date')
            ->count();
        $totalResolved = Ticket::whereNotNull('resolved_at')->count();
        $slaCompliance = $totalResolved > 0 ? round(($resolvedWithSLA / $totalResolved) * 100) : 0;

        $resolutionTimes = Ticket::whereNotNull('resolved_at')
            ->whereNotNull('sla_due_date')
            ->get()
            ->map(fn ($ticket) => $ticket->resolved_at->diffInHours($ticket->created_at));
        $avgResolutionTime = $resolutionTimes->isNotEmpty() ? round($resolutionTimes->avg(), 1) : 0;

        $monthlyStats = Ticket::select(
            DB::raw('DATE_FORMAT(created_at, "%b") as month'),
            DB::raw('COUNT(*) as created'),
            DB::raw('SUM(CASE WHEN resolved_at IS NOT NULL THEN 1 ELSE 0 END) as resolved'),
            DB::raw('SUM(CASE WHEN resolved_at IS NOT NULL AND resolved_at > sla_due_date THEN 1 ELSE 0 END) as late')
        )
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%b")'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();

        $categoryStats = Category::withCount(['tickets', 'tickets as resolved_count' => function ($query) {
            $query->whereNotNull('resolved_at');
        }])
            ->get()
            ->map(function ($category) {
                $resolvedWithSLA = $category->tickets()
                    ->whereNotNull('resolved_at')
                    ->whereColumn('resolved_at', '<=', 'sla_due_date')
                    ->count();
                $totalResolved = $category->tickets()
                    ->whereNotNull('resolved_at')
                    ->count();

                return (object) [
                    'name' => $category->name,
                    'tickets' => $category->tickets_count,
                    'resolved' => $category->resolved_count,
                    'sla' => $totalResolved > 0 ? round(($resolvedWithSLA / $totalResolved) * 100) : 0,
                ];
            });

        $technicianStats = User::where('role', 'teknisi')
            ->withCount(['assignedTickets', 'assignedTickets as resolved_count' => function ($query) {
                $query->whereNotNull('resolved_at');
            }])
            ->get()
            ->map(function ($technician) {
                $avgTime = $technician->assignedTickets()
                    ->whereNotNull('resolved_at')
                    ->get()
                    ->map(fn ($ticket) => $ticket->resolved_at->diffInHours($ticket->created_at));

                return (object) [
                    'name' => $technician->name,
                    'assigned' => $technician->assigned_tickets_count,
                    'resolved' => $technician->resolved_count,
                    'avg_time' => $avgTime->isNotEmpty() ? round($avgTime->avg(), 1) : 0,
                ];
            });

        $priorityStats = ['high', 'medium', 'low'];
        $priorityStats = collect($priorityStats)->map(function ($priority) {
            $tickets = Ticket::where('priority', $priority);
            $resolved = $tickets->clone()->whereNotNull('resolved_at')->count();
            $late = $tickets->clone()
                ->whereNotNull('resolved_at')
                ->whereColumn('resolved_at', '>', 'sla_due_date')
                ->count();

            return [
                'priority' => ucfirst($priority),
                'count' => $tickets->count(),
                'resolved' => $resolved,
                'late' => $late,
            ];
        });

        return view('reports.index', compact(
            'totalTickets',
            'resolvedTickets',
            'openTickets',
            'slaCompliance',
            'avgResolutionTime',
            'monthlyStats',
            'categoryStats',
            'technicianStats',
            'priorityStats'
        ));
    }
}