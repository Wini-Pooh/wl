<?php

use Illuminate\Support\Facades\Route;

// Маршруты для админ-панели (доступны только для пользователей с ролью admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Управление пользователями и их ролями
    Route::get('/users', [App\Http\Controllers\Admin\UserRoleController::class, 'index'])->name('users.index');
    Route::get('/users/{id}/edit', [App\Http\Controllers\Admin\UserRoleController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [App\Http\Controllers\Admin\UserRoleController::class, 'update'])->name('users.update');
});
