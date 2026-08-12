<x-app-layout>
    <x-page-header title="Reports" subtitle="Clinic overview" />

    <x-alerts />

    <div class="card mb-4 p-4">
        @include('reports::partials.date-range', ['from' => $from, 'to' => $to])
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Patients" :value="$stats['totalPatients']" icon="users" tone="purple" />
        <x-stat-card label="New Patients" :value="$stats['newPatients']" icon="user-plus" tone="blue" />
        <x-stat-card label="Visits" :value="$stats['visits']" icon="stethoscope" tone="primary" />
        <x-stat-card label="Appointments" :value="$stats['appointmentsTotal']" icon="calendar" tone="amber" />
        <x-stat-card label="Revenue" :value="money($stats['revenue'])" icon="banknotes" tone="green" />
        <x-stat-card label="Expenses" :value="money($stats['expenses'])" icon="wallet" tone="red" />
        <x-stat-card label="Net Income" :value="money($stats['net'])" icon="calculator" tone="green" />
        <x-stat-card label="Outstanding" :value="money($stats['outstanding'])" icon="credit-card" tone="amber" />
    </div>
</x-app-layout>
