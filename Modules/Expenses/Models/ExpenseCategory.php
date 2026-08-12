<?php

declare(strict_types=1);

namespace Modules\Expenses\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\BaseModel;

class ExpenseCategory extends BaseModel
{
    protected $fillable = [
        'clinic_id',
        'name',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }
}
