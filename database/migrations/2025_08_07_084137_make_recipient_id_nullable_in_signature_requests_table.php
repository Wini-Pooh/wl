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
            // Делаем recipient_id nullable для случаев с внешними получателями
            $table->foreignId('recipient_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            // Возвращаем обратно NOT NULL (если нужно)
            $table->foreignId('recipient_id')->nullable(false)->change();
        });
    }
};
