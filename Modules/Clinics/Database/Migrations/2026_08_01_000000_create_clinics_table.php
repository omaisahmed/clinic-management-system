<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('whatsapp', 40)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->string('city', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('country', 120)->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->string('timezone', 60)->default('UTC');
            $table->string('currency', 3)->default('USD');
            $table->string('registration_number')->nullable();
            $table->string('tax_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
