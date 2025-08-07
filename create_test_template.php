<?php

// Создание тестового шаблона
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\DocumentTemplate;

$template = new DocumentTemplate();
$template->name = 'Тестовый договор';
$template->description = 'Простой тестовый шаблон для проверки функционала';
$template->document_type = 'contract';
$template->content = 'ДОГОВОР №@{{contract_number}} от @{{current_date}}

Клиент: @{{client_name}}
Телефон: @{{client_phone}}
Адрес объекта: @{{object_address}}

Общая стоимость: @{{total_cost}} рублей
Условия оплаты: @{{payment_terms}}

Настоящий договор заключен на выполнение работ по адресу: @{{object_address}}.

Дата подписания: @{{current_date}}';
$template->created_by = 1;
$template->is_active = true;
$template->save();

echo "Тестовый шаблон создан с ID: " . $template->id . "\n";
echo "Извлеченные переменные: " . implode(', ', $template->extractVariables()) . "\n";
