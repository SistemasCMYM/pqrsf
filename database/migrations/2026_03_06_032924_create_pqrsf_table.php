<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pqrsf', function (Blueprint $table): void {
            $table->id();
            $table->string('radicado', 40)->unique();
            $table->foreignId('pqrsf_tipo_id')->constrained('pqrsf_tipos');
            $table->foreignId('pqrsf_estado_id')->constrained('pqrsf_estados');
            $table->string('prioridad', 20)->default('media');
            $table->string('canal_ingreso', 80);
            $table->string('nombres', 120);
            $table->string('apellidos', 120);
            $table->string('tipo_documento', 30);
            $table->string('numero_documento', 30);
            $table->string('email', 120);
            $table->string('telefono', 30)->nullable();
            $table->string('ciudad', 120)->nullable();
            $table->string('asunto', 180);
            $table->longText('descripcion');
            $table->boolean('acepta_tratamiento_datos');
            $table->foreignId('asignado_a')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_limite_respuesta')->nullable();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->timestamp('fecha_cierre')->nullable();
            $table->boolean('vencida')->default(false);
            $table->longText('respuesta_final')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['numero_documento', 'created_at']);
            $table->index(['pqrsf_estado_id', 'fecha_limite_respuesta']);
            $table->index(['asignado_a', 'pqrsf_estado_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pqrsf');
    }
};
