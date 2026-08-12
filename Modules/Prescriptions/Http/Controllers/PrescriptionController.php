<?php

declare(strict_types=1);

namespace Modules\Prescriptions\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Audit\Facades\Audit;
use Modules\Authentication\Enums\Role;
use Modules\Authentication\Models\User;
use Modules\Medicines\Models\Medicine;
use Modules\Patients\Models\Patient;
use Modules\Prescriptions\Enums\PrescriptionStatus;
use Modules\Prescriptions\Models\Prescription;
use Modules\Prescriptions\Services\PrescriptionService;
use Modules\Visits\Models\Visit;

class PrescriptionController extends Controller
{
    public function __construct(private readonly PrescriptionService $prescriptions)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('prescriptions.view');

        $prescriptions = Prescription::query()
            ->search($request->query('q'))
            ->forStatus($request->query('status'))
            ->with('patient', 'doctor')
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('prescriptions::index', [
            'prescriptions' => $prescriptions,
            'statuses' => PrescriptionStatus::choices(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('prescriptions.create');

        return view('prescriptions::create', [
            'patients' => $this->patientOptions(),
            'doctors' => $this->doctorOptions(),
            'visits' => $this->visitOptions(),
            'medicines' => $this->medicineOptions(),
            'statuses' => PrescriptionStatus::choices(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['nullable', 'exists:users,id'],
            'visit_id' => ['nullable', 'exists:visits,id'],
            'status' => ['sometimes', 'in:' . implode(',', array_keys(PrescriptionStatus::choices()))],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:150'],
            'items.*.medicine_id' => ['nullable', 'exists:medicines,id'],
            'items.*.dosage' => ['nullable', 'string', 'max:100'],
            'items.*.frequency' => ['nullable', 'string', 'max:100'],
            'items.*.duration' => ['nullable', 'string', 'max:100'],
            'items.*.instructions' => ['nullable', 'string'],
        ]);

        $prescription = $this->prescriptions->createWithItems($validated, $validated['items']);

        Audit::record('Prescription Created', 'prescriptions', $prescription, [
            'patient_id' => $prescription->patient_id,
        ]);

        return redirect()
            ->route('prescriptions.show', $prescription)
            ->with('toast', [['type' => 'success', 'message' => "Prescription {$prescription->prescription_number} created."]]);
    }

    public function show(Prescription $prescription): View
    {
        Gate::authorize('prescriptions.view');

        $prescription->load('patient', 'doctor', 'items.medicine');

        return view('prescriptions::show', [
            'prescription' => $prescription,
        ]);
    }

    public function edit(Prescription $prescription): View
    {
        Gate::authorize('prescriptions.update');

        $prescription->load('items');

        return view('prescriptions::edit', [
            'prescription' => $prescription,
            'patients' => $this->patientOptions(),
            'doctors' => $this->doctorOptions(),
            'visits' => $this->visitOptions(),
            'medicines' => $this->medicineOptions(),
            'statuses' => PrescriptionStatus::choices(),
        ]);
    }

    public function update(Request $request, Prescription $prescription): RedirectResponse
    {
        Gate::authorize('prescriptions.update');

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['nullable', 'exists:users,id'],
            'visit_id' => ['nullable', 'exists:visits,id'],
            'status' => ['sometimes', 'in:' . implode(',', array_keys(PrescriptionStatus::choices()))],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:150'],
            'items.*.medicine_id' => ['nullable', 'exists:medicines,id'],
            'items.*.dosage' => ['nullable', 'string', 'max:100'],
            'items.*.frequency' => ['nullable', 'string', 'max:100'],
            'items.*.duration' => ['nullable', 'string', 'max:100'],
            'items.*.instructions' => ['nullable', 'string'],
        ]);

        $this->prescriptions->updateWithItems($prescription, $validated, $validated['items']);

        Audit::record('Prescription Updated', 'prescriptions', $prescription, [
            'patient_id' => $prescription->patient_id,
        ]);

        return redirect()
            ->route('prescriptions.show', $prescription)
            ->with('toast', [['type' => 'success', 'message' => "Prescription {$prescription->prescription_number} updated."]]);
    }

    public function print(Prescription $prescription): View
    {
        Gate::authorize('prescriptions.print');

        $prescription->load('patient', 'doctor', 'items');

        return view('prescriptions::print', [
            'prescription' => $prescription,
        ]);
    }

    public function destroy(Prescription $prescription): RedirectResponse
    {
        Gate::authorize('prescriptions.delete');

        $prescription->delete();

        Audit::record('Prescription Deleted', 'prescriptions', $prescription, [
            'patient_id' => $prescription->patient_id,
        ]);

        return redirect()
            ->route('prescriptions.index')
            ->with('toast', [['type' => 'success', 'message' => 'Prescription deleted.']]);
    }

    /**
     * @return array<string, string>
     */
    private function patientOptions(): array
    {
        return Patient::query()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->mapWithKeys(fn (Patient $patient): array => [
                $patient->id => $patient->full_name,
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function doctorOptions(): array
    {
        return User::query()
            ->role(Role::Doctor->value)
            ->active()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function visitOptions(): array
    {
        return Visit::query()
            ->with('patient')
            ->orderByDesc('visit_date')
            ->get()
            ->mapWithKeys(function (Visit $visit): array {
                $label = $visit->visit_number;

                if ($visit->visit_date !== null) {
                    $label .= ' · ' . $visit->visit_date->format('M d, Y');
                }

                if ($visit->patient !== null) {
                    $label .= ' · ' . $visit->patient->full_name;
                }

                return [$visit->id => $label];
            })
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function medicineOptions(): array
    {
        return Medicine::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
