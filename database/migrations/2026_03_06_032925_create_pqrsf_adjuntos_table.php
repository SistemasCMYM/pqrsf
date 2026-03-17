<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pqrsf_adjuntos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pqrsf_id')->constrained('pqrsf')->cascadeOnDelete();
            $table->foreignId('cargado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nombre_original', 255);
            $table->string('ruta', 255);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('tamano')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pqrsf_adjuntos');
    }
};
