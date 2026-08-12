<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Appointments\Models\Appointment;
use Modules\Authentication\Models\User;
use Modules\Billing\Enums\InvoiceStatus;
use Modules\Billing\Models\Invoice;
use Modules\Expenses\Models\Expense;
use Modules\LabTests\Enums\LabTestStatus;
use Modules\LabTests\Models\LabTest;
use Modules\Medicines\Models\Medicine;
use Modules\Patients\Models\Patient;
use Modules\Payments\Models\Payment;
use Modules\Prescriptions\Models\Prescription;
use Modules\Prescriptions\Models\PrescriptionItem;
use Modules\Queue\Models\QueueEntry;
use Modules\Visits\Models\Visit;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ClinicWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        (new \Database\Seeders\DatabaseSeeder())->run();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs(User::where('email', 'admin@admin.com')->firstOrFail());
    }

    public function test_registration_and_appointment_booking(): void
    {
        $before = Patient::count();

        $response = $this->post(route('patients.store'), [
            'first_name' => 'Workflow',
            'last_name' => 'Test',
            'gender' => 'male',
            'date_of_birth' => '1990-05-15',
            'blood_group' => 'A+',
            'phone' => '+1 555 0199',
            'email' => 'workflow@example.com',
            'city' => 'Springfield',
        ]);

        $response->assertRedirect();

        $patient = Patient::where('email', 'workflow@example.com')->firstOrFail();

        $this->assertSame($before + 1, Patient::count());
        $this->assertNotEmpty($patient->patient_number);

        $doctor = User::role('doctor')->firstOrFail();

        $appointmentResponse = $this->post(route('appointments.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_type' => 'new_consultation',
            'status' => 'scheduled',
            'appointment_date' => today()->addDay()->toDateString(),
            'start_time' => '10:30',
            'duration' => 15,
            'reason' => 'Routine check-up',
        ]);

        $appointmentResponse->assertRedirect();

        $appointment = Appointment::where('patient_id', $patient->id)->firstOrFail();

        $this->assertSame($patient->id, $appointment->patient_id);
        $this->assertSame('scheduled', $appointment->status->value);
        $this->assertSame('10:30', $appointment->start_time);

        $this->get(route('patients.show', $patient))->assertStatus(200);
        $this->get(route('appointments.show', $appointment))->assertStatus(200);
    }

    public function test_queue_issuance_and_visit_flow(): void
    {
        $patient = $this->newPatient();
        $doctor = User::role('doctor')->firstOrFail();

        $response = $this->post(route('queue.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'notes' => 'Walk-in visit',
        ]);

        $response->assertRedirect();

        $entry = QueueEntry::where('patient_id', $patient->id)->firstOrFail();

        $this->assertSame('waiting', $entry->status->value);
        $this->assertGreaterThanOrEqual(1, $entry->token_number);

        $this->patch(route('queue.status', $entry), ['status' => 'in_progress'])->assertRedirect();
        $this->assertSame('in_progress', $entry->fresh()->status->value);
        $this->assertNotNull($entry->fresh()->called_at);

        $visitResponse = $this->post(route('visits.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'visit_date' => today()->toDateString(),
            'status' => 'in_progress',
            'chief_complaint' => 'Fever and chills',
            'diagnosis' => 'Viral infection',
            'temperature' => 38.4,
            'blood_pressure' => '118/76',
            'heart_rate' => 88,
            'respiratory_rate' => 18,
            'weight' => 68.5,
            'height' => 172,
        ]);

        $visitResponse->assertRedirect();

        $visit = Visit::where('patient_id', $patient->id)->firstOrFail();

        $this->assertNotEmpty($visit->visit_number);
        $this->assertSame('38.40', (string) $visit->temperature);
        $this->assertSame('in_progress', $visit->status->value);

        $this->put(route('visits.complete', $visit))->assertRedirect();
        $this->assertSame('completed', $visit->fresh()->status->value);

        $this->get(route('visits.show', $visit))->assertStatus(200);
    }

    public function test_prescription_creation_with_items(): void
    {
        $patient = $this->newPatient();
        $doctor = User::role('doctor')->firstOrFail();
        $visit = $this->newVisit($patient, $doctor);
        $medicine = Medicine::firstOrFail();

        $response = $this->post(route('prescriptions.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'visit_id' => $visit->id,
            'status' => 'active',
            'notes' => 'Take as directed.',
            'items' => [
                [
                    'medicine_id' => $medicine->id,
                    'name' => $medicine->name,
                    'dosage' => '1 tablet',
                    'frequency' => 'Twice daily',
                    'duration' => '7 days',
                    'instructions' => 'After meals',
                ],
                [
                    'medicine_id' => null,
                    'name' => 'Multivitamin',
                    'dosage' => '1 tablet',
                    'frequency' => 'Once daily',
                    'duration' => '30 days',
                    'instructions' => 'In the morning',
                ],
            ],
        ]);

        $response->assertRedirect();

        $prescription = Prescription::where('patient_id', $patient->id)->firstOrFail();

        $this->assertNotEmpty($prescription->prescription_number);
        $this->assertSame(2, PrescriptionItem::where('prescription_id', $prescription->id)->count());
        $this->assertSame('active', $prescription->status->value);

        $this->get(route('prescriptions.show', $prescription))->assertStatus(200);
    }

    public function test_lab_test_order_and_result_recording(): void
    {
        $patient = $this->newPatient();
        $doctor = User::role('doctor')->firstOrFail();
        $visit = $this->newVisit($patient, $doctor);

        $response = $this->post(route('lab_tests.store'), [
            'patient_id' => $patient->id,
            'visit_id' => $visit->id,
            'test_name' => 'Complete Blood Count',
            'category' => 'Hematology',
            'price' => 25.00,
            'sample_type' => 'Blood',
            'collection_date' => today()->toDateString(),
            'notes' => 'Fasting sample',
        ]);

        $response->assertRedirect();

        $test = LabTest::where('patient_id', $patient->id)->firstOrFail();

        $this->assertSame(LabTestStatus::Requested->value, $test->status->value);

        $resultResponse = $this->put(route('lab_tests.result.update', $test), [
            'result' => 'WBC 6.5, Hb 14.2, Platelets 250k',
            'status' => 'completed',
            'result_date' => today()->toDateString(),
        ]);

        $resultResponse->assertRedirect();

        $test->refresh();

        $this->assertSame(LabTestStatus::Completed->value, $test->status->value);
        $this->assertStringContainsString('WBC 6.5', $test->result);

        $this->get(route('lab_tests.show', $test))->assertStatus(200);
    }

    public function test_document_upload_and_download(): void
    {
        Storage::fake('public');

        $patient = $this->newPatient();

        $response = $this->post(route('documents.store'), [
            'patient_id' => $patient->id,
            'title' => 'Lab Results',
            'category' => 'lab_results',
            'notes' => 'CBC report',
            'file' => UploadedFile::fake()->create('cbc_report.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect();

        $document = \Modules\Documents\Models\Document::where('patient_id', $patient->id)->firstOrFail();

        Storage::disk('public')->assertExists($document->file_path);
        $this->assertSame('cbc_report.pdf', $document->original_name);

        $this->get(route('documents.download', $document))
            ->assertOk()
            ->assertDownload('cbc_report.pdf');

        $this->get(route('documents.show', $document))->assertStatus(200);
    }

    public function test_billing_payment_and_expense_flow(): void
    {
        $patient = $this->newPatient();
        $doctor = User::role('doctor')->firstOrFail();
        $visit = $this->newVisit($patient, $doctor);

        $invoiceResponse = $this->post(route('billing.store'), [
            'patient_id' => $patient->id,
            'visit_id' => $visit->id,
            'issue_date' => today()->toDateString(),
            'due_date' => today()->addDays(7)->toDateString(),
            'status' => 'issued',
            'discount' => 0,
            'tax' => 0,
            'items' => [
                ['description' => 'General consultation', 'quantity' => 1, 'unit_price' => 50.00],
                ['description' => 'Blood test', 'quantity' => 1, 'unit_price' => 25.00],
            ],
        ]);

        $invoiceResponse->assertRedirect();

        $invoice = Invoice::where('patient_id', $patient->id)->firstOrFail();

        $this->assertNotEmpty($invoice->invoice_number);
        $this->assertSame('75.00', (string) $invoice->total);
        $this->assertSame(InvoiceStatus::Issued->value, $invoice->status->value);
        $this->assertSame(2, $invoice->items()->count());

        $paymentResponse = $this->post(route('payments.store'), [
            'patient_id' => $patient->id,
            'invoice_id' => $invoice->id,
            'amount' => 75.00,
            'method' => 'cash',
            'reference' => 'REF-TEST-001',
            'payment_date' => today()->toDateString(),
        ]);

        $paymentResponse->assertRedirect();

        $payment = Payment::where('invoice_id', $invoice->id)->firstOrFail();
        $this->assertSame('75.00', (string) $payment->amount);

        $this->assertSame(InvoiceStatus::Paid->value, $invoice->fresh()->status->value);
        $this->assertSame(0.0, (float) $invoice->fresh()->due_amount);

        $expenseResponse = $this->post(route('expenses.store'), [
            'category_id' => $this->expenseCategoryId(),
            'description' => 'Office supplies',
            'amount' => 45.50,
            'expense_date' => today()->toDateString(),
            'payment_method' => 'cash',
            'notes' => 'Purchased paper and pens',
        ]);

        $expenseResponse->assertRedirect();

        $expense = Expense::where('description', 'Office supplies')->firstOrFail();
        $this->assertSame('45.50', (string) $expense->amount);

        $this->get(route('billing.show', $invoice))->assertStatus(200);
        $this->get(route('payments.show', $payment))->assertStatus(200);
        $this->get(route('expenses.show', $expense))->assertStatus(200);
    }

    public function test_dashboard_and_reports_reflect_seeded_data(): void
    {
        $this->get('/dashboard')
            ->assertStatus(200)
            ->assertSee((string) Patient::count());

        $this->get(route('reports.index'))->assertStatus(200);
        $this->get(route('reports.revenue'))->assertStatus(200);
        $this->get(route('reports.patients-report'))->assertStatus(200);
        $this->get(route('reports.inventory'))->assertStatus(200);
    }

    private function newPatient(): Patient
    {
        return app(\Modules\Patients\Services\PatientService::class)->create([
            'clinic_id' => \Modules\Clinics\Models\Clinic::query()->value('id'),
            'first_name' => 'Flow',
            'last_name' => 'Patient',
            'gender' => 'female',
            'phone' => '+1 555 ' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
            'email' => 'flow' . random_int(1000, 99999) . '@example.com',
        ]);
    }

    private function newVisit(Patient $patient, User $doctor): Visit
    {
        return app(\Modules\Visits\Services\VisitService::class)->create([
            'clinic_id' => \Modules\Clinics\Models\Clinic::query()->value('id'),
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'visit_date' => today()->toDateString(),
            'status' => 'in_progress',
            'chief_complaint' => 'General check-up',
            'diagnosis' => 'Healthy',
        ]);
    }

    private function expenseCategoryId(): string
    {
        return \Modules\Expenses\Models\ExpenseCategory::query()->value('id');
    }
}
