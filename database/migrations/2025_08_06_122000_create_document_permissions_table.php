<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создание таблицы разрешений доступа к документам
     */
    public function up(): void
    {
        Schema::create('document_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('entity_type'); // user, role, project
            $table->unsignedBigInteger('entity_id'); // ID пользователя, роли или проекта
            $table->string('permission'); // view, edit, delete, sign, share
            $table->boolean('granted')->default(true); // Разрешено/запрещено
            $table->timestamp('granted_at')->nullable(); // Когда предоставлено
            $table->foreignId('granted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('expires_at')->nullable(); // Срок действия
            $table->timestamps();
            
            // Индексы
            $table->index('document_id');
            $table->index(['entity_type', 'entity_id']);
            $table->index('permission');
            $table->index('granted');
            $table->index('expires_at');
            $table->unique(['document_id', 'entity_type', 'entity_id', 'permission'], 'doc_permissions_unique');
        });
    }

    /**
     * Откат миграции
     */
    public function down(): void
    {
        Schema::dropIfExists('document_permissions');
    }
};
