<?php

declare(strict_types=1);

namespace Modules\Queue\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Appointments\Models\Appointment;
use Modules\Audit\Facades\Audit;
use Modules\Authentication\Models\User;
use Modules\Patients\Models\Patient;
use Modules\Queue\Enums\QueueStatus;
use Modules\Queue\Models\QueueEntry;
use Modules\Queue\Services\QueueService;

class QueueEntryController extends Controller
{
    public function __construct(private readonly QueueService $queue)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('queue.view');

        $date = $request->query('date', today()->toDateString()) ?: today()->toDateString();

        $entries = QueueEntry::query()
            ->with(['patient', 'appointment'])
            ->forDate($date)
            ->when($request->query('status'), fn ($q, $status) => $q->forStatus($status))
            ->orderBy('token_number')
            ->get();

        $todayEntries = QueueEntry::query()->forDate(today()->toDateString())->get();

        return view('queue::index', [
            'entries' => $entries,
            'statuses' => QueueStatus::choices(),
            'date' => $date,
            'patients' => Patient::query()
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get()
                ->mapWithKeys(fn (Patient $patient): array => [$patient->id => $patient->full_name]),
            'doctors' => User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', 'doctor'))
                ->orderBy('name')
                ->get()
                ->mapWithKeys(fn (User $user): array => [$user->id => $user->name]),
            'appointments' => Appointment::query()
                ->whereDate('appointment_date', today())
                ->with('patient')
                ->orderBy('start_time')
                ->get()
                ->mapWithKeys(fn (Appointment $appointment): array => [
                    $appointment->id => ($appointment->patient?->full_name ?? 'Patient') . ' · ' . Carbon::parse($appointment->start_time)->format('h:i A'),
                ]),
            'waitingCount' => $todayEntries->where('status', QueueStatus::Waiting)->count(),
            'inProgressCount' => $todayEntries->where('status', QueueStatus::InProgress)->count(),
            'completedCount' => $todayEntries->where('status', QueueStatus::Completed)->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('queue.create');

        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['nullable', 'exists:users,id'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $alreadyQueued = QueueEntry::query()
            ->forDate(today()->toDateString())
            ->where('patient_id', $data['patient_id'])
            ->whereIn('status', [QueueStatus::Waiting->value, QueueStatus::InProgress->value])
            ->exists();

        if ($alreadyQueued) {
            return redirect()->back()->with('toast', [['type' => 'error', 'message' => "Patient already in today's queue."]]);
        }

        $clinicId = current_clinic()?->id;

        $entry = QueueEntry::create([
            'clinic_id' => $clinicId,
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'] ?? null,
            'appointment_id' => $data['appointment_id'] ?? null,
            'token_number' => $this->queue->issueToken($clinicId),
            'status' => QueueStatus::Waiting,
            'entered_at' => now(),
            'notes' => $data['notes'] ?? null,
        ]);

        Audit::record('Queue Entry Created', 'queue', $entry, [
            'patient_id' => $entry->patient_id,
        ]);

        return redirect()
            ->route('queue.index', ['date' => today()->toDateString()])
            ->with('toast', [['type' => 'success', 'message' => "Token #{$entry->token_number} issued."]]);
    }

    public function callNext(): RedirectResponse
    {
        Gate::authorize('queue.update');

        $next = $this->queue->callNext(current_clinic()?->id);

        if ($next === null) {
            return redirect()->back()->with('toast', [['type' => 'error', 'message' => 'Queue is empty.']]);
        }

        Audit::record('Queue Advanced', 'queue', $next, [
            'token_number' => $next->token_number,
        ]);

        return redirect()->back()->with('toast', [['type' => 'success', 'message' => "Calling token #{$next->token_number}."]]);
    }

    public function updateStatus(Request $request, QueueEntry $entry): RedirectResponse
    {
        Gate::authorize('queue.update');

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(QueueStatus::choices()))],
        ]);

        $entry->status = $data['status'];

        if ($entry->status === QueueStatus::Completed) {
            $entry->completed_at = now();
        } elseif ($entry->status === QueueStatus::Waiting) {
            $entry->entered_at = now();
        } elseif ($entry->status === QueueStatus::InProgress) {
            $entry->called_at = now();
        }

        $entry->save();

        Audit::record('Queue Status Updated', 'queue', $entry, [
            'token_number' => $entry->token_number,
            'status' => $data['status'],
        ]);

        return redirect()->back()->with('toast', [['type' => 'success', 'message' => 'Queue status updated.']]);
    }

    public function destroy(QueueEntry $entry): RedirectResponse
    {
        Gate::authorize('queue.delete');

        $entry->delete();

        Audit::record('Queue Entry Deleted', 'queue', $entry, [
            'token_number' => $entry->token_number,
        ]);

        return redirect()->back()->with('toast', [['type' => 'success', 'message' => 'Queue entry removed.']]);
    }
}
