<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_settings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('clinic_id')->constrained()->cascadeOnDelete();
            $table->string('group', 60);
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('type', 30)->default('string');
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();

            $table->unique(['clinic_id', 'key']);
            $table->index(['clinic_id', 'group']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_settings');
    }
};
