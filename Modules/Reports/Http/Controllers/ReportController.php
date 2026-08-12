<?php

declare(strict_types=1);

namespace Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('reports.view');

        $from = $request->filled('date_from')
            ? (string) $request->input('date_from')
            : today()->subDays(29)->toDateString();

        $to = $request->filled('date_to')
            ? (string) $request->input('date_to')
            : today()->toDateString();

        $stats = [
            'totalPatients' => $this->count('patients'),
            'newPatients' => $this->countBetween('patients', 'created_at', $from, $to),
            'visits' => $this->countBetween('visits', 'visit_date', $from, $to),
            'appointmentsUpcoming' => $this->count('appointments', function ($query) {
                return $query->whereDate('appointment_date', '>=', today())
                    ->where('status', '!=', 'cancelled');
            }),
            'appointmentsTotal' => $this->count('appointments'),
            'invoicesIssued' => $this->count('invoices'),
            'invoicesPaid' => $this->sum('invoices', 'total', function ($query) {
                return $query->where('status', 'paid');
            }),
            'outstanding' => $this->sum('invoices', 'due_amount', function ($query) {
                return $query->where('status', '!=', 'paid')->where('status', '!=', 'cancelled');
            }),
            'revenue' => $this->sumBetween('payments', 'amount', 'payment_date', $from, $to),
            'expenses' => $this->sumBetween('expenses', 'amount', 'expense_date', $from, $to),
        ];

        $stats['net'] = $stats['revenue'] - $stats['expenses'];

        return view('reports::index', [
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function revenue(): View
    {
        Gate::authorize('reports.view');

        $start = today()->startOfMonth()->subMonths(11);

        $labels = [];
        $revenue = [];
        $expenses = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i)->format('Y-m');
            $labels[] = $month;
            $revenue[$month] = 0.0;
            $expenses[$month] = 0.0;
        }

        if (Schema::hasTable('payments')) {
            DB::table('payments')
                ->select('payment_date', 'amount')
                ->where('payment_date', '>=', $start->toDateString())
                ->get()
                ->each(function ($row) use (&$revenue): void {
                    $month = (string) \Illuminate\Support\Carbon::parse($row->payment_date)->format('Y-m');
                    $revenue[$month] = (float) ($revenue[$month] ?? 0) + (float) $row->amount;
                });
        }

        if (Schema::hasTable('expenses')) {
            DB::table('expenses')
                ->select('expense_date', 'amount')
                ->where('expense_date', '>=', $start->toDateString())
                ->get()
                ->each(function ($row) use (&$expenses): void {
                    $month = (string) \Illuminate\Support\Carbon::parse($row->expense_date)->format('Y-m');
                    $expenses[$month] = (float) ($expenses[$month] ?? 0) + (float) $row->amount;
                });
        }

        $totalRevenue = $this->sum('payments', 'amount');
        $totalExpenses = $this->sum('expenses', 'amount');

        return view('reports::revenue', [
            'months' => $labels,
            'revenue' => array_values($revenue),
            'expenses' => array_values($expenses),
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'net' => $totalRevenue - $totalExpenses,
            'hasRevenueData' => Schema::hasTable('payments') || Schema::hasTable('expenses'),
        ]);
    }

    public function patientsReport(): View
    {
        Gate::authorize('reports.view');

        $topPatients = [];

        if (Schema::hasTable('invoices') && Schema::hasTable('patients')) {
            $topPatients = DB::table('invoices')
                ->join('patients', 'patients.id', '=', 'invoices.patient_id')
                ->select(
                    'patients.id',
                    'patients.first_name',
                    'patients.last_name',
                    DB::raw('COALESCE(SUM(invoices.total), 0) as total_billed'),
                )
                ->whereNotNull('invoices.patient_id')
                ->groupBy('patients.id', 'patients.first_name', 'patients.last_name')
                ->orderByDesc('total_billed')
                ->limit(10)
                ->get()
                ->map(function ($row) {
                    $row->name = trim("{$row->first_name} {$row->last_name}");
                    unset($row->first_name, $row->last_name);

                    return $row;
                })
                ->all();
        }

        $totalPatients = $this->count('patients');

        $genderCounts = [];
        if (Schema::hasTable('patients')) {
            $genderCounts = DB::table('patients')
                ->select('gender', DB::raw('count(*) as total'))
                ->groupBy('gender')
                ->pluck('total', 'gender')
                ->all();
        }

        return view('reports::patients', [
            'totalPatients' => $totalPatients,
            'malePatients' => (int) ($genderCounts['male'] ?? 0),
            'femalePatients' => (int) ($genderCounts['female'] ?? 0),
            'topPatients' => $topPatients,
        ]);
    }

    public function inventory(): View
    {
        Gate::authorize('reports.view');

        $totalMedicines = 0;
        $totalStock = 0;
        $lowStockCount = 0;
        $lowStock = [];

        if (Schema::hasTable('medicines')) {
            $totalMedicines = (int) DB::table('medicines')->count();
            $totalStock = (int) DB::table('medicines')->sum('stock');
            $lowStockCount = (int) DB::table('medicines')
                ->whereColumn('stock', '<=', 'reorder_level')
                ->count();
            $lowStock = DB::table('medicines')
                ->whereColumn('stock', '<=', 'reorder_level')
                ->orderBy('stock')
                ->limit(20)
                ->get(['name', 'stock', 'reorder_level'])
                ->all();
        }

        return view('reports::inventory', [
            'totalMedicines' => $totalMedicines,
            'lowStockCount' => $lowStockCount,
            'totalStock' => $totalStock,
            'lowStock' => $lowStock,
        ]);
    }

    private function count(string $table, ?callable $where = null): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        $query = DB::table($table);

        if ($where) {
            $where($query);
        }

        return (int) $query->count();
    }

    private function countBetween(string $table, string $column, string $from, string $to): int
    {
        return $this->count($table, function ($query) use ($column, $from, $to) {
            return $query->whereBetween($column, [$from, $to]);
        });
    }

    private function sum(string $table, string $column, ?callable $where = null): float
    {
        if (! Schema::hasTable($table)) {
            return 0.0;
        }

        $query = DB::table($table);

        if ($where) {
            $where($query);
        }

        return (float) $query->sum($column);
    }

    private function sumBetween(string $table, string $column, string $dateColumn, string $from, string $to): float
    {
        return $this->sum($table, $column, function ($query) use ($dateColumn, $from, $to) {
            return $query->whereBetween($dateColumn, [$from, $to]);
        });
    }
}
