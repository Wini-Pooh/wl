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
        Schema::table('project_works', function (Blueprint $table) {
            $table->string('unit')->default('шт')->after('type'); // Единица измерения
            $table->decimal('quantity', 10, 3)->default(1)->after('unit'); // Количество
            $table->decimal('price', 12, 2)->default(0)->after('quantity'); // Цена за единицу
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_works', function (Blueprint $table) {
            $table->dropColumn(['unit', 'quantity', 'price']);
        });
    }
};