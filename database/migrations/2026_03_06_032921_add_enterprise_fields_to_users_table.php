<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('document_type', 30)->nullable()->after('email');
            $table->string('document_number', 30)->nullable()->unique()->after('document_type');
            $table->string('phone', 30)->nullable()->after('document_number');
            $table->string('city', 120)->nullable()->after('phone');
            $table->boolean('is_external')->default(false)->after('city');
            $table->string('status', 20)->default('active')->after('is_external');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['document_type', 'document_number', 'phone', 'city', 'is_external', 'status']);
        });
    }
};
