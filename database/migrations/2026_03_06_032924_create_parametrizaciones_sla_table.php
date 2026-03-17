<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parametrizaciones_sla', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pqrsf_tipo_id')->nullable()->constrained('pqrsf_tipos')->nullOnDelete();
            $table->string('prioridad', 20)->default('media');
            $table->unsignedInteger('dias_respuesta');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametrizaciones_sla');
    }
};
