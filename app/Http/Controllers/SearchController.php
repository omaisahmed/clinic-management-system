<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Modules\Appointments\Models\Appointment;
use Modules\Billing\Models\Invoice;
use Modules\Documents\Models\Document;
use Modules\Expenses\Models\Expense;
use Modules\LabTests\Models\LabTest;
use Modules\Medicines\Models\Medicine;
use Modules\Patients\Models\Patient;
use Modules\Payments\Models\Payment;
use Modules\Prescriptions\Models\Prescription;
use Modules\Visits\Models\Visit;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $term = trim((string) $request->query('q', ''));

        $results = $term !== '' ? $this->search($term) : [];

        return view('search.index', [
            'term' => $term,
            'subtitle' => $term !== '' ? 'Results for "'.$term.'"' : 'Search across the clinic',
            'emptyMessage' => 'No results found for "'.$term.'".',
            'results' => $results,
            'total' => collect($results)->sum(fn (array $group): int => $group['items']->count()),
        ]);
    }

    /**
     * @return array<int, array{label: string, icon: string, href: string, items: Collection<int, array{title: string, subtitle: string, href: string}>}>
     */
    private function search(string $term): array
    {
        $groups = [];

        if (Schema::hasTable('patients') && Gate::allows('patients.view')) {
            $items = Patient::query()
                ->search($term)
                ->limit(8)
                ->get()
                ->map(fn (Patient $patient): array => [
                    'title' => $patient->full_name,
                    'subtitle' => collect([$patient->patient_number, $patient->phone])->filter()->implode(' · '),
                    'href' => route('patients.show', $patient),
                ]);

            if ($items->isNotEmpty()) {
                $groups[] = $this->group('Patients', 'users', 'patients.index', $items, $term);
            }
        }

        if (Schema::hasTable('appointments') && Gate::allows('appointments.view')) {
            $items = Appointment::query()
                ->with('patient')
                ->search($term)
                ->limit(8)
                ->get()
                ->map(fn (Appointment $appointment): array => [
                    'title' => $appointment->patient?->full_name ?? 'Appointment',
                    'subtitle' => collect([
                        $appointment->appointment_date?->format('M d, Y'),
                        $appointment->start_time,
                    ])->filter()->implode(' · '),
                    'href' => route('appointments.show', $appointment),
                ]);

            if ($items->isNotEmpty()) {
                $groups[] = $this->group('Appointments', 'calendar', 'appointments.index', $items, $term);
            }
        }

        if (Schema::hasTable('visits') && Gate::allows('visits.view')) {
            $items = Visit::query()
                ->with('patient')
                ->search($term)
                ->limit(8)
                ->get()
                ->map(fn (Visit $visit): array => [
                    'title' => $visit->visit_number,
                    'subtitle' => collect([
                        $visit->patient?->full_name,
                        $visit->visit_date?->format('M d, Y'),
                    ])->filter()->implode(' · '),
                    'href' => route('visits.show', $visit),
                ]);

            if ($items->isNotEmpty()) {
                $groups[] = $this->group('Visits', 'stethoscope', 'visits.index', $items, $term);
            }
        }

        if (Schema::hasTable('medicines') && Gate::allows('medicines.view')) {
            $items = Medicine::query()
                ->search($term)
                ->limit(8)
                ->get()
                ->map(fn (Medicine $medicine): array => [
                    'title' => $medicine->name,
                    'subtitle' => collect([$medicine->generic_name, $medicine->brand, $medicine->category])->filter()->implode(' · '),
                    'href' => route('medicines.show', $medicine),
                ]);

            if ($items->isNotEmpty()) {
                $groups[] = $this->group('Medicines', 'capsule', 'medicines.index', $items, $term);
            }
        }

        if (Schema::hasTable('prescriptions') && Gate::allows('prescriptions.view')) {
            $items = Prescription::query()
                ->with('patient')
                ->search($term)
                ->limit(8)
                ->get()
                ->map(fn (Prescription $prescription): array => [
                    'title' => $prescription->prescription_number,
                    'subtitle' => collect([
                        $prescription->patient?->full_name,
                        $prescription->created_at?->format('M d, Y'),
                    ])->filter()->implode(' · '),
                    'href' => route('prescriptions.show', $prescription),
                ]);

            if ($items->isNotEmpty()) {
                $groups[] = $this->group('Prescriptions', 'clipboard-list', 'prescriptions.index', $items, $term);
            }
        }

        if (Schema::hasTable('lab_tests') && Gate::allows('lab_tests.view')) {
            $items = LabTest::query()
                ->with('patient')
                ->search($term)
                ->limit(8)
                ->get()
                ->map(fn (LabTest $test): array => [
                    'title' => $test->test_name,
                    'subtitle' => collect([$test->patient?->full_name, $test->category])->filter()->implode(' · '),
                    'href' => route('lab_tests.show', $test),
                ]);

            if ($items->isNotEmpty()) {
                $groups[] = $this->group('Lab Tests', 'beaker', 'lab_tests.index', $items, $term);
            }
        }

        if (Schema::hasTable('documents') && Gate::allows('documents.view')) {
            $items = Document::query()
                ->with('patient')
                ->search($term)
                ->limit(8)
                ->get()
                ->map(fn (Document $document): array => [
                    'title' => $document->title,
                    'subtitle' => collect([$document->patient?->full_name, $document->category])->filter()->implode(' · '),
                    'href' => route('documents.show', $document),
                ]);

            if ($items->isNotEmpty()) {
                $groups[] = $this->group('Documents', 'document', 'documents.index', $items, $term);
            }
        }

        if (Schema::hasTable('invoices') && Gate::allows('billing.view')) {
            $items = Invoice::query()
                ->with('patient')
                ->search($term)
                ->limit(8)
                ->get()
                ->map(fn (Invoice $invoice): array => [
                    'title' => $invoice->invoice_number,
                    'subtitle' => collect([
                        $invoice->patient?->full_name,
                        $invoice->total !== null ? money($invoice->total) : null,
                    ])->filter()->implode(' · '),
                    'href' => route('billing.show', $invoice),
                ]);

            if ($items->isNotEmpty()) {
                $groups[] = $this->group('Invoices', 'receipt', 'billing.index', $items, $term);
            }
        }

        if (Schema::hasTable('payments') && Gate::allows('payments.view')) {
            $items = Payment::query()
                ->with('patient')
                ->search($term)
                ->limit(8)
                ->get()
                ->map(fn (Payment $payment): array => [
                    'title' => $payment->patient?->full_name ?? ($payment->reference ?: 'Payment'),
                    'subtitle' => collect([
                        $payment->reference,
                        $payment->amount !== null ? money($payment->amount) : null,
                    ])->filter()->implode(' · '),
                    'href' => route('payments.show', $payment),
                ]);

            if ($items->isNotEmpty()) {
                $groups[] = $this->group('Payments', 'banknotes', 'payments.index', $items, $term);
            }
        }

        if (Schema::hasTable('expenses') && Gate::allows('expenses.view')) {
            $items = Expense::query()
                ->search($term)
                ->limit(8)
                ->get()
                ->map(fn (Expense $expense): array => [
                    'title' => $expense->description,
                    'subtitle' => collect([
                        money($expense->amount),
                        $expense->expense_date?->format('M d, Y'),
                    ])->filter()->implode(' · '),
                    'href' => route('expenses.show', $expense),
                ]);

            if ($items->isNotEmpty()) {
                $groups[] = $this->group('Expenses', 'wallet', 'expenses.index', $items, $term);
            }
        }

        return $groups;
    }

    /**
     * @param  Collection<int, array{title: string, subtitle: string, href: string}>  $items
     * @return array{label: string, icon: string, href: string, items: Collection<int, array{title: string, subtitle: string, href: string}>}
     */
    private function group(string $label, string $icon, string $indexRoute, Collection $items, string $term): array
    {
        return [
            'label' => $label,
            'icon' => $icon,
            'href' => route($indexRoute, ['q' => $term]),
            'items' => $items,
        ];
    }
}
