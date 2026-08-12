<?php

declare(strict_types=1);

namespace Modules\LabTests\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Modules\Audit\Facades\Audit;
use Modules\LabTests\Enums\LabTestStatus;
use Modules\LabTests\Models\LabTest;
use Modules\Patients\Models\Patient;
use Modules\Visits\Models\Visit;

class LabTestController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('lab_tests.view');

        $tests = LabTest::query()
            ->search($request->query('q'))
            ->forStatus($request->query('status'))
            ->when($request->query('category'), fn ($q, $category) => $q->where('category', $category))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('lab_tests::index', [
            'tests' => $tests,
            'statuses' => LabTestStatus::choices(),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('lab_tests.create');

        return view('lab_tests::create', [
            'patients' => $this->patientOptions(),
            'visits' => $this->visitOptions(),
            'statuses' => LabTestStatus::choices(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'visit_id' => ['nullable', 'exists:visits,id'],
            'test_name' => ['required', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:80'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:' . implode(',', array_keys(LabTestStatus::choices()))],
            'sample_type' => ['nullable', 'string', 'max:80'],
            'collection_date' => ['nullable', 'date'],
            'result' => ['nullable', 'string'],
            'result_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['status'] = $validated['status'] ?? LabTestStatus::Requested->value;
        $validated['clinic_id'] = current_clinic()?->id;

        $test = LabTest::create($validated);

        Audit::record('Lab Test Created', 'lab_tests', $test, [
            'patient_id' => $test->patient_id,
        ]);

        return redirect()
            ->route('lab_tests.show', $test)
            ->with('toast', [['type' => 'success', 'message' => 'Lab test created.']]);
    }

    public function show(LabTest $test): View
    {
        Gate::authorize('lab_tests.view');

        $test->load('patient', 'visit');

        return view('lab_tests::show', [
            'test' => $test,
        ]);
    }

    public function edit(LabTest $test): View
    {
        Gate::authorize('lab_tests.update');

        return view('lab_tests::edit', [
            'test' => $test,
            'patients' => $this->patientOptions(),
            'visits' => $this->visitOptions(),
            'statuses' => LabTestStatus::choices(),
        ]);
    }

    public function update(Request $request, LabTest $test): RedirectResponse
    {
        Gate::authorize('lab_tests.update');

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'visit_id' => ['nullable', 'exists:visits,id'],
            'test_name' => ['required', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:80'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:' . implode(',', array_keys(LabTestStatus::choices()))],
            'sample_type' => ['nullable', 'string', 'max:80'],
            'collection_date' => ['nullable', 'date'],
            'result' => ['nullable', 'string'],
            'result_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $test->update($validated);

        Audit::record('Lab Test Updated', 'lab_tests', $test, [
            'patient_id' => $test->patient_id,
        ]);

        return redirect()
            ->route('lab_tests.show', $test)
            ->with('toast', [['type' => 'success', 'message' => 'Lab test updated.']]);
    }

    public function result(LabTest $test): View
    {
        Gate::authorize('lab_tests.manage_results');

        $test->load('patient');

        return view('lab_tests::result', [
            'test' => $test,
            'statuses' => LabTestStatus::choices(),
        ]);
    }

    public function updateResult(Request $request, LabTest $test): RedirectResponse
    {
        Gate::authorize('lab_tests.manage_results');

        $validated = $request->validate([
            'result' => ['required', 'string'],
            'status' => ['required', 'in:' . implode(',', array_keys(LabTestStatus::choices()))],
            'result_date' => ['nullable', 'date'],
        ]);

        $test->update($validated);

        Audit::record('Lab Result Recorded', 'lab_tests', $test, [
            'patient_id' => $test->patient_id,
        ]);

        return redirect()
            ->route('lab_tests.show', $test)
            ->with('toast', [['type' => 'success', 'message' => 'Lab result recorded.']]);
    }

    public function destroy(LabTest $test): RedirectResponse
    {
        Gate::authorize('lab_tests.delete');

        $test->delete();

        Audit::record('Lab Test Deleted', 'lab_tests', $test, [
            'patient_id' => $test->patient_id,
        ]);

        return redirect()
            ->route('lab_tests.index')
            ->with('toast', [['type' => 'success', 'message' => 'Lab test removed.']]);
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
     * @return array<string, string>|Illuminate\Support\Collection<int, mixed>
     */
    private function visitOptions(): array|object
    {
        if (! Schema::hasTable('visits')) {
            return collect();
        }

        return Visit::query()
            ->orderByDesc('visit_date')
            ->limit(100)
            ->get()
            ->mapWithKeys(fn (Visit $visit): array => [
                $visit->id => ($visit->visit_date?->format('M d, Y') ?? '') . ' · ' . $visit->visit_number,
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function categoryOptions(): array
    {
        return LabTest::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->filter()
            ->mapWithKeys(fn (string $category): array => [$category => $category])
            ->all();
    }
}
