<?php

declare(strict_types=1);

namespace Modules\Payments\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case BankTransfer = 'bank_transfer';
    case Other = 'other';

    public function label(): string
    {
        return ucfirst(str_replace('_', ' ', $this->value));
    }

    public static function choices(): array
    {
        $map = [];

        foreach (self::cases() as $case) {
            $map[$case->value] = $case->label();
        }

        return $map;
    }
}
