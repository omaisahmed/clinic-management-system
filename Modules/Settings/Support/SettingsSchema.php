<?php

declare(strict_types=1);

namespace Modules\Settings\Support;

use Modules\Settings\Enums\SettingType;

/**
 * Declarative description of every settings group. Drives both the forms
 * and the validation so settings never drift from the UI.
 */
final class SettingsSchema
{
    /**
     * @return array<string, array{label: string, icon: string, fields: array<int, array{key: string, label: string, type: SettingType, encrypted?: bool, help?: string, searchable?: bool, options?: array<string, string>}>}>
     */
    public static function groups(): array
    {
        return [
            'clinic' => [
                'label' => 'Clinic',
                'icon' => 'building',
                'fields' => self::clinicFields(),
            ],
            'branding' => [
                'label' => 'Branding',
                'icon' => 'palette',
                'fields' => self::brandingFields(),
            ],
            'prescription' => [
                'label' => 'Prescription',
                'icon' => 'document-text',
                'fields' => self::prescriptionFields(),
            ],
            'invoice' => [
                'label' => 'Invoice',
                'icon' => 'receipt',
                'fields' => self::invoiceFields(),
            ],
            'appointment' => [
                'label' => 'Appointments',
                'icon' => 'calendar',
                'fields' => self::appointmentFields(),
            ],
            'notification' => [
                'label' => 'Notifications',
                'icon' => 'bell',
                'fields' => self::notificationFields(),
            ],
            'localization' => [
                'label' => 'Localization',
                'icon' => 'globe',
                'fields' => self::localizationFields(),
            ],
            'integrations' => [
                'label' => 'Integrations',
                'icon' => 'plug',
                'fields' => self::integrationFields(),
            ],
        ];
    }

    public static function group(string $group): ?array
    {
        return self::groups()[$group] ?? null;
    }

    /**
     * @return array<int, array{key: string, label: string, type: SettingType, encrypted?: bool, help?: string, options?: array<string, string>}>
     */
    private static function clinicFields(): array
    {
        return [
            ['key' => 'clinic.name', 'label' => 'Clinic Name', 'type' => SettingType::String],
            ['key' => 'clinic.tagline', 'label' => 'Tagline', 'type' => SettingType::String],
            ['key' => 'clinic.description', 'label' => 'Description', 'type' => SettingType::Textarea],
            ['key' => 'clinic.phone', 'label' => 'Phone', 'type' => SettingType::String],
            ['key' => 'clinic.whatsapp', 'label' => 'WhatsApp', 'type' => SettingType::String],
            ['key' => 'clinic.email', 'label' => 'Email', 'type' => SettingType::String],
            ['key' => 'clinic.website', 'label' => 'Website', 'type' => SettingType::String],
            ['key' => 'clinic.address', 'label' => 'Address', 'type' => SettingType::String],
            ['key' => 'clinic.city', 'label' => 'City', 'type' => SettingType::String],
            ['key' => 'clinic.state', 'label' => 'State / Province', 'type' => SettingType::String],
            ['key' => 'clinic.country', 'label' => 'Country', 'type' => SettingType::String],
            ['key' => 'clinic.postal_code', 'label' => 'Postal Code', 'type' => SettingType::String],
            ['key' => 'clinic.registration_number', 'label' => 'Registration Number', 'type' => SettingType::String],
            ['key' => 'clinic.tax_number', 'label' => 'Tax Number', 'type' => SettingType::String],
            ['key' => 'clinic.currency', 'label' => 'Currency', 'type' => SettingType::String, 'searchable' => true, 'options' => self::currencies()],
            ['key' => 'clinic.timezone', 'label' => 'Timezone', 'type' => SettingType::String, 'searchable' => true, 'options' => self::timezones()],
        ];
    }

    private static function brandingFields(): array
    {
        return [
            ['key' => 'branding.primary_color', 'label' => 'Primary Color', 'type' => SettingType::Color],
            ['key' => 'branding.secondary_color', 'label' => 'Secondary Color', 'type' => SettingType::Color],
            ['key' => 'branding.accent_color', 'label' => 'Accent Color', 'type' => SettingType::Color],
        ];
    }

    private static function prescriptionFields(): array
    {
        return [
            ['key' => 'prescription.header', 'label' => 'Prescription Header', 'type' => SettingType::Textarea],
            ['key' => 'prescription.footer', 'label' => 'Prescription Footer', 'type' => SettingType::Textarea],
            ['key' => 'prescription.default_instructions', 'label' => 'Default Instructions', 'type' => SettingType::Textarea],
        ];
    }

