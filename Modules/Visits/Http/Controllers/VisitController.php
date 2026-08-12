<?php

declare(strict_types=1);

namespace Modules\Visits\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Modules\Audit\Facades\Audit;
use Modules\Authentication\Enums\Role;
use Modules\Authentication\Models\User;
use Modules\Patients\Models\Patient;
use Modules\Visits\Enums\VisitStatus;
use Modules\Visits\Models\Visit;
use Modules\Visits\Services\VisitService;

class VisitController extends Controller
{
    public function __construct(private readonly VisitService $visits)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('visits.view');

        $visits = Visit::query()
            ->search($request->query('q'))
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->when(
                $request->query('date_from') && $request->query('date_to'),
                fn ($q) => $q->whereBetween('visit_date', [$request->query('date_from'), $request->query('date_to')])
            )
            ->orderByDesc('visit_date')
            ->paginate(15)
            ->withQueryString();

        return view('visits::index', [
            'visits' => $visits,
            'statuses' => VisitStatus::choices(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('visits.create');

        return view('visits::create', [
            'patients' => $this->patientOptions(),
            'doctors' => $this->doctorOptions(),
            'appointments' => $this->appointmentOptions(),
            'statuses' => VisitStatus::choices(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['nullable', 'exists:users,id'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'visit_date' => ['required', 'date'],
            'status' => ['sometimes', 'in:' . implode(',', array_keys(VisitStatus::choices()))],
            'chief_complaint' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'temperature' => ['nullable', 'numeric'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'heart_rate' => ['nullable', 'integer', 'min:20', 'max:300'],
            'respiratory_rate' => ['nullable', 'integer', 'min:4', 'max:100'],
            'weight' => ['nullable', 'numeric'],
            'height' => ['nullable', 'numeric'],
        ]);

        $validated['status'] = $validated['status'] ?? VisitStatus::InProgress->value;

        $visit = $this->visits->create($validated);

        Audit::record('Visit Created', 'visits', $visit, [
            'patient_id' => $visit->patient_id,
        ]);

        return redirect()
            ->route('visits.show', $visit)
            ->with('toast', [['type' => 'success', 'message' => "Visit {$visit->visit_number} created."]]);
    }

    public function show(Visit $visit): View
    {
        Gate::authorize('visits.view');

        $visit->load('patient', 'doctor');

        return view('visits::show', [
            'visit' => $visit,
            'statuses' => VisitStatus::choices(),
        ]);
    }

    public function edit(Visit $visit): View
    {
        Gate::authorize('visits.update');

        return view('visits::edit', [
            'visit' => $visit,
            'patients' => $this->patientOptions(),
            'doctors' => $this->doctorOptions(),
            'appointments' => $this->appointmentOptions(),
            'statuses' => VisitStatus::choices(),
        ]);
    }

    public function update(Request $request, Visit $visit): RedirectResponse
    {
        Gate::authorize('visits.update');

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['nullable', 'exists:users,id'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'visit_date' => ['required', 'date'],
            'status' => ['required', 'in:' . implode(',', array_keys(VisitStatus::choices()))],
            'chief_complaint' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'temperature' => ['nullable', 'numeric'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'heart_rate' => ['nullable', 'integer', 'min:20', 'max:300'],
            'respiratory_rate' => ['nullable', 'integer', 'min:4', 'max:100'],
            'weight' => ['nullable', 'numeric'],
            'height' => ['nullable', 'numeric'],
        ]);

        $this->visits->update($visit, $validated);

        Audit::record('Visit Updated', 'visits', $visit, [
            'patient_id' => $visit->patient_id,
        ]);

        return redirect()
            ->route('visits.show', $visit)
            ->with('toast', [['type' => 'success', 'message' => "Visit {$visit->visit_number} updated."]]);
    }

    public function complete(Visit $visit): RedirectResponse
    {
        Gate::authorize('visits.complete');

        $visit->update(['status' => VisitStatus::Completed->value]);

        Audit::record('Visit Completed', 'visits', $visit, [
            'patient_id' => $visit->patient_id,
        ]);

        return redirect()
            ->back()
            ->with('toast', [['type' => 'success', 'message' => "Visit {$visit->visit_number} marked as completed."]]);
    }

    public function destroy(Visit $visit): RedirectResponse
    {
        Gate::authorize('visits.delete');

        $visit->delete();

        Audit::record('Visit Deleted', 'visits', $visit, [
            'patient_id' => $visit->patient_id,
        ]);

        return redirect()
            ->route('visits.index')
            ->with('toast', [['type' => 'success', 'message' => "Visit {$visit->visit_number} deleted."]]);
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
                $patient->id => trim("{$patient->first_name} {$patient->last_name}") . ' (' . $patient->patient_number . ')',
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
    private function appointmentOptions(): array
    {
        if (! Schema::hasTable('appointments')) {
            return [];
        }

        return DB::table('appointments')
            ->whereDate('appointment_date', '>=', today())
            ->whereNotIn('status', ['completed', 'cancelled', 'no_show'])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get()
            ->mapWithKeys(fn ($row): array => [
                $row->id => "{$row->appointment_date} · " . ($row->start_time ?? ''),
            ])
            ->all();
    }
}
