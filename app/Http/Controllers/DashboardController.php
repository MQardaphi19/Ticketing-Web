<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ChatbotLog;
use App\Models\KnowledgeBase;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', 'open')->count();
        $resolvedTickets = Ticket::where('status', 'resolved')->orWhere('status', 'closed')->count();

        $highPriorityCount = Ticket::where('priority', 'high')->count();
        $mediumPriorityCount = Ticket::where('priority', 'medium')->count();
        $lowPriorityCount = Ticket::where('priority', 'low')->count();
        $total = $highPriorityCount + $mediumPriorityCount + $lowPriorityCount;
        $highPriorityPercentage = $total > 0 ? round(($highPriorityCount / $total) * 100) : 0;
        $mediumPriorityPercentage = $total > 0 ? round(($mediumPriorityCount / $total) * 100) : 0;
        $lowPriorityPercentage = $total > 0 ? round(($lowPriorityCount / $total) * 100) : 0;

        $resolvedWithSLA = Ticket::whereNotNull('resolved_at')
            ->whereColumn('resolved_at', '<=', 'sla_due_date')
            ->count();
        $totalResolved = Ticket::whereNotNull('resolved_at')->count();
        $slaCompliance = $totalResolved > 0 ? round(($resolvedWithSLA / $totalResolved) * 100) : 0;

        $totalQueries = ChatbotLog::count();
        $correctPredictions = ChatbotLog::where('is_correct', true)->count();
        $chatbotAccuracy = $totalQueries > 0 ? round(($correctPredictions / $totalQueries) * 100) : 0;
        $avgConfidence = $totalQueries > 0 ? round(ChatbotLog::avg('confidence_score')) : 0;
        $modelAccuracy = $chatbotAccuracy;

        $trainingDataCount = KnowledgeBase::count();
        $lastKnowledge = KnowledgeBase::latest()->first();
        $lastTrainedAt = $lastKnowledge ? $lastKnowledge->created_at->format('d M Y, H:i') : '-';

        $recentTickets = Ticket::with(['user', 'category'])
            ->latest()
            ->limit(5)
            ->get();

        $categories = Category::all();
        $categoryData = [];
        $categoryLabels = [];
        foreach ($categories as $category) {
            $categoryData[] = $category->tickets()->count();
            $categoryLabels[] = $category->name;
        }

        $monthlyStats = Ticket::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as created')
            ->whereYear('created_at', now()->year)
            ->groupBy('month', 'year')
            ->orderBy('month')
            ->get();

        $monthlyStatsResolved = Ticket::selectRaw('MONTH(created_at) as month, COUNT(*) as resolved')
            ->whereYear('created_at', now()->year)
            ->whereIn('status', ['resolved', 'closed'])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyStatsLate = Ticket::selectRaw('MONTH(created_at) as month, COUNT(*) as late')
            ->whereYear('created_at', now()->year)
            ->whereNotNull('resolved_at')
            ->whereColumn('resolved_at', '>', 'sla_due_date')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Des'];
        $monthlyCreated = array_fill(0, 12, 0);
        $monthlyResolved = array_fill(0, 12, 0);
        $monthlyLate = array_fill(0, 12, 0);

        foreach ($monthlyStats as $stat) {
            $monthlyCreated[$stat->month - 1] = $stat->created;
        }

        foreach ($monthlyStatsResolved as $stat) {
            $monthlyResolved[$stat->month - 1] = $stat->resolved;
        }

        foreach ($monthlyStatsLate as $stat) {
            $monthlyLate[$stat->month - 1] = $stat->late;
        }

        return view('dashboard', compact(
            'totalTickets',
            'openTickets',
            'resolvedTickets',
            'highPriorityCount',
            'mediumPriorityCount',
            'lowPriorityCount',
            'highPriorityPercentage',
            'mediumPriorityPercentage',
            'lowPriorityPercentage',
            'slaCompliance',
            'chatbotAccuracy',
            'trainingDataCount',
            'lastTrainedAt',
            'totalQueries',
            'correctPredictions',
            'avgConfidence',
            'recentTickets',
            'categoryData',
            'categoryLabels',
            'modelAccuracy',
            'months',
            'monthlyCreated',
            'monthlyResolved',
            'monthlyLate'
        ));
    }
}