    private static function invoiceFields(): array
    {
        return [
            ['key' => 'invoice.prefix', 'label' => 'Invoice Prefix', 'type' => SettingType::String],
            ['key' => 'invoice.footer', 'label' => 'Invoice Footer', 'type' => SettingType::Textarea],
            ['key' => 'invoice.payment_terms', 'label' => 'Payment Terms', 'type' => SettingType::String],
        ];
    }

    private static function appointmentFields(): array
    {
        return [
            ['key' => 'appointment.default_duration', 'label' => 'Default Appointment Duration (minutes)', 'type' => SettingType::Integer],
            ['key' => 'appointment.allow_walk_ins', 'label' => 'Allow Walk-in Appointments', 'type' => SettingType::Boolean],
            ['key' => 'appointment.allow_double_booking', 'label' => 'Allow Double Booking', 'type' => SettingType::Boolean],
            ['key' => 'appointment.booking_interval', 'label' => 'Booking Interval (minutes)', 'type' => SettingType::Integer],
        ];
    }

    private static function notificationFields(): array
    {
        return [
            ['key' => 'notification.appointment_reminders', 'label' => 'Appointment Reminders', 'type' => SettingType::Boolean],
            ['key' => 'notification.payment_reminders', 'label' => 'Payment Reminders', 'type' => SettingType::Boolean],
            ['key' => 'notification.follow_up_reminders', 'label' => 'Follow-up Reminders', 'type' => SettingType::Boolean],
        ];
    }

    private static function localizationFields(): array
    {
        return [
            ['key' => 'localization.language', 'label' => 'Language', 'type' => SettingType::String, 'options' => ['en' => 'English']],
            ['key' => 'localization.currency', 'label' => 'Currency', 'type' => SettingType::String, 'searchable' => true, 'options' => self::currencies()],
            ['key' => 'localization.timezone', 'label' => 'Timezone', 'type' => SettingType::String, 'searchable' => true, 'options' => self::timezones()],
            ['key' => 'localization.date_format', 'label' => 'Date Format', 'type' => SettingType::String],
            ['key' => 'localization.time_format', 'label' => 'Time Format', 'type' => SettingType::String],
        ];
    }

