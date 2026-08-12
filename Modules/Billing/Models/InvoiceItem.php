<?php

declare(strict_types=1);

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;

class InvoiceItem extends BaseModel
{
    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'amount',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
