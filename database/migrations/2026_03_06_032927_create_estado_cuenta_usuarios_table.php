<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('estado_cuenta_usuarios', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('cedula', 30)->index();
            $table->string('nombre_asesor', 180)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'cedula']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estado_cuenta_usuarios');
    }
};
