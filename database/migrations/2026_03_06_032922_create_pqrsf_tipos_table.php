<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pqrsf_tipos', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre', 80);
            $table->string('slug', 80)->unique();
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('dias_sla')->default(15);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pqrsf_tipos');
    }
};
