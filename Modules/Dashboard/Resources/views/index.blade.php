<x-app-layout>
    <x-slot name="header">
        <div class="mb-2 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }}
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    {{ now()->format('l, F j, Y') }}
                </p>
            </div>
        </div>
    </x-slot>

<x-alerts />

<!-- Stat cards -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-stat-card label="Today's Appointments" :value="$overview['todayAppointments']" icon="calendar" tone="primary" />
    <x-stat-card label="Waiting Patients" :value="$overview['waitingPatients']" icon="clock" tone="amber" />
    <x-stat-card label="Completed Visits" :value="$overview['completedVisits']" icon="check-circle" tone="green" />
    <x-stat-card label="Cancelled Appointments" :value="$overview['cancelledAppointments']" icon="ban" tone="red" />
    <x-stat-card label="New Patients (Month)" :value="$overview['newPatients']" icon="user-plus" tone="blue" />
    <x-stat-card label="Total Patients" :value="$overview['totalPatients']" icon="users" tone="purple" />
    <x-stat-card label="Today's Revenue" :value="money($overview['todayRevenue'])" icon="banknotes" tone="green" />
    <x-stat-card label="Pending Payments" :value="money($overview['pendingPayments'])" icon="wallet" tone="amber" />
</div>

<!-- Schedule + Queue -->
<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
    <x-card title="Today's Schedule" subtitle="{{ count($todaySchedule) }} appointments"
            :actions="Route::has('appointments.index') ? [['href' => route('appointments.index'), 'label' => 'View all', 'icon' => 'arrow-right']] : []">
        <x-slot name="actions">
            @if (Route::has('appointments.index'))
                <a href="{{ route('appointments.index') }}" class="flex items-center gap-1 text-sm font-medium text-[var(--color-primary)] hover:underline">
                    View all <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            @endif
        </x-slot>

        @forelse ($todaySchedule as $appointment)
            <div class="flex items-center gap-4 py-3 {{ ! $loop->last ? 'border-b border-slate-100 dark:border-slate-700' : '' }}">
                <span class="flex h-10 w-14 shrink-0 flex-col items-center justify-center rounded-lg bg-[var(--color-primary-50)]">
                    <span class="text-sm font-bold text-[var(--color-primary-600)]">
                        {{ \Illuminate\Support\Carbon::parse($appointment->start_time)->format('g:i') }}
                    </span>
                    <span class="text-[10px] uppercase text-slate-400">
                        {{ \Illuminate\Support\Carbon::parse($appointment->start_time)->format('A') }}
                    </span>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate font-semibold text-slate-800 dark:text-slate-100">{{ $appointment->patient }}</p>
                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $appointment->reason ?? '—' }} · Dr. {{ $appointment->doctor }}</p>
                </div>
                <x-badge variant="{{ \Modules\Appointments\Enums\AppointmentStatus::tryFrom($appointment->status)?->color() ?? 'gray' }}">
                    {{ $appointment->status }}
                </x-badge>
            </div>
        @empty
            <x-empty-state message="No appointments scheduled for today." icon="calendar" />
        @endforelse
    </x-card>

    <x-card title="Waiting Queue" subtitle="{{ count($waitingQueue) }} patients waiting">
        @forelse ($waitingQueue as $token)
            <div class="flex items-center gap-4 py-3 {{ ! $loop->last ? 'border-b border-slate-100 dark:border-slate-700' : '' }}">
                <span class="flex h-10 w-14 shrink-0 items-center justify-center rounded-lg bg-amber-100 font-bold text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                    #{{ str_pad((string) $token->token_number, 3, '0', STR_PAD_LEFT) }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate font-semibold text-slate-800 dark:text-slate-100">{{ $token->patient }}</p>
                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">Dr. {{ $token->doctor }}</p>
                </div>
                <span class="text-xs text-slate-400">{{ $token->created_at ?? '' }}</span>
            </div>
        @empty
            <x-empty-state message="Queue is empty." icon="clock" />
        @endforelse
    </x-card>
</div>

