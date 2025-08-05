<?php

use Illuminate\Support\Facades\Route;

// Тестовый маршрут для проверки работы частичных данных финансов
Route::middleware(['auth', 'role:partner,employee,foreman,client,admin'])->group(function () {
    Route::prefix('partner/projects/{project}')->name('partner.projects.')->group(function () {
        // Тестовые маршруты для отладки
        Route::get('finance/test-works-partial', function () {
            return response()->json([
                'success' => true,
                'html' => '<div class="alert alert-info">Тестовые данные работ загружены успешно</div>',
                'count' => 0,
                'message' => 'Тестовый ответ - маршрут работает'
            ]);
        })->name('finance.test-works-partial');
        
        Route::get('finance/test-materials-partial', function () {
            return response()->json([
                'success' => true,
                'html' => '<div class="alert alert-info">Тестовые данные материалов загружены успешно</div>',
                'count' => 0,
                'message' => 'Тестовый ответ - маршрут работает'
            ]);
        })->name('finance.test-materials-partial');
        
        Route::get('finance/test-transports-partial', function () {
            return response()->json([
                'success' => true,
                'html' => '<div class="alert alert-info">Тестовые данные транспорта загружены успешно</div>',
                'count' => 0,
                'message' => 'Тестовый ответ - маршрут работает'
            ]);
        })->name('finance.test-transports-partial');
    });
});
