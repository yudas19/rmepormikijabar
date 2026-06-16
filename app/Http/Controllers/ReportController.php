<?php

namespace App\Http\Controllers;

use App\Models\FaskesProfile;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the Executive Reports Dashboard with date range filtering.
     */
    public function index(Request $request): View
    {
        // 1. Authorize action
        if (! auth()->user()->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        // 2. Fetch filter dates
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $startDateTime = $startDate.' 00:00:00';
        $endDateTime = $endDate.' 23:59:59';

        // 3. Financial Aggregate Queries
        $totalRevenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDateTime, $endDateTime])
            ->sum('grand_total');

        $totalCash = Invoice::where('status', 'paid')
            ->where('payment_method', 'tunai')
            ->whereBetween('paid_at', [$startDateTime, $endDateTime])
            ->sum('grand_total');

        $totalNonCash = Invoice::where('status', 'paid')
            ->whereIn('payment_method', ['qris', 'transfer'])
            ->whereBetween('paid_at', [$startDateTime, $endDateTime])
            ->sum('grand_total');

        $paymentBreakdown = Invoice::select(
            'payment_method',
            DB::raw('count(*) as transaction_count'),
            DB::raw('sum(grand_total) as total_amount')
        )
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDateTime, $endDateTime])
            ->groupBy('payment_method')
            ->get();

        // 4. Disease Trend Queries (Top 10 Diagnoses from medical_record_icd10)
        $topDiseases = DB::table('medical_record_icd10')
            ->select('icd10_code', 'icd10_name', DB::raw('count(*) as cases_count'))
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->groupBy('icd10_code', 'icd10_name')
            ->orderByDesc('cases_count')
            ->limit(10)
            ->get();

        // 5. Fetch Faskes Profile details
        $profile = FaskesProfile::find(1) ?? new FaskesProfile;

        return view('admin.laporan-eksekutif', compact(
            'startDate',
            'endDate',
            'totalRevenue',
            'totalCash',
            'totalNonCash',
            'paymentBreakdown',
            'topDiseases',
            'profile'
        ));
    }
}
