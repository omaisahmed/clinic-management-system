<?php

declare(strict_types=1);

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Modules\Audit\Facades\Audit;
use Modules\Patients\Enums\BloodGroup;
use Modules\Patients\Enums\Gender;
use Modules\Patients\Enums\MaritalStatus;
use Modules\Patients\Http\Requests\StorePatientRequest;
use Modules\Patients\Http\Requests\UpdatePatientRequest;
use Modules\Patients\Models\Patient;
use Modules\Patients\Models\PatientContact;
use Modules\Patients\Services\PatientService;

class PatientController extends Controller
{
    public function __construct(private readonly PatientService $patients)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Patient::class);

        $patients = Patient::query()
            ->search($request->query('q'))
            ->when($request->query('blood_group'), fn ($q, $bg) => $q->where('blood_group', $bg))
            ->when($request->query('city'), fn ($q, $city) => $q->where('city', 'like', "%{$city}%"))
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('patients::index', [
            'patients' => $patients,
            'bloodGroups' => BloodGroup::choices(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Patient::class);

        return view('patients::create', [
            'genders' => Gender::choices(),
            'bloodGroups' => BloodGroup::choices(),
            'maritalStatuses' => MaritalStatus::choices(),
        ]);
    }

    public function store(StorePatientRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('patients/photos', 'public');
        }

        unset($data['photo']);

        $patient = $this->patients->create($data);

        Audit::record('Patient Created', 'patients', $patient, [
            'patient_number' => $patient->patient_number,
            'name' => $patient->full_name,
        ]);

        return redirect()
            ->route('patients.show', $patient)
            ->with('toast', [['type' => 'success', 'message' => "Patient {$patient->full_name} registered."]]);
    }

    public function show(Request $request, Patient $patient): View
    {
        Gate::authorize('view', $patient);

        $patient->load([
            'contacts',
            'allergies',
            'conditions',
            'surgeries',
            'familyHistories',
            'socialHistory',
        ]);

        $timeline = $this->timeline($patient);

        return view('patients::show', [
            'patient' => $patient,
            'timeline' => $timeline,
            'bloodGroups' => BloodGroup::choices(),
            'tabRecords' => $this->tabRecords($patient),
        ]);
    }

    /**
     * Load per-tab record lists for the patient profile (guarded by table existence).
     *
     * @return array<string, \Illuminate\Support\Collection<int, mixed>>
     */
    private function tabRecords(Patient $patient): array
    {
        return [
            'appointments' => Schema::hasTable('appointments')
                ? \Modules\Appointments\Models\Appointment::query()
                    ->where('patient_id', $patient->id)
                    ->orderByDesc('appointment_date')
                    ->limit(10)
                    ->get()
                : collect(),
            'visits' => Schema::hasTable('visits')
                ? \Modules\Visits\Models\Visit::query()
                    ->where('patient_id', $patient->id)
                    ->orderByDesc('visit_date')
                    ->limit(10)
                    ->get()
                : collect(),
            'prescriptions' => Schema::hasTable('prescriptions')
                ? \Modules\Prescriptions\Models\Prescription::query()
                    ->with('items')
                    ->where('patient_id', $patient->id)
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get()
                : collect(),
            'lab_tests' => Schema::hasTable('lab_tests')
                ? \Modules\LabTests\Models\LabTest::query()
                    ->where('patient_id', $patient->id)
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get()
                : collect(),
            'documents' => Schema::hasTable('documents')
                ? \Modules\Documents\Models\Document::query()
                    ->where('patient_id', $patient->id)
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get()
                : collect(),
            'billing' => Schema::hasTable('invoices')
                ? \Modules\Billing\Models\Invoice::query()
                    ->where('patient_id', $patient->id)
                    ->orderByDesc('issue_date')
                    ->limit(10)
                    ->get()
                : collect(),
        ];
    }

    public function edit(Patient $patient): View
    {
        Gate::authorize('update', $patient);

        return view('patients::edit', [
            'patient' => $patient,
            'genders' => Gender::choices(),
            'bloodGroups' => BloodGroup::choices(),
            'maritalStatuses' => MaritalStatus::choices(),
        ]);
    }

    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('patients/photos', 'public');
        }

        unset($data['photo']);

        $this->patients->update($patient, $data);

        Audit::record('Patient Updated', 'patients', $patient, [
            'patient_number' => $patient->patient_number,
        ]);

        return redirect()
            ->route('patients.show', $patient)
            ->with('toast', [['type' => 'success', 'message' => 'Patient updated.']]);
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        Gate::authorize('delete', $patient);

        $patient->delete();

        Audit::record('Patient Deleted', 'patients', $patient, [
            'patient_number' => $patient->patient_number,
        ]);

        return redirect()
            ->route('patients.index')
            ->with('toast', [['type' => 'success', 'message' => 'Patient removed.']]);
    }

    public function storeContact(Request $request, Patient $patient): RedirectResponse
    {
        Gate::authorize('update', $patient);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'relationship' => ['nullable', 'string', 'max:80'],
            'phone' => ['nullable', 'string', 'max:40'],
            'is_primary' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $data['is_primary'] = (bool) ($data['is_primary'] ?? false);

        if ($data['is_primary']) {
            $patient->contacts()->update(['is_primary' => false]);
        }

        $contact = $patient->contacts()->create($data);

        Audit::record('Contact Added', 'patients', $patient, [
            'patient_number' => $patient->patient_number,
            'contact' => $contact->name,
        ]);

        return redirect()
            ->route('patients.show', $patient)
            ->with('toast', [['type' => 'success', 'message' => 'Contact added.']]);
    }

    public function destroyContact(Patient $patient, PatientContact $contact): RedirectResponse
    {
        Gate::authorize('update', $patient);

        $contact->delete();

        Audit::record('Contact Removed', 'patients', $patient, [
            'patient_number' => $patient->patient_number,
            'contact' => $contact->name,
        ]);

        return redirect()
            ->route('patients.show', $patient)
            ->with('toast', [['type' => 'success', 'message' => 'Contact removed.']]);
    }

    /**
     * Build a chronological patient timeline from available modules.
     *
     * @return array<int, array{date: string, type: string, title: string, description: string}>
     */
    private function timeline(Patient $patient): array
    {
        $events = [];

        $events[] = [
            'date' => $patient->created_at->toDateTimeString(),
            'type' => 'registration',
            'title' => 'Patient Registered',
            'description' => "Registered with number {$patient->patient_number}.",
        ];

        if (Schema::hasTable('visits')) {
            $visits = DB::table('visits')
                ->where('patient_id', $patient->id)
                ->get(['id', 'visit_date', 'chief_complaint']);

            foreach ($visits as $visit) {
                $events[] = [
                    'date' => Carbon::parse($visit->visit_date)->toDateTimeString(),
                    'type' => 'visit',
                    'title' => 'Consultation',
                    'description' => $visit->chief_complaint ?: 'Visit',
                ];
            }
        }

        usort($events, fn ($a, $b) => strcmp($a['date'], $b['date']));

        return array_reverse($events);
    }
}
