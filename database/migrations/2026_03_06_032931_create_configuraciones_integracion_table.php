<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('configuraciones_integracion', function (Blueprint $table): void {
            $table->id();
            $table->string('modulo', 80)->default('estado_cuenta');
            $table->string('fuente_activa', 20)->default('excel');
            $table->string('api_base_url', 255)->nullable();
            $table->string('api_token', 255)->nullable();
            $table->string('api_username', 120)->nullable();
            $table->string('api_password', 120)->nullable();
            $table->unsignedInteger('api_timeout')->default(15);
            $table->json('opciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['modulo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuraciones_integracion');
    }
};
