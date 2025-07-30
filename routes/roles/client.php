<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\ProjectController;

// Маршруты для клиентов (доступны для пользователей с ролью client или admin)
Route::middleware(['auth', 'role:client,admin'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', function () {
        return view('home'); // Временно используем домашнюю страницу
    })->name('dashboard');
    
    // Маршруты для просмотра проектов клиентом (только чтение)
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
});

// Примечание: Клиенты теперь могут также использовать партнерские роуты /partner/projects
// с автоматической фильтрацией по номеру телефона
