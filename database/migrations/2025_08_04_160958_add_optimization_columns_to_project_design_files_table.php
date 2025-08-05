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
        Schema::table('project_design_files', function (Blueprint $table) {
            $table->bigInteger('original_file_size')->nullable()->after('file_size'); // Оригинальный размер файла до оптимизации
            $table->boolean('is_optimized')->default(false)->after('uploaded_by'); // Флаг оптимизации
            $table->json('optimization_data')->nullable()->after('is_optimized'); // Данные об оптимизации (миниатюры, сжатие и т.д.)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_design_files', function (Blueprint $table) {
            $table->dropColumn(['original_file_size', 'is_optimized', 'optimization_data']);
        });
    }
};
