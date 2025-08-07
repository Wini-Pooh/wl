<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Используем прямой SQL для более точного контроля
        DB::statement('ALTER TABLE signature_requests ADD CONSTRAINT signature_requests_document_id_foreign FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем внешний ключ
        DB::statement('ALTER TABLE signature_requests DROP FOREIGN KEY signature_requests_document_id_foreign');
    }
};
