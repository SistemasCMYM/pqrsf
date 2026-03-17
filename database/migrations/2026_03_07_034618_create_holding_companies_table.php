<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('holding_companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 140);
            $table->string('slug', 140)->unique();
            $table->string('tagline', 180)->nullable();
            $table->text('intro')->nullable();
            $table->string('logo_path', 255)->nullable();
            $table->string('animation_path', 255)->nullable();
            $table->string('support_booking_url', 255)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('active')->default(true);
            $table->json('theme')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holding_companies');
    }
};
