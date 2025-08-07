<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            // Удаляем старый foreign key constraint
            $table->dropForeign(['document_id']);
            
            // Добавляем новый foreign key constraint на правильную таблицу
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            // Откатываем изменения
            $table->dropForeign(['document_id']);
            $table->foreign('document_id')->references('id')->on('project_documents')->onDelete('cascade');
        });
    }
};
