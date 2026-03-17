<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sincronizaciones_api', function (Blueprint $table): void {
            $table->id();
            $table->string('modulo', 80)->default('estado_cuenta');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('estado', 30)->default('pendiente');
            $table->unsignedInteger('total_registros')->default(0);
            $table->unsignedInteger('procesados')->default(0);
            $table->unsignedInteger('fallidos')->default(0);
            $table->json('request_payload')->nullable();
            $table->json('response_summary')->nullable();
            $table->longText('log_error')->nullable();
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sincronizaciones_api');
    }
};
