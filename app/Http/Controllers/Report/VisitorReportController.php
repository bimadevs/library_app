<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitorReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Base Query
        $query = Visitor::query()
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate);

        // 1. Total Visitors
        $totalVisitors = $query->count();

        // 2. Average Visitors per Day
        $daysCount = max(1, \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1);
        $avgVisitors = round($totalVisitors / $daysCount, 1);

        // 3. Daily Trend (Line Chart)
        $dailyTrend = Visitor::select(DB::raw('DATE(date) as date'), DB::raw('count(*) as total'))
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 4. Visitors by Class (Bar Chart)
        $visitorsByClass = Visitor::join('students', 'visitors.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->select('classes.name as class_name', DB::raw('count(*) as total'))
            ->whereDate('visitors.date', '>=', $startDate)
            ->whereDate('visitors.date', '<=', $endDate)
            ->groupBy('classes.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // 5. Visitors by Major (Donut Chart)
        $visitorsByMajor = Visitor::join('students', 'visitors.student_id', '=', 'students.id')
            ->join('majors', 'students.major_id', '=', 'majors.id')
            ->select('majors.name as major_name', DB::raw('count(*) as total'))
            ->whereDate('visitors.date', '>=', $startDate)
            ->whereDate('visitors.date', '<=', $endDate)
            ->groupBy('majors.name')
            ->get();

        // 6. Recent List (Table)
        $recentVisitors = Visitor::with(['student.class', 'student.major'])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->latest()
            ->paginate(10);

        return view('reports.visitors.index', compact(
            'startDate', 'endDate', 
            'totalVisitors', 'avgVisitors', 
            'dailyTrend', 'visitorsByClass', 'visitorsByMajor',
            'recentVisitors'
        ));
    }
}