<!-- Charts -->
<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
    <x-card title="Revenue (Last 7 Days)">
        @php
            $max = count($revenueByDay) ? max(1, max(array_column($revenueByDay, 'revenue'))) : 1;
        @endphp
        <div class="flex h-56 items-end gap-3 px-2">
            @foreach ($revenueByDay as $day)
                <div class="group flex flex-1 flex-col items-center gap-2">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                        {{ number_format($day['revenue'], 0) }}
                    </span>
                    <div class="flex w-full flex-1 items-end">
                        <div class="w-full rounded-t-lg bg-[var(--color-primary)] transition group-hover:bg-[var(--color-primary-600)]"
                             style="height: {{ max(4, ($day['revenue'] / $max) * 100) }}%"></div>
                    </div>
                    <span class="text-xs text-slate-400">{{ \Illuminate\Support\Carbon::parse($day['date'])->format('D') }}</span>
                </div>
            @endforeach
        </div>
    </x-card>

    <x-card title="Appointments by Status">
        @php
            $total = array_sum($appointmentsByStatus);
            $colors = [
                'scheduled' => 'bg-blue-500',
                'confirmed' => 'bg-[var(--color-primary)]',
                'checked_in' => 'bg-cyan-500',
                'waiting' => 'bg-amber-500',
                'in_consultation' => 'bg-purple-500',
                'completed' => 'bg-green-500',
                'cancelled' => 'bg-red-500',
                'no_show' => 'bg-slate-400',
            ];
        @endphp
        <div class="space-y-4">
            @forelse ($appointmentsByStatus as $status => $count)
                <div>
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="font-medium capitalize text-slate-700 dark:text-slate-200">{{ str_replace('_', ' ', $status) }}</span>
                        <span class="text-slate-500 dark:text-slate-400">{{ $count }} ({{ $total ? round(($count / $total) * 100) : 0 }}%)</span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                        <div class="h-full rounded-full {{ $colors[$status] ?? 'bg-slate-400' }} transition-all"
                             style="width: {{ $total ? ($count / $total) * 100 : 0 }}%"></div>
                    </div>
                </div>
            @empty
                <x-empty-state message="No appointments this month yet." icon="chart-bar" />
            @endforelse
        </div>
    </x-card>
</div>

<!-- Recent activity -->
<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
    <x-card title="Upcoming Appointments"
            :actions="Route::has('appointments.index') ? [['href' => route('appointments.index'), 'label' => 'View all', 'icon' => 'arrow-right']] : []">
        <x-slot name="actions">
            @if (Route::has('appointments.index'))
                <a href="{{ route('appointments.index') }}" class="flex items-center gap-1 text-sm font-medium text-[var(--color-primary)] hover:underline">
                    View all <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            @endif
        </x-slot>
        @forelse ($upcomingAppointments as $appointment)
            <div class="flex items-center gap-4 py-3 {{ ! $loop->last ? 'border-b border-slate-100 dark:border-slate-700' : '' }}">
                <span class="flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-700">
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ \Illuminate\Support\Carbon::parse($appointment->appointment_date)->format('d') }}</span>
                    <span class="text-[10px] uppercase text-slate-400">{{ \Illuminate\Support\Carbon::parse($appointment->appointment_date)->format('M') }}</span>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate font-semibold text-slate-800 dark:text-slate-100">{{ $appointment->patient }}</p>
                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                        {{ \Illuminate\Support\Carbon::parse($appointment->start_time)->format('g:i A') }} · Dr. {{ $appointment->doctor }}
                    </p>
                </div>
            </div>
        @empty
            <x-empty-state message="No upcoming appointments." icon="calendar" />
        @endforelse
    </x-card>

    <x-card title="Recent Payments">
        @forelse ($recentPayments as $payment)
            <div class="flex items-center gap-4 py-3 {{ ! $loop->last ? 'border-b border-slate-100 dark:border-slate-700' : '' }}">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300">
                    <x-icon name="banknotes" class="h-5 w-5" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate font-semibold text-slate-800 dark:text-slate-100">{{ $payment->patient }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $payment->payment_method ?? 'Payment' }} · {{ \Illuminate\Support\Carbon::parse($payment->payment_date)->format('M d, g:i A') }}
                    </p>
                </div>
                <span class="font-semibold text-green-600 dark:text-green-400">{{ money($payment->amount) }}</span>
            </div>
        @empty
            <x-empty-state message="No payments recorded yet." icon="banknotes" />
        @endforelse
    </x-card>
</div>
</x-app-layout>
