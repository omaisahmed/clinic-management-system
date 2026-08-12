@php
    $tab = request('tab', 'overview');
    $tabs = [
        'overview' => ['label' => 'Overview', 'icon' => 'user'],
        'medical' => ['label' => 'Medical History', 'icon' => 'heart'],
        'timeline' => ['label' => 'Timeline', 'icon' => 'clock'],
        'appointments' => ['label' => 'Appointments', 'icon' => 'calendar'],
        'visits' => ['label' => 'Visits', 'icon' => 'document'],
        'prescriptions' => ['label' => 'Prescriptions', 'icon' => 'capsule'],
        'lab-tests' => ['label' => 'Lab Tests', 'icon' => 'beaker'],
        'documents' => ['label' => 'Documents', 'icon' => 'folder'],
        'billing' => ['label' => 'Billing', 'icon' => 'banknotes'],
    ];
    $availableTabs = array_keys($tabs);
    if (! in_array($tab, $availableTabs, true)) {
        $tab = 'overview';
    }
@endphp

<x-app-layout>
    <x-page-header :title="$patient->full_name" :subtitle="$patient->patient_number . ' · ' . $patient->clinic?->name">
        <x-slot name="actions">
            @can('patients.update')
                <x-button :href="route('patients.edit', $patient)" variant="secondary" icon="pencil">Edit</x-button>
            @endcan
            @if (Route::has('appointments.create'))
                <x-button :href="route('appointments.create', ['patient' => $patient->id])" icon="calendar">New Appointment</x-button>
            @endif
        </x-slot>
    </x-page-header>

    <x-alerts />

    <!-- Profile header card -->
    <div class="card overflow-hidden">
        <div class="bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-secondary)] px-6 pb-16 pt-6">
            <div class="flex items-center gap-4">
                @if ($patient->photo_url)
                    <img src="{{ $patient->photo_url }}" alt="{{ $patient->full_name }}" class="h-16 w-16 rounded-full border-2 border-white/60 object-cover">
                @else
                    <span class="flex h-16 w-16 items-center justify-center rounded-full border-2 border-white/60 bg-white/20 text-xl font-bold text-white">
                        {{ $patient->initials }}
                    </span>
                @endif
                <div>
                    <p class="text-lg font-semibold text-white">{{ $patient->full_name }}</p>
                    <p class="text-sm text-white/80">
                        {{ $patient->gender ? ucfirst($patient->gender) : 'Gender' }}
                        @if ($patient->age !== null) · {{ $patient->age }} years @endif
                        @if ($patient->blood_group) · <span class="font-semibold">Blood {{ $patient->blood_group }}</span> @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="relative px-6 pb-5">
            <div class="-mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Phone</p>
                    <p class="mt-1 truncate text-sm font-medium text-slate-900 dark:text-white">{{ $patient->phone ?: '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Email</p>
                    <p class="mt-1 truncate text-sm font-medium text-slate-900 dark:text-white">{{ $patient->email ?: '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Location</p>
                    <p class="mt-1 truncate text-sm font-medium text-slate-900 dark:text-white">{{ trim($patient->city . ', ' . $patient->country, ', ') ?: '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Registered</p>
                    <p class="mt-1 truncate text-sm font-medium text-slate-900 dark:text-white">{{ $patient->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mt-6 flex flex-wrap gap-1 border-b border-slate-200 dark:border-slate-700">
        @foreach ($tabs as $key => $meta)
            <a href="{{ route('patients.show', [$patient, 'tab' => $key]) }}"
               class="flex items-center gap-2 border-b-2 px-4 py-2.5 text-sm font-medium transition
                      {{ $tab === $key
                            ? 'border-[var(--color-primary)] text-[var(--color-primary)]'
                            : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
                <x-icon :name="$meta['icon']" class="h-4 w-4" />
                {{ $meta['label'] }}
            </a>
        @endforeach
    </div>

    <div class="mt-6">
        @if ($tab === 'overview')
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="card p-6">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Personal Information</h3>
                        <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                            <x-detail label="Patient Number">{{ $patient->patient_number }}</x-detail>
                            <x-detail label="Gender">{{ $patient->gender ? ucfirst($patient->gender) : '—' }}</x-detail>
                            <x-detail label="Date of Birth">{{ $patient->date_of_birth?->format('M d, Y') ?: '—' }}</x-detail>
                            <x-detail label="Age">{{ $patient->age !== null ? $patient->age . ' years' : '—' }}</x-detail>
                            <x-detail label="Blood Group">{{ $patient->blood_group ?: '—' }}</x-detail>
                            <x-detail label="Marital Status">{{ $patient->marital_status ? ucfirst($patient->marital_status) : '—' }}</x-detail>
                            <x-detail label="Occupation">{{ $patient->occupation ?: '—' }}</x-detail>
                            <x-detail label="CNIC / ID">{{ $patient->cnic ?: '—' }}</x-detail>
                        </dl>
                    </div>

                    <div class="card p-6">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Address</h3>
                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">{{ $patient->address ?: 'No address on file.' }}</p>
                    </div>

                    @if ($patient->notes)
                        <div class="card p-6">
                            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Notes</h3>
                            <p class="mt-3 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $patient->notes }}</p>
                        </div>
                    @endif

                    <div class="card p-6">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Contacts</h3>

                        @if ($patient->contacts->isNotEmpty())
                            <div class="mt-4 space-y-3">
                                @foreach ($patient->contacts as $contact)
                                    <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 px-4 py-3 dark:border-slate-700">
                                        <div>
                                            <p class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $contact->name }}
                                                @if ($contact->is_primary)
                                                    <x-badge variant="primary" class="ml-1">Primary</x-badge>
                                                @endif
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $contact->relationship ?: 'Contact' }}</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $contact->phone ?: '—' }}</p>
                                            @can('patients.update')
                                                <form method="POST" action="{{ route('patients.contacts.destroy', [$patient, $contact]) }}"
                                                      x-data x-on:submit.prevent="$dispatch('confirm-submit')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-button type="submit" variant="ghost" size="sm" icon="trash" class="text-red-600">Remove</x-button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-4 text-sm text-slate-400">No contacts on file.</p>
                        @endif

                        @can('patients.update')
                            <form method="POST" action="{{ route('patients.contacts.store', $patient) }}" class="mt-5 rounded-lg border border-dashed border-slate-300 p-4 dark:border-slate-600">
                                @csrf
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Add Contact</p>
                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <x-label for="contact_name">Name</x-label>
                                        <x-input name="name" id="contact_name" placeholder="Contact name" />
                                    </div>
                                    <div>
                                        <x-label for="contact_phone">Phone</x-label>
                                        <x-input name="phone" id="contact_phone" placeholder="+1 555 0100" />
                                    </div>
                                    <div>
                                        <x-label for="contact_relationship">Relationship</x-label>
                                        <x-input name="relationship" id="contact_relationship" placeholder="Spouse, parent..." />
                                    </div>
                                    <div class="flex items-end pb-1">
                                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200">
                                            <input type="checkbox" name="is_primary" value="1" class="rounded border-slate-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)] dark:border-slate-600 dark:bg-slate-800">
                                            Primary contact
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <x-button type="submit" size="sm" icon="plus">Add Contact</x-button>
                                </div>
                            </form>
                        @endcan
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="card p-6">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Medical Summary</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">Allergies</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @forelse ($patient->allergies as $allergy)
                                        <x-badge variant="red">{{ $allergy->allergy }}</x-badge>
                                    @empty
                                        <span class="text-sm text-slate-400">None recorded</span>
                                    @endforelse
                                </div>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">Active Conditions</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @forelse ($patient->conditions->where('status', 'active') as $condition)
                                        <x-badge variant="amber">{{ $condition->condition }}</x-badge>
                                    @empty
                                        <span class="text-sm text-slate-400">None active</span>
                                    @endforelse
                                </div>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">Emergency Contact</p>
                                <div class="mt-2">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $patient->emergency_contact ?: '—' }}</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $patient->emergency_contact_phone ?: '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-6">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Recent Activity</h3>
                        <div class="mt-4 space-y-3">
                            @forelse (array_slice($timeline, 0, 4) as $event)
                                <div class="flex items-center gap-3">
                                    <span class="h-2 w-2 shrink-0 rounded-full {{ $event['type'] === 'registration' ? 'bg-[var(--color-primary)]' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm text-slate-800 dark:text-slate-200">{{ $event['title'] }}</p>
                                        <p class="text-xs text-slate-400">{{ $event['description'] }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400">No activity yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        @elseif ($tab === 'medical')
            @include('medical-records::partials.medical-history')

        @elseif ($tab === 'timeline')
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Patient Timeline</h3>
                <ol class="mt-6 space-y-0">
                    @foreach ($timeline as $index => $event)
                        <li class="relative flex gap-4 pb-8 last:pb-0">
                            @if (! $loop->last)
                                <span class="absolute left-[11px] top-7 h-full w-px bg-slate-200 dark:bg-slate-700"></span>
                            @endif
                            <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full
                                {{ $event['type'] === 'registration'
                                    ? 'bg-[var(--color-primary)] text-white'
                                    : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-300' }}">
                                <x-icon :name="$event['type'] === 'registration' ? 'user' : 'document'" class="h-3.5 w-3.5" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $event['title'] }}</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $event['description'] }}</p>
                                <p class="mt-0.5 text-xs text-slate-400">{{ \Carbon\Carbon::parse($event['date'])->format('M d, Y · h:i A') }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

        @elseif ($tab === 'appointments')
            <div class="card p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Appointments</h3>
                    @if (Route::has('appointments.create'))
                        <x-button :href="route('appointments.create', ['patient' => $patient->id])" size="sm" icon="calendar">New Appointment</x-button>
                    @endif
                </div>
                @if ($tabRecords['appointments']->isNotEmpty())
                    <x-table :headers="['Date', 'Type', 'Status', 'Reason', '']" class="mt-4">
                        @foreach ($tabRecords['appointments'] as $appointment)
                            <tr>
                                <td class="whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $appointment->appointment_date?->format('M d, Y') ?: '—' }}</td>
                                <td class="text-sm text-slate-600 dark:text-slate-300">{{ $appointment->appointment_type?->label() ?? '—' }}</td>
                                <td>
                                    @if ($appointment->status)
                                        <x-badge :variant="$appointment->status->color()">{{ $appointment->status->label() }}</x-badge>
                                    @endif
                                </td>
                                <td class="text-sm text-slate-600 dark:text-slate-300">{{ $appointment->reason ?: '—' }}</td>
                                <td class="text-right">
                                    @if (Route::has('appointments.show'))
                                        <x-button :href="route('appointments.show', $appointment)" variant="ghost" size="sm" icon="arrow-right">View</x-button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @else
                    <p class="mt-4 text-sm text-slate-400">No appointments recorded.</p>
                @endif
            </div>

        @elseif ($tab === 'visits')
            <div class="card p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Visits</h3>
                    @if (Route::has('visits.create'))
                        <x-button :href="route('visits.create', ['patient' => $patient->id])" size="sm" icon="plus">New Visit</x-button>
                    @endif
                </div>
                @if ($tabRecords['visits']->isNotEmpty())
                    <x-table :headers="['Visit #', 'Date', 'Chief Complaint', '']" class="mt-4">
                        @foreach ($tabRecords['visits'] as $visit)
                            <tr>
                                <td class="whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">{{ $visit->visit_number ?: '—' }}</td>
                                <td class="whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $visit->visit_date?->format('M d, Y') ?: '—' }}</td>
                                <td class="text-sm text-slate-600 dark:text-slate-300">{{ $visit->chief_complaint ?: '—' }}</td>
                                <td class="text-right">
                                    @if (Route::has('visits.show'))
                                        <x-button :href="route('visits.show', $visit)" variant="ghost" size="sm" icon="arrow-right">View</x-button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @else
                    <p class="mt-4 text-sm text-slate-400">No visits recorded.</p>
                @endif
            </div>

        @elseif ($tab === 'prescriptions')
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Prescriptions</h3>
                @if ($tabRecords['prescriptions']->isNotEmpty())
                    <x-table :headers="['Number', 'Date', 'Items', 'Status', '']" class="mt-4">
                        @foreach ($tabRecords['prescriptions'] as $prescription)
                            <tr>
                                <td class="whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">{{ $prescription->prescription_number ?: '—' }}</td>
                                <td class="whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $prescription->created_at?->format('M d, Y') ?: '—' }}</td>
                                <td class="text-sm text-slate-600 dark:text-slate-300">{{ $prescription->items->count() }} item(s)</td>
                                <td>
                                    @if ($prescription->status)
                                        <x-badge :variant="$prescription->status->color()">{{ $prescription->status->label() }}</x-badge>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if (Route::has('prescriptions.show'))
                                        <x-button :href="route('prescriptions.show', $prescription)" variant="ghost" size="sm" icon="arrow-right">View</x-button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @else
                    <p class="mt-4 text-sm text-slate-400">No prescriptions recorded.</p>
                @endif
            </div>

        @elseif ($tab === 'lab-tests')
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Lab Tests</h3>
                @if ($tabRecords['lab_tests']->isNotEmpty())
                    <x-table :headers="['Test', 'Category', 'Status', 'Result']" class="mt-4">
                        @foreach ($tabRecords['lab_tests'] as $labTest)
                            <tr>
                                <td class="text-sm font-medium text-slate-900 dark:text-white">{{ $labTest->test_name }}</td>
                                <td class="text-sm text-slate-600 dark:text-slate-300">{{ $labTest->category ?: '—' }}</td>
                                <td>
                                    @if ($labTest->status)
                                        <x-badge :variant="$labTest->status->color()">{{ $labTest->status->label() }}</x-badge>
                                    @endif
                                </td>
                                <td class="max-w-xs truncate text-sm text-slate-600 dark:text-slate-300">{{ $labTest->result ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </x-table>
                @else
                    <p class="mt-4 text-sm text-slate-400">No lab tests recorded.</p>
                @endif
            </div>

        @elseif ($tab === 'documents')
            <div class="card p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Documents</h3>
                    @can('documents.create')
                        @if (Route::has('documents.create'))
                            <x-button :href="route('documents.create', ['patient' => $patient->id])" size="sm" icon="folder">Upload Document</x-button>
                        @endif
                    @endcan
                </div>
                @if ($tabRecords['documents']->isNotEmpty())
                    <x-table :headers="['Title', 'Category', 'Size', 'Uploaded', '']" class="mt-4">
                        @foreach ($tabRecords['documents'] as $document)
                            <tr>
                                <td class="text-sm font-medium text-slate-900 dark:text-white">{{ $document->title }}</td>
                                <td class="text-sm text-slate-600 dark:text-slate-300">{{ $document->category ?: '—' }}</td>
                                <td class="whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $document->file_size_label }}</td>
                                <td class="whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $document->created_at?->format('M d, Y') ?: '—' }}</td>
                                <td class="text-right">
                                    @can('documents.download')
                                        @if (Route::has('documents.download'))
                                            <x-button :href="route('documents.download', $document)" variant="ghost" size="sm" icon="download">Download</x-button>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @else
                    <p class="mt-4 text-sm text-slate-400">No documents uploaded.</p>
                @endif
            </div>

        @elseif ($tab === 'billing')
            <div class="card p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Billing</h3>
                    @can('billing.create')
                        @if (Route::has('billing.create'))
                            <x-button :href="route('billing.create', ['patient' => $patient->id])" size="sm" icon="receipt">New Invoice</x-button>
                        @endif
                    @endcan
                </div>
                @if ($tabRecords['billing']->isNotEmpty())
                    <x-table :headers="['Invoice', 'Date', 'Total', 'Paid', 'Due', 'Status', '']" class="mt-4">
                        @foreach ($tabRecords['billing'] as $invoice)
                            <tr>
                                <td class="whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">{{ $invoice->invoice_number }}</td>
                                <td class="whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $invoice->issue_date?->format('M d, Y') ?: '—' }}</td>
                                <td class="whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ money($invoice->total) }}</td>
                                <td class="whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ money($invoice->paid_amount) }}</td>
                                <td class="whitespace-nowrap text-sm font-medium text-red-600">{{ money($invoice->due_amount) }}</td>
                                <td>
                                    @if ($invoice->status)
                                        <x-badge :variant="$invoice->status->color()">{{ $invoice->status->label() }}</x-badge>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if (Route::has('billing.show'))
                                        <x-button :href="route('billing.show', $invoice)" variant="ghost" size="sm" icon="arrow-right">View</x-button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @else
                    <p class="mt-4 text-sm text-slate-400">No invoices for this patient.</p>
                @endif
            </div>

        @else
            <x-empty-state :icon="$tabs[$tab]['icon']" :message="$tabs[$tab]['label'] . ' will appear here once that module is built.'" />
        @endif
    </div>
</x-app-layout>
