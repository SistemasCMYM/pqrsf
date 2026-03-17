<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pqrsf_estados', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre', 80);
            $table->string('slug', 80)->unique();
            $table->string('color', 30)->default('gray');
            $table->boolean('es_cierre')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pqrsf_estados');
    }
};
