<?php

declare(strict_types=1);

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Appointments\Models\Appointment;
use Modules\Authentication\Models\User;
use Modules\Billing\Models\Invoice;
use Modules\Documents\Models\Document;
use Modules\Expenses\Models\Expense;
use Modules\Expenses\Models\ExpenseCategory;
use Modules\LabTests\Models\LabTest;
use Modules\Medicines\Models\Medicine;
use Modules\Patients\Models\Patient;
use Modules\Payments\Models\Payment;
use Modules\Prescriptions\Models\Prescription;
use Modules\Queue\Models\QueueEntry;
use Modules\Visits\Models\Visit;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class FullBusinessFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        (new DatabaseSeeder)->run();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs(User::where('email', 'admin@admin.com')->firstOrFail());
    }

    public function test_patient_journey_end_to_end(): void
    {
        Storage::fake('public');

        $doctor = User::role('doctor')->firstOrFail();
        $medicine = Medicine::firstOrFail();

        $patientCountBefore = Patient::count();

        // 1. Register a new patient.
        $this->post(route('patients.store'), [
            'first_name' => 'Journey',
            'last_name' => 'Patient',
            'gender' => 'female',
            'date_of_birth' => '1985-08-20',
            'blood_group' => 'O+',
            'phone' => '+1 555 9876',
            'email' => 'journey@example.com',
            'city' => 'Springfield',
        ])->assertRedirect();

        $patient = Patient::where('email', 'journey@example.com')->firstOrFail();

        $this->assertSame($patientCountBefore + 1, Patient::count());
        $this->assertNotEmpty($patient->patient_number);

        // 2. Book an appointment.
        $this->post(route('appointments.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_type' => 'new_consultation',
            'status' => 'scheduled',
            'appointment_date' => today()->addDay()->toDateString(),
            'start_time' => '09:30',
            'duration' => 15,
            'reason' => 'Fever and cough',
        ])->assertRedirect();

        $appointment = Appointment::where('patient_id', $patient->id)->firstOrFail();

        $this->assertSame('scheduled', $appointment->status->value);

        // 3. Check the patient into today's queue.
        $this->post(route('queue.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment->id,
            'notes' => 'Arrived for consultation',
        ])->assertRedirect();

        $queueEntry = QueueEntry::where('patient_id', $patient->id)->firstOrFail();

        $this->assertSame('waiting', $queueEntry->status->value);
        $this->assertGreaterThanOrEqual(1, $queueEntry->token_number);

        // 4. Call the patient and start the visit.
        $this->patch(route('queue.status', $queueEntry), ['status' => 'in_progress'])->assertRedirect();
        $this->assertSame('in_progress', $queueEntry->fresh()->status->value);

        $this->post(route('visits.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment->id,
            'visit_date' => today()->toDateString(),
            'status' => 'in_progress',
            'chief_complaint' => 'Fever, cough and headache for three days',
            'diagnosis' => 'Upper respiratory tract infection',
            'temperature' => 38.6,
            'blood_pressure' => '120/80',
            'heart_rate' => 92,
            'respiratory_rate' => 19,
            'weight' => 62.0,
            'height' => 165.0,
        ])->assertRedirect();

        $visit = Visit::where('patient_id', $patient->id)->firstOrFail();

        $this->assertNotEmpty($visit->visit_number);
        $this->assertSame('in_progress', $visit->status->value);

        // 5. Write a prescription with items.
        $this->post(route('prescriptions.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'visit_id' => $visit->id,
            'status' => 'active',
            'notes' => 'Take as directed.',
            'items' => [
                [
                    'medicine_id' => $medicine->id,
                    'name' => $medicine->name,
                    'dosage' => '1 '.$medicine->strength,
                    'frequency' => 'Three times daily',
                    'duration' => '7 days',
                    'instructions' => 'After meals',
                ],
                [
                    'medicine_id' => null,
                    'name' => 'Vitamin C',
                    'dosage' => '500mg',
                    'frequency' => 'Once daily',
                    'duration' => '14 days',
                    'instructions' => 'In the morning',
                ],
            ],
        ])->assertRedirect();

        $prescription = Prescription::where('patient_id', $patient->id)->firstOrFail();

        $this->assertNotEmpty($prescription->prescription_number);
        $this->assertCount(2, $prescription->items);

        // 6. Order a lab test and record its result.
        $this->post(route('lab_tests.store'), [
            'patient_id' => $patient->id,
            'visit_id' => $visit->id,
            'test_name' => 'Complete Blood Count',
            'category' => 'Hematology',
            'price' => 25.00,
            'sample_type' => 'Blood',
            'collection_date' => today()->toDateString(),
            'notes' => 'Fasting sample',
        ])->assertRedirect();

        $labTest = LabTest::where('patient_id', $patient->id)->firstOrFail();

        $this->assertSame('requested', $labTest->status->value);

        $this->put(route('lab_tests.result.update', $labTest), [
            'result' => 'WBC 6.5, RBC 4.8, Hb 14.2, Platelets 250k',
            'status' => 'completed',
            'result_date' => today()->toDateString(),
        ])->assertRedirect();

        $this->assertSame('completed', $labTest->fresh()->status->value);

        // 7. Attach a document to the patient record.
        $this->post(route('documents.store'), [
            'patient_id' => $patient->id,
            'title' => 'Initial Lab Report',
            'category' => 'lab_results',
            'notes' => 'CBC results',
            'file' => UploadedFile::fake()->create('cbc_journey.pdf', 100, 'application/pdf'),
        ])->assertRedirect();

        $document = Document::where('patient_id', $patient->id)->firstOrFail();

        Storage::disk('public')->assertExists($document->file_path);
        $this->assertSame('cbc_journey.pdf', $document->original_name);

        // 8. Complete the visit.
        $this->put(route('visits.complete', $visit))->assertRedirect();
        $this->assertSame('completed', $visit->fresh()->status->value);
        $this->patch(route('queue.status', $queueEntry), ['status' => 'completed'])->assertRedirect();

        // 9. Generate the invoice for the consultation and tests.
        $this->post(route('billing.store'), [
            'patient_id' => $patient->id,
            'visit_id' => $visit->id,
            'issue_date' => today()->toDateString(),
            'due_date' => today()->addDays(7)->toDateString(),
            'status' => 'issued',
            'discount' => 0,
            'tax' => 0,
            'items' => [
                ['description' => 'General consultation', 'quantity' => 1, 'unit_price' => 50.00],
                ['description' => 'Complete Blood Count', 'quantity' => 1, 'unit_price' => 25.00],
            ],
        ])->assertRedirect();

        $invoice = Invoice::where('patient_id', $patient->id)->firstOrFail();

        $this->assertNotEmpty($invoice->invoice_number);
        $this->assertSame('75.00', (string) $invoice->total);
        $this->assertSame('75.00', (string) $invoice->due_amount);
        $this->assertSame('issued', $invoice->status->value);

        // 10. Record full payment and confirm the invoice settles.
        $this->post(route('payments.store'), [
            'patient_id' => $patient->id,
            'invoice_id' => $invoice->id,
            'amount' => 75.00,
            'method' => 'cash',
            'reference' => 'PMT-JOURNEY-001',
            'payment_date' => today()->toDateString(),
        ])->assertRedirect();

        $payment = Payment::where('invoice_id', $invoice->id)->firstOrFail();

        $this->assertSame('75.00', (string) $payment->amount);
        $this->assertSame('paid', $invoice->fresh()->status->value);
        $this->assertSame(0.0, (float) $invoice->fresh()->due_amount);

        // 11. Record a clinic expense.
        $this->post(route('expenses.store'), [
            'category_id' => ExpenseCategory::query()->value('id'),
            'description' => 'Medical supplies restock',
            'amount' => 45.50,
            'expense_date' => today()->toDateString(),
            'payment_method' => 'cash',
            'notes' => 'Bandages and gloves',
        ])->assertRedirect();

        $expense = Expense::where('description', 'Medical supplies restock')->firstOrFail();

        $this->assertSame('45.50', (string) $expense->amount);

        // 12. Verify key pages and reports render with the new records.
        $this->get(route('patients.show', $patient))->assertStatus(200);
        $this->get(route('appointments.show', $appointment))->assertStatus(200);
        $this->get(route('visits.show', $visit))->assertStatus(200);
        $this->get(route('prescriptions.show', $prescription))->assertStatus(200);
        $this->get(route('lab_tests.show', $labTest))->assertStatus(200);
        $this->get(route('documents.show', $document))->assertStatus(200);
        $this->get(route('billing.show', $invoice))->assertStatus(200);
        $this->get(route('payments.show', $payment))->assertStatus(200);
        $this->get(route('expenses.show', $expense))->assertStatus(200);

        $this->get('/dashboard')
            ->assertStatus(200)
            ->assertSee((string) Patient::count());

        $this->get(route('reports.index'))->assertStatus(200);
        $this->get(route('reports.revenue'))->assertStatus(200);
        $this->get(route('reports.patients-report'))->assertStatus(200);
        $this->get(route('reports.inventory'))->assertStatus(200);
    }
}
