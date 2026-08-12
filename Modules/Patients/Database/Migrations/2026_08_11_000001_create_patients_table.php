<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('clinic_id')->nullable()->constrained()->nullOnDelete();
            $table->string('patient_number', 30)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('gender', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('whatsapp', 40)->nullable();
            $table->string('email')->nullable();
            $table->string('cnic', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('city', 120)->nullable();
            $table->string('country', 120)->nullable();
            $table->string('emergency_contact', 100)->nullable();
            $table->string('emergency_contact_phone', 40)->nullable();
            $table->string('occupation')->nullable();
            $table->string('marital_status', 20)->nullable();
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['clinic_id', 'last_name', 'first_name']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
