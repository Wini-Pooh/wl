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
            Schema::table('project_photos', function (Blueprint $table) {
                // Поля для поддержки оптимизации изображений
                $table->bigInteger('original_file_size')->nullable()->after('file_size')->comment('Оригинальный размер файла до оптимизации');
                $table->boolean('is_optimized')->default(false)->after('file_hash')->comment('Было ли изображение оптимизировано');
                $table->json('optimization_data')->nullable()->after('is_optimized')->comment('Данные об оптимизации (миниатюры, сжатие и т.д.)');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('project_photos', function (Blueprint $table) {
                $table->dropColumn(['original_file_size', 'is_optimized', 'optimization_data']);
            });
        }
    };
