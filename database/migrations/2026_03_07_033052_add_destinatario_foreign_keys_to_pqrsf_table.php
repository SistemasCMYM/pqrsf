<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('pqrsf_destinatarios')) {
            return;
        }

        Schema::table('pqrsf', function (Blueprint $table): void {
            $table->foreign('destinatario_id')->references('id')->on('pqrsf_destinatarios')->nullOnDelete();
            $table->foreign('destinatario_original_id')->references('id')->on('pqrsf_destinatarios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pqrsf', function (Blueprint $table): void {
            try {
                $table->dropForeign(['destinatario_id']);
            } catch (\Throwable) {
            }

            try {
                $table->dropForeign(['destinatario_original_id']);
            } catch (\Throwable) {
            }
        });
    }
};
