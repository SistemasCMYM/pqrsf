<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('estado_cuenta_resumen', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('importacion_excel_id')->nullable();
            $table->string('fuente_datos', 20)->default('excel');
            $table->string('id_asesor', 40)->nullable();
            $table->string('cedula', 30)->index();
            $table->string('nombre_asesor', 180);
            $table->decimal('anticipos_adiciones', 15, 2)->default(0);
            $table->decimal('legalizado_devoluciones', 15, 2)->default(0);
            $table->decimal('sin_legalizar', 15, 2)->default(0);
            $table->string('estado_saldo', 120)->nullable();
            $table->decimal('valor_liquidacion', 15, 2)->default(0);
            $table->date('fecha_retiro')->nullable();
            $table->string('estado_actual', 60)->nullable();
            $table->decimal('anticipos_solicitado_anio', 15, 2)->default(0);
            $table->decimal('total_consignar', 15, 2)->default(0);
            $table->unsignedSmallInteger('anio')->default(2026);
            $table->timestamps();

            $table->unique(['cedula', 'anio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estado_cuenta_resumen');
    }
};
