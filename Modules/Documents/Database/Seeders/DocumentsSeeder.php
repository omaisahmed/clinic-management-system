<?php

declare(strict_types=1);

namespace Modules\Documents\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Modules\Authentication\Models\User;
use Modules\Clinics\Models\Clinic;
use Modules\Documents\Models\Document;
use Modules\Patients\Models\Patient;

class DocumentsSeeder extends Seeder
{
    public function run(): void
    {
        $clinicId = Clinic::query()->value('id');
        $patientIds = Patient::query()->pluck('id')->all();
        $uploaderIds = User::query()->pluck('id')->all();

        if ($patientIds === [] || $uploaderIds === []) {
            return;
        }

        $documents = [
            ['title' => 'Lab Results - CBC', 'category' => 'lab_results', 'original_name' => 'cbc_results.pdf', 'notes' => 'Complete blood count report.'],
            ['title' => 'Insurance Card Front', 'category' => 'insurance', 'original_name' => 'insurance_front.jpg', 'notes' => 'Front of insurance card.'],
            ['title' => 'Consent Form', 'category' => 'consent_forms', 'original_name' => 'consent_form.pdf', 'notes' => 'Signed consent for treatment.'],
            ['title' => 'X-Ray Report', 'category' => 'reports', 'original_name' => 'chest_xray.pdf', 'notes' => 'Chest X-ray radiologist report.'],
            ['title' => 'Prescription History', 'category' => 'history', 'original_name' => 'prescription_history.pdf', 'notes' => 'Past prescriptions record.'],
            ['title' => 'Referral Letter', 'category' => 'referrals', 'original_name' => 'referral.pdf', 'notes' => 'Referral to cardiology.'],
            ['title' => 'Vaccination Record', 'category' => 'vaccination', 'original_name' => 'vaccination_record.pdf', 'notes' => 'Immunization history.'],
            ['title' => 'Discharge Summary', 'category' => 'reports', 'original_name' => 'discharge_summary.pdf', 'notes' => 'Previous admission discharge summary.'],
        ];

        foreach ($documents as $doc) {
            $path = 'documents/' . $doc['original_name'];
            $content = 'Demo document: ' . $doc['title'] . PHP_EOL . 'This is a placeholder file created by the seeder.';

            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, $content);
            }

            Document::query()->create([
                'clinic_id' => $clinicId,
                'patient_id' => $patientIds[array_rand($patientIds)],
                'title' => $doc['title'],
                'category' => $doc['category'],
                'file_path' => $path,
                'original_name' => $doc['original_name'],
                'mime_type' => 'application/pdf',
                'file_size' => strlen($content),
                'uploaded_by' => $uploaderIds[array_rand($uploaderIds)],
                'notes' => $doc['notes'],
            ]);
        }
    }
}
