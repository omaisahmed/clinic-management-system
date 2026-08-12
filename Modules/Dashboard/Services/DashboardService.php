<?php

declare(strict_types=1);

namespace Modules\Dashboard\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Aggregates dashboard widgets across modules.
 *
 * Every lookup is guarded by a schema check so the dashboard can render
 * progressively while modules are being added.
 */
class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function overview(): array
    {
        $today = today();
        $startOfMonth = today()->startOfMonth();

        return [
            'todayAppointments' => $this->count('appointments', fn ($q) => $q->whereDate('appointment_date', $today)),
            'waitingPatients' => $this->count('appointments', function ($q) use ($today) {
                return $q->whereDate('appointment_date', $today)->whereIn('status', ['waiting', 'checked_in']);
            }),
            'completedVisits' => $this->count('visits', function ($q) use ($today) {
                return $q->whereDate('visit_date', $today)->where('status', 'completed');
            }),
            'cancelledAppointments' => $this->count('appointments', function ($q) use ($today) {
                return $q->whereDate('appointment_date', $today)->where('status', 'cancelled');
            }),
            'newPatients' => $this->count('patients', fn ($q) => $q->where('created_at', '>=', $startOfMonth)),
            'totalPatients' => $this->count('patients'),
            'todayRevenue' => $this->sum('payments', 'amount', fn ($q) => $q->whereDate('payment_date', $today)),
            'pendingPayments' => $this->sum('invoices', 'due_amount', fn ($q) => $q->where('status', '!=', 'paid')),
        ];
    }

    /**
     * @return array<int, object>
     */
    public function todaySchedule(): array
    {
        if (! Schema::hasTable('appointments')) {
            return [];
        }

        return DB::table('appointments')
            ->whereDate('appointment_date', today())
            ->orderBy('start_time')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                $row->patient = $this->patientName($row->patient_id);
                $row->doctor = $this->doctorName($row->doctor_id);

                return $row;
            })
            ->all();
    }

    /**
     * @return array<int, object>
     */
    public function waitingQueue(): array
    {
        if (! Schema::hasTable('queue_tokens')) {
            return [];
        }

        return DB::table('queue_tokens')
            ->whereDate('created_at', today())
            ->whereIn('status', ['waiting'])
            ->orderBy('token_number')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                $row->patient = $this->patientName($row->patient_id);
                $row->doctor = $this->doctorName($row->doctor_id);

                return $row;
            })
            ->all();
    }

    /**
     * @return array<int, object>
     */
    public function upcomingAppointments(int $limit = 5): array
    {
        if (! Schema::hasTable('appointments')) {
            return [];
        }

        return DB::table('appointments')
            ->whereDate('appointment_date', '>=', today())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $row->patient = $this->patientName($row->patient_id);
                $row->doctor = $this->doctorName($row->doctor_id);

                return $row;
            })
            ->all();
    }

    /**
     * @return array<int, object>
     */
    public function recentPatients(int $limit = 5): array
    {
        if (! Schema::hasTable('patients')) {
            return [];
        }

        return DB::table('patients')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->all();
    }

    /**
     * @return array<int, object>
     */
    public function recentPayments(int $limit = 5): array
    {
        if (! Schema::hasTable('payments')) {
            return [];
        }

        return DB::table('payments')
            ->orderByDesc('payment_date')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $row->patient = $this->patientName($row->patient_id);

                return $row;
            })
            ->all();
    }

    /**
     * @return array<int, array{date: string, revenue: float}>
     */
    public function revenueByDay(int $days = 7): array
    {
        if (! Schema::hasTable('payments')) {
            return [];
        }

        $rows = DB::table('payments')
            ->select(DB::raw('DATE(payment_date) as day'), DB::raw('COALESCE(SUM(amount), 0) as total'))
            ->where('payment_date', '>=', today()->subDays($days - 1))
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = today()->subDays($i)->toDateString();
            $out[] = ['date' => $day, 'revenue' => (float) ($rows[$day] ?? 0)];
        }

        return $out;
    }

    /**
     * @return array<string, int>
     */
    public function appointmentsByStatus(): array
    {
        if (! Schema::hasTable('appointments')) {
            return [];
        }

        return DB::table('appointments')
            ->select('status', DB::raw('count(*) as total'))
            ->whereDate('appointment_date', '>=', today()->startOfMonth())
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
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

    private function patientName(int|string $patientId): string
    {
        if (! Schema::hasTable('patients')) {
            return '';
        }

        $patient = DB::table('patients')->find($patientId);

        return $patient ? trim("{$patient->first_name} {$patient->last_name}") : '';
    }

    private function doctorName(int|string $doctorId): string
    {
        if (! Schema::hasTable('users')) {
            return '';
        }

        $doctor = DB::table('users')->find($doctorId);

        return $doctor ? $doctor->name : '';
    }
}
