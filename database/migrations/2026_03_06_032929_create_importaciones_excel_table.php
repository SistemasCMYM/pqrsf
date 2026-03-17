<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('importaciones_excel', function (Blueprint $table): void {
            $table->id();
            $table->string('modulo', 80)->default('estado_cuenta');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nombre_archivo', 255);
            $table->string('ruta_archivo', 255);
            $table->string('estado', 30)->default('procesando');
            $table->unsignedInteger('total_registros')->default(0);
            $table->unsignedInteger('procesados')->default(0);
            $table->unsignedInteger('fallidos')->default(0);
            $table->longText('log_error')->nullable();
            $table->timestamp('fecha_importacion')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('importaciones_excel');
    }
};
