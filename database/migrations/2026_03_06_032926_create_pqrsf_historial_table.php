<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pqrsf_historial', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pqrsf_id')->constrained('pqrsf')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('estado_anterior_id')->nullable()->constrained('pqrsf_estados')->nullOnDelete();
            $table->foreignId('estado_nuevo_id')->nullable()->constrained('pqrsf_estados')->nullOnDelete();
            $table->foreignId('responsable_anterior_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('responsable_nuevo_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accion', 80);
            $table->text('observacion')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pqrsf_historial');
    }
};
