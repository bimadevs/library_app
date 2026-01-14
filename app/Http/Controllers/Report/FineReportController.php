<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Exports\FineReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FineReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $paymentStatus = $request->get('payment_status', 'all');
        $fineType = $request->get('fine_type', 'all');

        $query = Fine::with(['student', 'loan.bookCopy.book'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($paymentStatus !== 'all') {
            $query->where('is_paid', $paymentStatus === 'paid');
        }

        if ($fineType !== 'all') {
            $query->where('type', $fineType);
        }

        $fines = $query->orderBy('created_at', 'desc')->get();

        // Calculate summaries
        $summary = [
            'total_fines' => $fines->count(),
            'total_amount' => $fines->sum('amount'),
            'paid_count' => $fines->where('is_paid', true)->count(),
            'paid_amount' => $fines->where('is_paid', true)->sum('amount'),
            'unpaid_count' => $fines->where('is_paid', false)->count(),
            'unpaid_amount' => $fines->where('is_paid', false)->sum('amount'),
            'late_fines_count' => $fines->where('type', 'late')->count(),
            'late_fines_amount' => $fines->where('type', 'late')->sum('amount'),
            'lost_fines_count' => $fines->where('type', 'lost')->count(),
            'lost_fines_amount' => $fines->where('type', 'lost')->sum('amount'),
        ];

        $periodLabel = \Carbon\Carbon::parse($startDate)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d/m/Y');

        return view('reports.fines.index', compact(
            'fines',
            'startDate',
            'endDate',
            'paymentStatus',
            'fineType',
            'periodLabel',
            'summary'
        ));
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $paymentStatus = $request->get('payment_status', 'all');
        $fineType = $request->get('fine_type', 'all');

        $filename = 'laporan-denda-' . $startDate . '-' . $endDate . '.xlsx';

        return Excel::download(
            new FineReportExport($startDate, $endDate, $paymentStatus, $fineType),
            $filename
        );
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $paymentStatus = $request->get('payment_status', 'all');
        $fineType = $request->get('fine_type', 'all');

        $query = Fine::with(['student', 'loan.bookCopy.book'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($paymentStatus !== 'all') {
            $query->where('is_paid', $paymentStatus === 'paid');
        }

        if ($fineType !== 'all') {
            $query->where('type', $fineType);
        }

        $fines = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'total_fines' => $fines->count(),
            'total_amount' => $fines->sum('amount'),
            'paid_count' => $fines->where('is_paid', true)->count(),
            'paid_amount' => $fines->where('is_paid', true)->sum('amount'),
            'unpaid_count' => $fines->where('is_paid', false)->count(),
            'unpaid_amount' => $fines->where('is_paid', false)->sum('amount'),
            'late_fines_count' => $fines->where('type', 'late')->count(),
            'late_fines_amount' => $fines->where('type', 'late')->sum('amount'),
            'lost_fines_count' => $fines->where('type', 'lost')->count(),
            'lost_fines_amount' => $fines->where('type', 'lost')->sum('amount'),
        ];

        $periodLabel = \Carbon\Carbon::parse($startDate)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d/m/Y');

        $pdf = Pdf::loadView('reports.fines.pdf', compact(
            'fines',
            'periodLabel',
            'summary'
        ));

        $filename = 'laporan-denda-' . $startDate . '-' . $endDate . '.pdf';

        return $pdf->download($filename);
    }
}
