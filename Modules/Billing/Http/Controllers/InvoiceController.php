<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Modules\Audit\Facades\Audit;
use Modules\Billing\Enums\InvoiceStatus;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Services\InvoiceService;
use Modules\Patients\Models\Patient;
use Modules\Visits\Models\Visit;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoices)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('billing.view');

        $invoices = Invoice::query()
            ->search($request->query('q'))
            ->forStatus($request->query('status'))
            ->with('patient')
            ->orderByDesc('issue_date')
            ->paginate(15)
            ->withQueryString();

        $outstanding = (float) Invoice::query()
            ->where('status', '!=', 'paid')
            ->sum('due_amount');

        $totalInvoices = (int) Invoice::query()->count();

        return view('billing::index', [
            'invoices' => $invoices,
            'statuses' => InvoiceStatus::choices(),
            'outstanding' => $outstanding,
            'totalInvoices' => $totalInvoices,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('billing.create');

        return view('billing::create', [
            'patients' => $this->patientOptions(),
            'visits' => $this->visitOptions(),
            'statuses' => InvoiceStatus::choices(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'visit_id' => ['nullable', 'exists:visits,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['sometimes', 'in:' . implode(',', array_keys(InvoiceStatus::choices()))],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $invoice = $this->invoices->createWithItems($data, $request->input('items', []));

        Audit::record('Invoice Created', 'billing', $invoice, [
            'patient_id' => $invoice->patient_id,
        ]);

        return redirect()
            ->route('billing.show', $invoice)
            ->with('toast', [['type' => 'success', 'message' => "Invoice {$invoice->invoice_number} created."]]);
    }

    public function show(Invoice $invoice): View
    {
        Gate::authorize('billing.view');

        $invoice->load('patient', 'items');

        return view('billing::show', [
            'invoice' => $invoice,
        ]);
    }

    public function edit(Invoice $invoice): View
    {
        Gate::authorize('billing.update');

        $invoice->load('items');

        return view('billing::edit', [
            'invoice' => $invoice,
            'patients' => $this->patientOptions(),
            'visits' => $this->visitOptions(),
            'statuses' => InvoiceStatus::choices(),
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        Gate::authorize('billing.update');

        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'visit_id' => ['nullable', 'exists:visits,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:' . implode(',', array_keys(InvoiceStatus::choices()))],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $this->invoices->updateWithItems($invoice, $data, $data['items']);

        Audit::record('Invoice Updated', 'billing', $invoice, [
            'patient_id' => $invoice->patient_id,
        ]);

        return redirect()
            ->route('billing.show', $invoice)
            ->with('toast', [['type' => 'success', 'message' => "Invoice {$invoice->invoice_number} updated."]]);
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        Gate::authorize('billing.delete');

        $invoice->delete();

        Audit::record('Invoice Deleted', 'billing', $invoice, [
            'patient_id' => $invoice->patient_id,
        ]);

        return redirect()
            ->route('billing.index')
            ->with('toast', [['type' => 'success', 'message' => 'Invoice deleted.']]);
    }

    /**
     * @return array<string, string>
     */
    private function patientOptions(): array
    {
        return Patient::query()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name'])
            ->mapWithKeys(fn (Patient $patient): array => [
                $patient->id => $patient->full_name,
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function visitOptions(): array
    {
        if (! Schema::hasTable('visits')) {
            return [];
        }

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
}
