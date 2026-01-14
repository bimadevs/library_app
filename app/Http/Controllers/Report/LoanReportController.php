<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Exports\LoanReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LoanReportController extends Controller
{
    public function index(Request $request): View
    {
        $reportType = $request->get('report_type', 'daily');
        $date = $request->get('date', now()->format('Y-m-d'));
        $month = $request->get('month', now()->format('Y-m'));

        $query = Loan::with(['student', 'bookCopy.book', 'fine']);

        if ($reportType === 'daily') {
            $query->whereDate('loan_date', $date);
            $periodLabel = \Carbon\Carbon::parse($date)->translatedFormat('d F Y');
        } else {
            $startOfMonth = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
            $endOfMonth = \Carbon\Carbon::parse($month . '-01')->endOfMonth();
            $query->whereBetween('loan_date', [$startOfMonth, $endOfMonth]);
            $periodLabel = \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y');
        }

        $loans = $query->orderBy('loan_date', 'desc')->get();

        $summary = [
            'total_loans' => $loans->count(),
            'active_loans' => $loans->where('status', 'active')->count(),
            'returned_loans' => $loans->where('status', 'returned')->count(),
            'overdue_loans' => $loans->where('status', 'overdue')->count(),
            'lost_loans' => $loans->where('status', 'lost')->count(),
        ];

        return view('reports.loans.index', compact(
            'loans',
            'reportType',
            'date',
            'month',
            'periodLabel',
            'summary'
        ));
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $reportType = $request->get('report_type', 'daily');
        $date = $request->get('date', now()->format('Y-m-d'));
        $month = $request->get('month', now()->format('Y-m'));

        $filename = $reportType === 'daily'
            ? 'laporan-peminjaman-' . $date . '.xlsx'
            : 'laporan-peminjaman-' . $month . '.xlsx';

        return Excel::download(
            new LoanReportExport($reportType, $date, $month),
            $filename
        );
    }

    public function exportPdf(Request $request)
    {
        $reportType = $request->get('report_type', 'daily');
        $date = $request->get('date', now()->format('Y-m-d'));
        $month = $request->get('month', now()->format('Y-m'));

        $query = Loan::with(['student', 'bookCopy.book', 'fine']);

        if ($reportType === 'daily') {
            $query->whereDate('loan_date', $date);
            $periodLabel = \Carbon\Carbon::parse($date)->translatedFormat('d F Y');
        } else {
            $startOfMonth = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
            $endOfMonth = \Carbon\Carbon::parse($month . '-01')->endOfMonth();
            $query->whereBetween('loan_date', [$startOfMonth, $endOfMonth]);
            $periodLabel = \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y');
        }

        $loans = $query->orderBy('loan_date', 'desc')->get();

        $summary = [
            'total_loans' => $loans->count(),
            'active_loans' => $loans->where('status', 'active')->count(),
            'returned_loans' => $loans->where('status', 'returned')->count(),
            'overdue_loans' => $loans->where('status', 'overdue')->count(),
            'lost_loans' => $loans->where('status', 'lost')->count(),
        ];

        $pdf = Pdf::loadView('reports.loans.pdf', compact(
            'loans',
            'reportType',
            'periodLabel',
            'summary'
        ));

        $filename = $reportType === 'daily'
            ? 'laporan-peminjaman-' . $date . '.pdf'
            : 'laporan-peminjaman-' . $month . '.pdf';

        return $pdf->download($filename);
    }
}
