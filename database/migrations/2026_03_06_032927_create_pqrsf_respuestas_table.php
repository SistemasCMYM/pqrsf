<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pqrsf_respuestas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pqrsf_id')->constrained('pqrsf')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo', 30)->default('nota_interna');
            $table->longText('mensaje');
            $table->boolean('notificado')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pqrsf_respuestas');
    }
};