    private static function integrationFields(): array
    {
        return [
            ['key' => 'integration.email_host', 'label' => 'SMTP Host', 'type' => SettingType::String],
            ['key' => 'integration.email_port', 'label' => 'SMTP Port', 'type' => SettingType::Integer],
            ['key' => 'integration.email_username', 'label' => 'SMTP Username', 'type' => SettingType::String],
            ['key' => 'integration.email_password', 'label' => 'SMTP Password', 'type' => SettingType::String, 'encrypted' => true],
            ['key' => 'integration.email_encryption', 'label' => 'SMTP Encryption', 'type' => SettingType::String, 'options' => ['tls' => 'TLS', 'ssl' => 'SSL', '' => 'None']],
            ['key' => 'integration.sms_provider', 'label' => 'SMS Provider', 'type' => SettingType::String],
            ['key' => 'integration.sms_api_key', 'label' => 'SMS API Key', 'type' => SettingType::String, 'encrypted' => true],
            ['key' => 'integration.sms_sender_id', 'label' => 'SMS Sender ID', 'type' => SettingType::String],
            ['key' => 'integration.whatsapp_provider', 'label' => 'WhatsApp Provider', 'type' => SettingType::String],
            ['key' => 'integration.whatsapp_api_token', 'label' => 'WhatsApp API Token', 'type' => SettingType::String, 'encrypted' => true],
            ['key' => 'integration.payment_provider', 'label' => 'Payment Provider', 'type' => SettingType::String],
            ['key' => 'integration.payment_secret', 'label' => 'Payment Secret', 'type' => SettingType::String, 'encrypted' => true],
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function currencies(): array
    {
        return [
            'AED' => 'UAE Dirham (AED)',
            'AFN' => 'Afghan Afghani (AFN)',
            'ALL' => 'Albanian Lek (ALL)',
            'AMD' => 'Armenian Dram (AMD)',
            'ANG' => 'Netherlands Antillean Guilder (ANG)',
            'AOA' => 'Angolan Kwanza (AOA)',
            'ARS' => 'Argentine Peso (ARS)',
            'AUD' => 'Australian Dollar (AUD)',
            'AWG' => 'Aruban Florin (AWG)',
            'AZN' => 'Azerbaijani Manat (AZN)',
            'BAM' => 'Bosnia-Herzegovina Convertible Mark (BAM)',
            'BBD' => 'Barbadian Dollar (BBD)',
            'BDT' => 'Bangladeshi Taka (BDT)',
            'BGN' => 'Bulgarian Lev (BGN)',
            'BHD' => 'Bahraini Dinar (BHD)',
            'BIF' => 'Burundian Franc (BIF)',
            'BMD' => 'Bermudian Dollar (BMD)',
            'BND' => 'Brunei Dollar (BND)',
            'BOB' => 'Bolivian Boliviano (BOB)',
            'BRL' => 'Brazilian Real (BRL)',
            'BSD' => 'Bahamian Dollar (BSD)',
            'BTN' => 'Bhutanese Ngultrum (BTN)',
            'BWP' => 'Botswana Pula (BWP)',
            'BYN' => 'Belarusian Ruble (BYN)',
            'BZD' => 'Belize Dollar (BZD)',
            'CAD' => 'Canadian Dollar (CAD)',
            'CDF' => 'Congolese Franc (CDF)',
            'CHF' => 'Swiss Franc (CHF)',
            'CLP' => 'Chilean Peso (CLP)',
            'CNY' => 'Chinese Yuan (CNY)',
            'COP' => 'Colombian Peso (COP)',
            'CRC' => 'Costa Rican Colón (CRC)',
            'CUP' => 'Cuban Peso (CUP)',
            'CVE' => 'Cape Verdean Escudo (CVE)',
            'CZK' => 'Czech Koruna (CZK)',
            'DJF' => 'Djiboutian Franc (DJF)',
            'DKK' => 'Danish Krone (DKK)',
            'DOP' => 'Dominican Peso (DOP)',
            'DZD' => 'Algerian Dinar (DZD)',
            'EGP' => 'Egyptian Pound (EGP)',
            'ERN' => 'Eritrean Nakfa (ERN)',
            'ETB' => 'Ethiopian Birr (ETB)',
            'EUR' => 'Euro (EUR)',
            'FJD' => 'Fijian Dollar (FJD)',
            'FKP' => 'Falkland Islands Pound (FKP)',
            'GBP' => 'British Pound Sterling (GBP)',
            'GEL' => 'Georgian Lari (GEL)',
            'GHS' => 'Ghanaian Cedi (GHS)',
            'GIP' => 'Gibraltar Pound (GIP)',
            'GMD' => 'Gambian Dalasi (GMD)',
            'GNF' => 'Guinean Franc (GNF)',
            'GTQ' => 'Guatemalan Quetzal (GTQ)',
            'GYD' => 'Guyanese Dollar (GYD)',
            'HKD' => 'Hong Kong Dollar (HKD)',
            'HNL' => 'Honduran Lempira (HNL)',
            'HRK' => 'Croatian Kuna (HRK)',
            'HTG' => 'Haitian Gourde (HTG)',
            'HUF' => 'Hungarian Forint (HUF)',
            'IDR' => 'Indonesian Rupiah (IDR)',
            'ILS' => 'Israeli New Shekel (ILS)',
            'INR' => 'Indian Rupee (INR)',
            'IQD' => 'Iraqi Dinar (IQD)',
            'IRR' => 'Iranian Rial (IRR)',
            'ISK' => 'Icelandic Króna (ISK)',
            'JMD' => 'Jamaican Dollar (JMD)',
            'JOD' => 'Jordanian Dinar (JOD)',
            'JPY' => 'Japanese Yen (JPY)',
            'KES' => 'Kenyan Shilling (KES)',
            'KGS' => 'Kyrgyzstani Som (KGS)',
            'KHR' => 'Cambodian Riel (KHR)',
            'KMF' => 'Comorian Franc (KMF)',
            'KRW' => 'South Korean Won (KRW)',
            'KWD' => 'Kuwaiti Dinar (KWD)',
            'KYD' => 'Cayman Islands Dollar (KYD)',
            'KZT' => 'Kazakhstani Tenge (KZT)',
            'LAK' => 'Lao Kip (LAK)',
            'LBP' => 'Lebanese Pound (LBP)',
            'LKR' => 'Sri Lankan Rupee (LKR)',
            'LRD' => 'Liberian Dollar (LRD)',
            'LSL' => 'Lesotho Loti (LSL)',
            'LYD' => 'Libyan Dinar (LYD)',
            'MAD' => 'Moroccan Dirham (MAD)',
            'MDL' => 'Moldovan Leu (MDL)',
            'MGA' => 'Malagasy Ariary (MGA)',
            'MKD' => 'Macedonian Denar (MKD)',
            'MMK' => 'Myanmar Kyat (MMK)',
            'MNT' => 'Mongolian Tögrög (MNT)',
            'MOP' => 'Macanese Pataca (MOP)',
            'MRU' => 'Mauritanian Ouguiya (MRU)',
            'MUR' => 'Mauritian Rupee (MUR)',
            'MVR' => 'Maldivian Rufiyaa (MVR)',
            'MWK' => 'Malawian Kwacha (MWK)',
            'MXN' => 'Mexican Peso (MXN)',
            'MYR' => 'Malaysian Ringgit (MYR)',
            'MZN' => 'Mozambican Metical (MZN)',
            'NAD' => 'Namibian Dollar (NAD)',
            'NGN' => 'Nigerian Naira (NGN)',
            'NIO' => 'Nicaraguan Córdoba (NIO)',
            'NOK' => 'Norwegian Krone (NOK)',
            'NPR' => 'Nepalese Rupee (NPR)',
            'NZD' => 'New Zealand Dollar (NZD)',
            'OMR' => 'Omani Rial (OMR)',
            'PAB' => 'Panamanian Balboa (PAB)',
            'PEN' => 'Peruvian Sol (PEN)',
            'PGK' => 'Papua New Guinean Kina (PGK)',
            'PHP' => 'Philippine Peso (PHP)',
            'PKR' => 'Pakistani Rupee (PKR)',
            'PLN' => 'Polish Złoty (PLN)',
            'PYG' => 'Paraguayan Guaraní (PYG)',
            'QAR' => 'Qatari Riyal (QAR)',
            'RON' => 'Romanian Leu (RON)',
            'RSD' => 'Serbian Dinar (RSD)',
            'RUB' => 'Russian Ruble (RUB)',
            'RWF' => 'Rwandan Franc (RWF)',
            'SAR' => 'Saudi Riyal (SAR)',
            'SBD' => 'Solomon Islands Dollar (SBD)',
            'SCR' => 'Seychellois Rupee (SCR)',
            'SDG' => 'Sudanese Pound (SDG)',
            'SEK' => 'Swedish Krona (SEK)',
            'SGD' => 'Singapore Dollar (SGD)',
            'SHP' => 'Saint Helena Pound (SHP)',
            'SLL' => 'Sierra Leonean Leone (SLL)',
            'SOS' => 'Somali Shilling (SOS)',
            'SRD' => 'Surinamese Dollar (SRD)',
            'SSP' => 'South Sudanese Pound (SSP)',
            'STN' => 'São Tomé and Príncipe Dobra (STN)',
            'SYP' => 'Syrian Pound (SYP)',
            'SZL' => 'Swazi Lilangeni (SZL)',
            'THB' => 'Thai Baht (THB)',
            'TJS' => 'Tajikistani Somoni (TJS)',
            'TMT' => 'Turkmenistani Manat (TMT)',
            'TND' => 'Tunisian Dinar (TND)',
            'TOP' => 'Tongan Paʻanga (TOP)',
            'TRY' => 'Turkish Lira (TRY)',
            'TTD' => 'Trinidad and Tobago Dollar (TTD)',
            'TWD' => 'New Taiwan Dollar (TWD)',
            'TZS' => 'Tanzanian Shilling (TZS)',
            'UAH' => 'Ukrainian Hryvnia (UAH)',
            'UGX' => 'Ugandan Shilling (UGX)',
            'USD' => 'US Dollar (USD)',
            'UYU' => 'Uruguayan Peso (UYU)',
            'UZS' => 'Uzbekistani Som (UZS)',
            'VES' => 'Venezuelan Bolívar (VES)',
            'VND' => 'Vietnamese Đồng (VND)',
            'VUV' => 'Vanuatu Vatu (VUV)',
            'WST' => 'Samoan Tala (WST)',
            'XAF' => 'Central African CFA Franc (XAF)',
            'XCD' => 'East Caribbean Dollar (XCD)',
            'XOF' => 'West African CFA Franc (XOF)',
            'XPF' => 'CFP Franc (XPF)',
            'YER' => 'Yemeni Rial (YER)',
            'ZAR' => 'South African Rand (ZAR)',
            'ZMW' => 'Zambian Kwacha (ZMW)',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function timezones(): array
    {
        return collect(\DateTimeZone::listIdentifiers())
            ->mapWithKeys(fn (string $tz): array => [$tz => $tz])
            ->all();
    }
}
