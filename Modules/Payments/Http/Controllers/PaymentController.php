<?php

declare(strict_types=1);

namespace Modules\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Audit\Facades\Audit;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Services\InvoiceService;
use Modules\Patients\Models\Patient;
use Modules\Payments\Enums\PaymentMethod;
use Modules\Payments\Models\Payment;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('payments.view');

        $payments = Payment::query()
            ->search($request->query('q'))
            ->forMethod($request->query('method'))
            ->betweenDates($request->query('date_from'), $request->query('date_to'))
            ->orderByDesc('payment_date')
            ->paginate(15)
            ->withQueryString();

        return view('payments::index', [
            'payments' => $payments,
            'methods' => PaymentMethod::choices(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('payments.create');

        return view('payments::create', [
            'patients' => $this->patientOptions(),
            'invoices' => $this->invoiceOptions(),
            'methods' => PaymentMethod::choices(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('payments.create');

        $data = $this->validatePayment($request);

        $data['clinic_id'] = current_clinic()?->id;

        $payment = Payment::create($data);

        if ($payment->invoice && class_exists(InvoiceService::class)) {
            app(InvoiceService::class)->refreshPaidStatus($payment->invoice);
        }

        Audit::record('Payment Recorded', 'payments', $payment, [
            'patient_id' => $payment->patient_id,
        ]);

        return redirect()
            ->route('payments.show', $payment)
            ->with('toast', [['type' => 'success', 'message' => 'Payment recorded.']]);
    }

    public function show(Payment $payment): View
    {
        Gate::authorize('payments.view');

        $payment->load('patient');

        if (class_exists(Invoice::class)) {
            $payment->load('invoice');
        }

        return view('payments::show', [
            'payment' => $payment,
        ]);
    }

    public function edit(Payment $payment): View
    {
        Gate::authorize('payments.update');

        return view('payments::edit', [
            'payment' => $payment,
            'patients' => $this->patientOptions(),
            'invoices' => $this->invoiceOptions(),
            'methods' => PaymentMethod::choices(),
        ]);
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        Gate::authorize('payments.update');

        $data = $this->validatePayment($request);

        $oldInvoiceId = $payment->invoice_id;

        $payment->update($data);

        if ($payment->invoice && class_exists(InvoiceService::class)) {
            app(InvoiceService::class)->refreshPaidStatus($payment->invoice);
        }

        if ($oldInvoiceId && $oldInvoiceId !== $payment->invoice_id && class_exists(InvoiceService::class)) {
            $oldInvoice = Invoice::find($oldInvoiceId);

            if ($oldInvoice) {
                app(InvoiceService::class)->refreshPaidStatus($oldInvoice);
            }
        }

        Audit::record('Payment Updated', 'payments', $payment, [
            'patient_id' => $payment->patient_id,
        ]);

        return redirect()
            ->route('payments.show', $payment)
            ->with('toast', [['type' => 'success', 'message' => 'Payment updated.']]);
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        Gate::authorize('payments.delete');

        $invoiceId = $payment->invoice_id;

        $payment->delete();

        if ($invoiceId && class_exists(InvoiceService::class)) {
            $invoice = Invoice::find($invoiceId);

            if ($invoice) {
                app(InvoiceService::class)->refreshPaidStatus($invoice);
            }
        }

        Audit::record('Payment Deleted', 'payments', $payment, [
            'patient_id' => $payment->patient_id,
        ]);

        return redirect()
            ->route('payments.index')
            ->with('toast', [['type' => 'success', 'message' => 'Payment removed.']]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayment(Request $request): array
    {
        return $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', Rule::in(array_keys(PaymentMethod::choices()))],
            'reference' => ['nullable', 'string', 'max:120'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function patientOptions(): array
    {
        return Patient::query()
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name'])
            ->mapWithKeys(fn (Patient $patient) => [$patient->id => $patient->full_name])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function invoiceOptions(): array
    {
        if (! class_exists(Invoice::class)) {
            return [];
        }

        return Invoice::query()
            ->where('due_amount', '>', 0)
            ->with('patient:id,first_name,last_name')
            ->orderBy('created_at')
            ->get()
            ->mapWithKeys(fn (Invoice $invoice) => [
                $invoice->id => "{$invoice->invoice_number} — {$invoice->patient?->full_name}",
            ])
            ->all();
    }
}
