<?php

declare(strict_types=1);

namespace Modules\Medicines\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Clinics\Models\Clinic;
use Modules\Medicines\Models\Medicine;

class MedicinesSeeder extends Seeder
{
    public function run(): void
    {
        $clinicId = Clinic::query()->value('id');

        $catalog = [
            ['name' => 'Paracetamol', 'generic_name' => 'Acetaminophen', 'category' => 'Analgesic', 'brand' => 'Panadol', 'strength' => '500mg', 'unit' => 'tablet', 'stock' => 500, 'reorder_level' => 100, 'cost_price' => 0.50, 'selling_price' => 1.25],
            ['name' => 'Ibuprofen', 'generic_name' => 'Ibuprofen', 'category' => 'NSAID', 'brand' => 'Brufen', 'strength' => '400mg', 'unit' => 'tablet', 'stock' => 300, 'reorder_level' => 80, 'cost_price' => 0.80, 'selling_price' => 2.00],
            ['name' => 'Amoxicillin', 'generic_name' => 'Amoxicillin', 'category' => 'Antibiotic', 'brand' => 'Amoxil', 'strength' => '500mg', 'unit' => 'capsule', 'stock' => 150, 'reorder_level' => 60, 'cost_price' => 1.20, 'selling_price' => 3.50],
            ['name' => 'Azithromycin', 'generic_name' => 'Azithromycin', 'category' => 'Antibiotic', 'brand' => 'Zithromax', 'strength' => '250mg', 'unit' => 'tablet', 'stock' => 80, 'reorder_level' => 40, 'cost_price' => 2.50, 'selling_price' => 6.00],
            ['name' => 'Metformin', 'generic_name' => 'Metformin', 'category' => 'Antidiabetic', 'brand' => 'Glucophage', 'strength' => '850mg', 'unit' => 'tablet', 'stock' => 200, 'reorder_level' => 70, 'cost_price' => 0.60, 'selling_price' => 1.75],
            ['name' => 'Amlodipine', 'generic_name' => 'Amlodipine', 'category' => 'Antihypertensive', 'brand' => 'Norvasc', 'strength' => '5mg', 'unit' => 'tablet', 'stock' => 180, 'reorder_level' => 60, 'cost_price' => 1.00, 'selling_price' => 2.50],
            ['name' => 'Lisinopril', 'generic_name' => 'Lisinopril', 'category' => 'Antihypertensive', 'brand' => 'Zestril', 'strength' => '10mg', 'unit' => 'tablet', 'stock' => 160, 'reorder_level' => 60, 'cost_price' => 0.90, 'selling_price' => 2.25],
            ['name' => 'Salbutamol Inhaler', 'generic_name' => 'Salbutamol', 'category' => 'Respiratory', 'brand' => 'Ventolin', 'strength' => '100mcg', 'unit' => 'inhaler', 'stock' => 40, 'reorder_level' => 15, 'cost_price' => 8.00, 'selling_price' => 15.00],
            ['name' => 'Omeprazole', 'generic_name' => 'Omeprazole', 'category' => 'Gastrointestinal', 'brand' => 'Losec', 'strength' => '20mg', 'unit' => 'capsule', 'stock' => 250, 'reorder_level' => 80, 'cost_price' => 0.70, 'selling_price' => 1.90],
            ['name' => 'Cetirizine', 'generic_name' => 'Cetirizine', 'category' => 'Antihistamine', 'brand' => 'Zyrtec', 'strength' => '10mg', 'unit' => 'tablet', 'stock' => 220, 'reorder_level' => 70, 'cost_price' => 0.40, 'selling_price' => 1.20],
            ['name' => 'Prednisolone', 'generic_name' => 'Prednisolone', 'category' => 'Corticosteroid', 'brand' => 'Deltasone', 'strength' => '5mg', 'unit' => 'tablet', 'stock' => 90, 'reorder_level' => 35, 'cost_price' => 0.55, 'selling_price' => 1.50],
            ['name' => 'Cough Syrup', 'generic_name' => 'Dextromethorphan', 'category' => 'Respiratory', 'brand' => 'Benylin', 'strength' => '100ml', 'unit' => 'bottle', 'stock' => 60, 'reorder_level' => 20, 'cost_price' => 3.00, 'selling_price' => 6.50],
            ['name' => 'ORS Sachet', 'generic_name' => 'Oral Rehydration Salts', 'category' => 'Electrolyte', 'brand' => 'ORSL', 'strength' => '20.5g', 'unit' => 'sachet', 'stock' => 400, 'reorder_level' => 120, 'cost_price' => 0.30, 'selling_price' => 0.90],
            ['name' => 'Insulin Glargine', 'generic_name' => 'Insulin Glargine', 'category' => 'Antidiabetic', 'brand' => 'Lantus', 'strength' => '100IU/ml', 'unit' => 'vial', 'stock' => 25, 'reorder_level' => 10, 'cost_price' => 22.00, 'selling_price' => 35.00],
            ['name' => 'Aspirin', 'generic_name' => 'Acetylsalicylic acid', 'category' => 'Analgesic', 'brand' => 'Bayer', 'strength' => '75mg', 'unit' => 'tablet', 'stock' => 350, 'reorder_level' => 100, 'cost_price' => 0.20, 'selling_price' => 0.80],
            ['name' => 'Diclofenac Gel', 'generic_name' => 'Diclofenac', 'category' => 'NSAID', 'brand' => 'Voltaren', 'strength' => '50g', 'unit' => 'tube', 'stock' => 70, 'reorder_level' => 25, 'cost_price' => 2.20, 'selling_price' => 5.00],
            ['name' => 'Vitamin D3', 'generic_name' => 'Cholecalciferol', 'category' => 'Supplement', 'brand' => 'Ostelin', 'strength' => '1000IU', 'unit' => 'tablet', 'stock' => 280, 'reorder_level' => 90, 'cost_price' => 0.45, 'selling_price' => 1.30],
            ['name' => 'Iron Supplement', 'generic_name' => 'Ferrous Sulfate', 'category' => 'Supplement', 'brand' => 'Fefol', 'strength' => '325mg', 'unit' => 'tablet', 'stock' => 190, 'reorder_level' => 60, 'cost_price' => 0.35, 'selling_price' => 1.00],
            ['name' => 'Amoxicillin Syrup', 'generic_name' => 'Amoxicillin', 'category' => 'Antibiotic', 'brand' => 'Amoxil', 'strength' => '125mg/5ml', 'unit' => 'bottle', 'stock' => 45, 'reorder_level' => 20, 'cost_price' => 2.80, 'selling_price' => 6.00],
            ['name' => 'Low Stock Antacid', 'generic_name' => 'Aluminium hydroxide', 'category' => 'Gastrointestinal', 'brand' => 'Gaviscon', 'strength' => '200ml', 'unit' => 'bottle', 'stock' => 12, 'reorder_level' => 30, 'cost_price' => 2.00, 'selling_price' => 4.50],
        ];

        foreach ($catalog as $medicine) {
            Medicine::query()->firstOrCreate(
                ['name' => $medicine['name'], 'clinic_id' => $clinicId],
                $medicine + ['clinic_id' => $clinicId],
            );
        }
    }
}
