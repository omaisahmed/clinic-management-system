<?php

declare(strict_types=1);

namespace Modules\Billing\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Billing\Enums\InvoiceStatus;
use Modules\Billing\Models\Invoice;

class InvoiceService
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $items
     */
    public function createWithItems(array $data, array $items): Invoice
    {
        $prepared = $this->prepareItems($items);

        $data['clinic_id'] = $data['clinic_id'] ?? current_clinic()?->id;
        $data['invoice_number'] = $this->generateInvoiceNumber();
        $data['subtotal'] = $prepared['subtotal'];
        $data['total'] = round($data['subtotal'] + (float) ($data['tax'] ?? 0) - (float) ($data['discount'] ?? 0), 2);
        $data['due_amount'] = $data['total'];

        return DB::transaction(function () use ($data, $prepared): Invoice {
            $invoice = Invoice::query()->create($data);

            $invoice->items()->createMany($prepared['items']);

            return $invoice;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $items
     */
    public function updateWithItems(Invoice $invoice, array $data, array $items): Invoice
    {
        $prepared = $this->prepareItems($items);

        $data['subtotal'] = $prepared['subtotal'];
        $data['total'] = round($data['subtotal'] + (float) ($data['tax'] ?? 0) - (float) ($data['discount'] ?? 0), 2);
        $data['due_amount'] = max(round((float) $data['total'] - (float) $invoice->paid_amount, 2), 0);

        DB::transaction(function () use ($invoice, $data, $prepared): void {
            $invoice->update($data);

            $invoice->items()->delete();

            $invoice->items()->createMany($prepared['items']);
        });

        return $invoice->fresh();
    }

    public function refreshPaidStatus(Invoice $invoice): Invoice
    {
        $paidAmount = (float) $invoice->payments()->sum('amount');

        $invoice->paid_amount = $paidAmount;
        $invoice->due_amount = max(round((float) $invoice->total - $paidAmount, 2), 0);

        if ($invoice->status === InvoiceStatus::Cancelled) {
            $invoice->save();

            return $invoice;
        }

        if ($invoice->due_amount <= 0) {
            $invoice->status = InvoiceStatus::Paid;
        } elseif ($invoice->status === InvoiceStatus::Paid) {
            $invoice->status = InvoiceStatus::Issued;
        }

        $invoice->save();

        return $invoice;
    }

    public function generateInvoiceNumber(): string
    {
        $prefix = Str::upper((string) setting('invoice.prefix', 'INV'));

        return DB::transaction(function () use ($prefix): string {
            $last = Invoice::query()
                ->where('invoice_number', 'like', $prefix . '-%')
                ->orderByDesc('invoice_number')
                ->value('invoice_number');

            $sequence = 1;

            if ($last !== null) {
                $sequence = ((int) Str::afterLast($last, '-')) + 1;
            }

            return $prefix . '-' . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array{items: array<int, array<string, mixed>>, subtotal: float}
     */
    private function prepareItems(array $items): array
    {
        $prepared = [];
        $subtotal = 0.0;

        foreach ($items as $item) {
            $quantity = (int) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $amount = round($quantity * $unitPrice, 2);

            $prepared[] = [
                'description' => (string) ($item['description'] ?? ''),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'amount' => $amount,
            ];

            $subtotal += $amount;
        }

        return [
            'items' => $prepared,
            'subtotal' => round($subtotal, 2),
        ];
    }
}
