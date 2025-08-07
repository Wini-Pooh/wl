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
        Schema::create('document_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('permission'); // view, edit, delete, sign, share, download
            $table->boolean('granted')->default(true); // Разрешено/запрещено
            $table->timestamp('granted_at')->nullable(); // Когда предоставлено
            $table->foreignId('granted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('expires_at')->nullable(); // Срок действия разрешения
            $table->text('notes')->nullable(); // Заметки к разрешению
            $table->timestamps();
            
            // Индексы для оптимизации запросов
            $table->index('document_id');
            $table->index('user_id');
            $table->index('permission');
            $table->index('granted');
            $table->index('expires_at');
            $table->index('granted_by');
            
            // Уникальный индекс для предотвращения дублирования разрешений
            $table->unique(['document_id', 'user_id', 'permission']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_permissions');
    }
};
