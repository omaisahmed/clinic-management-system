<?php

declare(strict_types=1);

namespace Modules\Expenses\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authentication\Models\User;
use Modules\Clinics\Models\Clinic;
use Modules\Expenses\Enums\ExpensePaymentMethod;
use Modules\Expenses\Models\Expense;
use Modules\Expenses\Models\ExpenseCategory;

class ExpensesSeeder extends Seeder
{
    public function run(): void
    {
        $clinicId = Clinic::query()->value('id');
        $recorderIds = User::query()->pluck('id')->all();

        $categories = [
            'Medical Supplies',
            'Equipment',
            'Utilities',
            'Rent',
            'Salaries',
        ];

        $categoryIds = [];

        foreach ($categories as $name) {
            $categoryIds[$name] = ExpenseCategory::query()->firstOrCreate(
                ['name' => $name, 'clinic_id' => $clinicId],
                ['name' => $name, 'clinic_id' => $clinicId],
            )->id;
        }

        $expenses = [
            ['Medical Supplies', 'Box of syringes and gloves', 120.00],
            ['Utilities', 'Electricity bill', 85.50],
            ['Medical Supplies', 'Bandages and dressings', 45.00],
            ['Equipment', 'Thermometer maintenance', 60.00],
            ['Rent', 'Monthly clinic rent', 1200.00],
            ['Medical Supplies', 'Oxygen cylinder refill', 95.00],
            ['Utilities', 'Water bill', 30.25],
            ['Salaries', 'Nurse weekly advance', 200.00],
            ['Equipment', 'Printer ink and paper', 22.40],
            ['Medical Supplies', 'Stethoscope replacement', 75.00],
            ['Utilities', 'Internet subscription', 50.00],
            ['Salaries', 'Receptionist weekly advance', 180.00],
            ['Medical Supplies', 'Disposable gowns', 140.00],
            ['Equipment', 'Blood pressure cuff', 55.00],
            ['Rent', 'Storage unit rent', 300.00],
        ];

        foreach ($expenses as [$categoryName, $description, $amount]) {
            Expense::query()->create([
                'clinic_id' => $clinicId,
                'category_id' => $categoryIds[$categoryName],
                'description' => $description,
                'amount' => $amount,
                'expense_date' => today()->subDays(random_int(0, 25))->toDateString(),
                'payment_method' => ExpensePaymentMethod::cases()[array_rand(ExpensePaymentMethod::cases())]->value,
                'recorded_by' => $recorderIds[array_rand($recorderIds)],
                'notes' => 'Seeded expense.',
            ]);
        }
    }
}
