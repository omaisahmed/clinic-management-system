<?php

declare(strict_types=1);

namespace Modules\MedicalRecords\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Audit\Facades\Audit;
use Modules\MedicalRecords\Enums\AllergySeverity;
use Modules\MedicalRecords\Enums\ConditionStatus;
use Modules\MedicalRecords\Services\MedicalHistoryService;
use Modules\Patients\Models\Patient;

class PatientMedicalHistoryController extends Controller
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private const TYPES = [
        'allergies' => ['field' => 'allergy', 'permission' => 'medical_records.create'],
        'conditions' => ['field' => 'condition', 'permission' => 'medical_records.create'],
        'surgeries' => ['field' => 'surgery', 'permission' => 'medical_records.create'],
        'family' => ['field' => 'condition', 'permission' => 'medical_records.create'],
        'social' => ['field' => null, 'permission' => 'medical_records.create'],
    ];

    public function __construct(private readonly MedicalHistoryService $history)
    {
    }

    public function store(Request $request, Patient $patient, string $type): RedirectResponse
    {
        Gate::authorize('update', $patient);
        Gate::authorize('medical_records.create');

        $config = self::TYPES[$type] ?? abort(404);

        $data = $this->validate($request, $type, $config);

        $this->history->create($patient, $type, $data);

        Audit::record(ucfirst($type) . ' Record Created', 'medical_records', $patient, [
            'type' => $type,
        ]);

        return redirect()
            ->route('patients.show', ['patient' => $patient, 'tab' => 'medical'])
            ->with('toast', [['type' => 'success', 'message' => 'Medical record added.']]);
    }

    public function destroy(Patient $patient, string $type, mixed $id): RedirectResponse
    {
        Gate::authorize('update', $patient);
        Gate::authorize('medical_records.delete');

        $this->history->delete($type, $id);

        Audit::record(ucfirst($type) . ' Record Deleted', 'medical_records', $patient, [
            'type' => $type,
        ]);

        return redirect()
            ->route('patients.show', ['patient' => $patient, 'tab' => 'medical'])
            ->with('toast', [['type' => 'success', 'message' => 'Medical record removed.']]);
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private function validate(Request $request, string $type, array $config): array
    {
        $rules = match ($type) {
            'allergies' => [
                'allergy' => ['required', 'string', 'max:150'],
                'reaction' => ['nullable', 'string', 'max:255'],
                'severity' => ['nullable', 'in:' . implode(',', array_keys(AllergySeverity::choices()))],
                'notes' => ['nullable', 'string'],
            ],
            'conditions' => [
                'condition' => ['required', 'string', 'max:150'],
                'diagnosis_date' => ['nullable', 'date', 'before_or_equal:today'],
                'status' => ['nullable', 'in:' . implode(',', array_keys(ConditionStatus::choices()))],
                'notes' => ['nullable', 'string'],
            ],
            'surgeries' => [
                'surgery' => ['required', 'string', 'max:150'],
                'date' => ['nullable', 'date'],
                'notes' => ['nullable', 'string'],
            ],
            'family' => [
                'condition' => ['required', 'string', 'max:150'],
                'relation' => ['nullable', 'string', 'max:80'],
                'notes' => ['nullable', 'string'],
            ],
            'social' => [
                'smoking' => ['nullable', 'boolean'],
                'alcohol' => ['nullable', 'boolean'],
                'occupation' => ['nullable', 'string', 'max:255'],
                'notes' => ['nullable', 'string'],
            ],
            default => abort(404),
        };

        return $request->validate($rules);
    }
}
