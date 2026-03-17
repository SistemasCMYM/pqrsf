<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('estado_cuenta_detalle', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('estado_cuenta_resumen_id')->nullable()->constrained('estado_cuenta_resumen')->nullOnDelete();
            $table->unsignedBigInteger('importacion_excel_id')->nullable();
            $table->string('fuente_datos', 20)->default('excel');
            $table->string('cedula', 30)->index();
            $table->string('ciudad_origen', 120)->nullable();
            $table->string('municipio_destino', 120)->nullable();
            $table->date('fecha_ida')->nullable();
            $table->date('fecha_regreso')->nullable();
            $table->date('fecha_pago_anticipo')->nullable();
            $table->unsignedTinyInteger('mes')->nullable();
            $table->unsignedSmallInteger('anio')->default(2026);
            $table->decimal('anticipo', 15, 2)->default(0);
            $table->decimal('legalizado', 15, 2)->default(0);
            $table->decimal('saldo_pendiente', 15, 2)->default(0);
            $table->string('estado', 120)->nullable();
            $table->string('hash_registro', 64)->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estado_cuenta_detalle');
    }
};
