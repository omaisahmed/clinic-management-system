<?php

declare(strict_types=1);

namespace Modules\Payments\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Services\InvoiceService;
use Modules\Clinics\Models\Clinic;
use Modules\Payments\Enums\PaymentMethod;
use Modules\Payments\Models\Payment;

class PaymentsSeeder extends Seeder
{
    public function run(): void
    {
        $clinicId = Clinic::query()->value('id');
        $issuedInvoices = Invoice::query()
            ->where('status', '!=', 'cancelled')
            ->get();

        if ($issuedInvoices->isEmpty()) {
            return;
        }

        $methods = PaymentMethod::cases();
        $service = app(InvoiceService::class);

        foreach ($issuedInvoices as $invoice) {
            $total = (float) $invoice->total;
            $roll = random_int(0, 99);

            if ($roll < 50) {
                Payment::query()->create([
                    'clinic_id' => $clinicId,
                    'patient_id' => $invoice->patient_id,
                    'invoice_id' => $invoice->id,
                    'amount' => $total,
                    'method' => $methods[array_rand($methods)]->value,
                    'reference' => 'PMT-' . strtoupper(uniqid()),
                    'payment_date' => $invoice->issue_date->addDays(random_int(0, 3))->toDateString(),
                    'notes' => 'Seeded full payment.',
                ]);
            } elseif ($roll < 75) {
                $partial = round($total / 2, 2);

                Payment::query()->create([
                    'clinic_id' => $clinicId,
                    'patient_id' => $invoice->patient_id,
                    'invoice_id' => $invoice->id,
                    'amount' => $partial,
                    'method' => $methods[array_rand($methods)]->value,
                    'reference' => 'PMT-' . strtoupper(uniqid()),
                    'payment_date' => $invoice->issue_date->addDays(random_int(0, 2))->toDateString(),
                    'notes' => 'Seeded partial payment.',
                ]);
            }

            $service->refreshPaidStatus($invoice);
        }
    }
}
