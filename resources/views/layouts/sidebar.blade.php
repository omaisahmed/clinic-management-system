@php
    $nav = [
        [
            'group' => 'Main',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home', 'permission' => 'dashboard.view'],
            ],
        ],
        [
            'group' => 'Clinical',
            'items' => [
                ['label' => 'Appointments', 'route' => 'appointments.index', 'icon' => 'calendar', 'permission' => 'appointments.view'],
                ['label' => 'Queue', 'route' => 'queue.index', 'icon' => 'clock', 'permission' => 'queue.view'],
                ['label' => 'Patients', 'route' => 'patients.index', 'icon' => 'users', 'permission' => 'patients.view'],
                ['label' => 'Visits', 'route' => 'visits.index', 'icon' => 'stethoscope', 'permission' => 'visits.view'],
                ['label' => 'Prescriptions', 'route' => 'prescriptions.index', 'icon' => 'document-text', 'permission' => 'prescriptions.view'],
                ['label' => 'Medicines', 'route' => 'medicines.index', 'icon' => 'capsule', 'permission' => 'medicines.view'],
                ['label' => 'Lab Tests', 'route' => 'lab_tests.index', 'icon' => 'beaker', 'permission' => 'lab_tests.view'],
                ['label' => 'Documents', 'route' => 'documents.index', 'icon' => 'folder', 'permission' => 'documents.view'],
            ],
        ],
        [
            'group' => 'Administration',
            'items' => [
                ['label' => 'Billing', 'route' => 'billing.index', 'icon' => 'receipt', 'permission' => 'billing.view'],
                ['label' => 'Payments', 'route' => 'payments.index', 'icon' => 'banknotes', 'permission' => 'payments.view'],
                ['label' => 'Expenses', 'route' => 'expenses.index', 'icon' => 'wallet', 'permission' => 'expenses.view'],
            ],
        ],
        [
            'group' => 'Management',
            'items' => [
                ['label' => 'Reports', 'route' => 'reports.index', 'icon' => 'chart-bar', 'permission' => 'reports.view'],
                ['label' => 'Staff', 'route' => 'authentication.users.index', 'icon' => 'user-group', 'permission' => 'staff.view'],
                ['label' => 'Audit Logs', 'route' => 'audit.logs.index', 'icon' => 'shield-check', 'permission' => 'audit_logs.view'],
                ['label' => 'Settings', 'route' => 'settings.index', 'icon' => 'cog', 'permission' => 'settings.view'],
            ],
        ],
    ];
@endphp

<!-- Desktop sidebar -->
<aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col border-r border-slate-800 bg-slate-900 lg:flex">
    <x-sidebar-content :nav="$nav" />
</aside>

<!-- Mobile drawer -->
<div x-cloak
     x-show="open"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
     x-on:click="close()"></div>

<div x-cloak
     x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 lg:hidden">
    <x-sidebar-content :nav="$nav" />
</div>
