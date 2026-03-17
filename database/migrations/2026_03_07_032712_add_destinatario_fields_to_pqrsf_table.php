<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pqrsf', function (Blueprint $table): void {
            $table->unsignedBigInteger('destinatario_id')->nullable()->after('pqrsf_tipo_id');
            $table->unsignedBigInteger('destinatario_original_id')->nullable()->after('destinatario_id');
        });
    }

    public function down(): void
    {
        Schema::table('pqrsf', function (Blueprint $table): void {
            if (Schema::hasColumn('pqrsf', 'destinatario_original_id')) {
                $table->dropColumn('destinatario_original_id');
            }
            if (Schema::hasColumn('pqrsf', 'destinatario_id')) {
                $table->dropColumn('destinatario_id');
            }
        });
    }
};
