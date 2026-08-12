<?php

declare(strict_types=1);

namespace Modules\Clinics\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Clinics\Models\Clinic;
use Modules\Clinics\Models\ClinicSetting;

class ClinicSeeder extends Seeder
{
    public function run(): void
    {
        $clinic = Clinic::query()->firstOrCreate(
            ['name' => 'City Care Medical Clinic'],
            [
                'tagline' => 'Caring for your health',
                'description' => 'A modern family clinic.',
                'phone' => '+1 555 0100',
                'whatsapp' => '+1 555 0100',
                'email' => 'info@citycare.example',
                'website' => 'https://citycare.example',
                'address' => '123 Health Avenue',
                'city' => 'Springfield',
                'state' => 'IL',
                'country' => 'US',
                'postal_code' => '62701',
                'timezone' => 'UTC',
                'currency' => 'USD',
                'registration_number' => 'CL-2026-0001',
                'tax_number' => 'TAX-000001',
            ]
        );

        $defaults = [
            ['group' => 'branding', 'key' => 'branding.primary_color', 'value' => '#0d9488', 'type' => 'color'],
            ['group' => 'branding', 'key' => 'branding.secondary_color', 'value' => '#0891b2', 'type' => 'color'],
            ['group' => 'branding', 'key' => 'branding.accent_color', 'value' => '#f59e0b', 'type' => 'color'],
            ['group' => 'invoice', 'key' => 'invoice.prefix', 'value' => 'INV', 'type' => 'string'],
            ['group' => 'invoice', 'key' => 'invoice.footer', 'value' => 'Thank you for choosing our clinic.', 'type' => 'textarea'],
            ['group' => 'invoice', 'key' => 'invoice.payment_terms', 'value' => 'Due on receipt', 'type' => 'string'],
            ['group' => 'patient', 'key' => 'patient.prefix', 'value' => 'PT', 'type' => 'string'],
            ['group' => 'prescription', 'key' => 'prescription.header', 'value' => 'Rx', 'type' => 'string'],
            ['group' => 'prescription', 'key' => 'prescription.footer', 'value' => 'Please complete the full course of medicine.', 'type' => 'textarea'],
            ['group' => 'prescription', 'key' => 'prescription.default_instructions', 'value' => 'Take as directed', 'type' => 'textarea'],
            ['group' => 'appointment', 'key' => 'appointment.default_duration', 'value' => '15', 'type' => 'integer'],
            ['group' => 'appointment', 'key' => 'appointment.allow_walk_ins', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'notification', 'key' => 'notification.appointment_reminders', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'notification', 'key' => 'notification.payment_reminders', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'notification', 'key' => 'notification.follow_up_reminders', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'localization', 'key' => 'localization.language', 'value' => 'en', 'type' => 'string'],
            ['group' => 'localization', 'key' => 'localization.currency', 'value' => 'USD', 'type' => 'string'],
            ['group' => 'localization', 'key' => 'localization.timezone', 'value' => 'UTC', 'type' => 'string'],
            ['group' => 'localization', 'key' => 'localization.date_format', 'value' => 'M d, Y', 'type' => 'string'],
            ['group' => 'localization', 'key' => 'localization.time_format', 'value' => 'g:i A', 'type' => 'string'],
        ];

        foreach ($defaults as $setting) {
            ClinicSetting::query()->firstOrCreate(
                ['clinic_id' => $clinic->id, 'key' => $setting['key']],
                $setting,
            );
        }
    }
}
