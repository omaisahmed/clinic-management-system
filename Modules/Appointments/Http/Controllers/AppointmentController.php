<?php

declare(strict_types=1);

namespace Modules\Appointments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Appointments\Enums\AppointmentStatus;
use Modules\Appointments\Enums\AppointmentType;
use Modules\Appointments\Models\Appointment;
use Modules\Audit\Facades\Audit;
use Modules\Authentication\Models\User;
use Modules\Patients\Models\Patient;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('appointments.view');

        $appointments = Appointment::query()
            ->search($request->query('q'))
            ->forStatus($request->query('status'))
            ->forDate($request->query('date'))
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->paginate(15)
            ->withQueryString();

        return view('appointments::index', [
            'appointments' => $appointments,
            'statuses' => AppointmentStatus::choices(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('appointments.create');

        return view('appointments::create', array_merge($this->formData(), [
            'appointment' => new Appointment(),
            'patientId' => request('patient'),
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('appointments.create');

        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['nullable', 'exists:users,id'],
            'appointment_type' => ['required', 'in:' . implode(',', array_keys(AppointmentType::choices()))],
            'status' => ['sometimes', 'in:' . implode(',', array_keys(AppointmentStatus::choices()))],
            'appointment_date' => ['required', 'date'],
            'start_time' => ['required', 'string', 'max:5'],
            'duration' => ['nullable', 'integer', 'min:5', 'max:240'],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['clinic_id'] = current_clinic()?->id;

        $appointment = Appointment::create($data);

        Audit::record('Appointment Created', 'appointments', $appointment, [
            'patient_id' => $appointment->patient_id,
        ]);

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('toast', [['type' => 'success', 'message' => 'Appointment created.']]);
    }

    public function show(Appointment $appointment): View
    {
        Gate::authorize('appointments.view');

        $appointment->load(['patient', 'doctor']);

        return view('appointments::show', [
            'appointment' => $appointment,
        ]);
    }

    public function edit(Appointment $appointment): View
    {
        Gate::authorize('appointments.update');

        return view('appointments::edit', array_merge($this->formData(), [
            'appointment' => $appointment,
            'patientId' => null,
        ]));
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        Gate::authorize('appointments.update');

        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['nullable', 'exists:users,id'],
            'appointment_type' => ['required', 'in:' . implode(',', array_keys(AppointmentType::choices()))],
            'status' => ['required', 'in:' . implode(',', array_keys(AppointmentStatus::choices()))],
            'appointment_date' => ['required', 'date'],
            'start_time' => ['required', 'string', 'max:5'],
            'duration' => ['nullable', 'integer', 'min:5', 'max:240'],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $appointment->update($data);

        Audit::record('Appointment Updated', 'appointments', $appointment, [
            'patient_id' => $appointment->patient_id,
        ]);

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('toast', [['type' => 'success', 'message' => 'Appointment updated.']]);
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        Gate::authorize('appointments.delete');

        $appointment->delete();

        Audit::record('Appointment Deleted', 'appointments', $appointment, [
            'patient_id' => $appointment->patient_id,
        ]);

        return redirect()
            ->route('appointments.index')
            ->with('toast', [['type' => 'success', 'message' => 'Appointment removed.']]);
    }

    /**
     * Shared dropdown data for the create/edit forms.
     *
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'statuses' => AppointmentStatus::choices(),
            'types' => AppointmentType::choices(),
            'doctors' => User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', 'doctor'))
                ->orderBy('name')
                ->get(),
            'patients' => Patient::query()
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get()
                ->mapWithKeys(fn (Patient $patient) => [$patient->id => $patient->full_name]),
        ];
    }
}
