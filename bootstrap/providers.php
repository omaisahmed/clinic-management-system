<?php

use App\Providers\AppServiceProvider;
use Modules\Appointments\Providers\AppointmentsServiceProvider;
use Modules\Audit\Providers\AuditServiceProvider;
use Modules\Authentication\Providers\AuthenticationServiceProvider;
use Modules\Billing\Providers\BillingServiceProvider;
use Modules\Clinics\Providers\ClinicsServiceProvider;
use Modules\Core\Providers\CoreServiceProvider;
use Modules\Dashboard\Providers\DashboardServiceProvider;
use Modules\Documents\Providers\DocumentsServiceProvider;
use Modules\Expenses\Providers\ExpensesServiceProvider;
use Modules\LabTests\Providers\LabTestsServiceProvider;
use Modules\MedicalRecords\Providers\MedicalRecordsServiceProvider;
use Modules\Medicines\Providers\MedicinesServiceProvider;
use Modules\Patients\Providers\PatientsServiceProvider;
use Modules\Payments\Providers\PaymentsServiceProvider;
use Modules\Prescriptions\Providers\PrescriptionsServiceProvider;
use Modules\Queue\Providers\QueueServiceProvider;
use Modules\Reports\Providers\ReportsServiceProvider;
use Modules\Settings\Providers\SettingsServiceProvider;
use Modules\Visits\Providers\VisitsServiceProvider;

return [
    AppServiceProvider::class,
    CoreServiceProvider::class,
    AuthenticationServiceProvider::class,
    ClinicsServiceProvider::class,
    SettingsServiceProvider::class,
    DashboardServiceProvider::class,
    PatientsServiceProvider::class,
    MedicalRecordsServiceProvider::class,
    AppointmentsServiceProvider::class,
    VisitsServiceProvider::class,
    QueueServiceProvider::class,
    MedicinesServiceProvider::class,
    PrescriptionsServiceProvider::class,
    LabTestsServiceProvider::class,
    DocumentsServiceProvider::class,
    BillingServiceProvider::class,
    PaymentsServiceProvider::class,
    ExpensesServiceProvider::class,
    ReportsServiceProvider::class,
    AuditServiceProvider::class,
];
