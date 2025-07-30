<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DocumentTemplate;
use App\Models\User;

class PrebuiltTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Найдём первого пользователя админа/партнёра для создания системных шаблонов
        $systemUser = User::where('role', 'admin')->first() ?? User::where('role', 'partner')->first();
        
        if (!$systemUser) {
            $this->command->info('Не найден пользователь для создания системных шаблонов. Пропускаем.');
            return;
        }

        $templates = [
            [
                'name' => 'Договор подряда (системный)',
                'description' => 'Готовый шаблон договора подряда от сервиса',
                'category' => 'contract',
                'sort_order' => 1,
                'template_variables' => [
                    ['name' => 'contract_number', 'label' => 'Номер договора', 'placeholder' => '001/2025', 'default' => ''],
                    ['name' => 'contract_date', 'label' => 'Дата договора', 'placeholder' => '22.01.2025', 'default' => ''],
                    ['name' => 'client_name', 'label' => 'Наименование заказчика', 'placeholder' => 'ООО "Название"', 'default' => ''],
                    ['name' => 'total_cost', 'label' => 'Общая стоимость работ', 'placeholder' => '1 000 000', 'default' => ''],
                ],
                'html_content' => '<div class="document-header" style="text-align: center; margin-bottom: 30px;">
    <h1>ДОГОВОР ПОДРЯДА</h1>
    <p>№ {{contract_number}} от {{contract_date}}</p>
</div>

<div class="parties" style="margin-bottom: 20px;">
    <h3>1. СТОРОНЫ</h3>
    <p><strong>Заказчик:</strong> {{client_name}}</p>
    <p><strong>Подрядчик:</strong> [Название вашей компании]</p>
</div>

<div class="cost" style="margin-bottom: 20px;">
    <h3>2. СТОИМОСТЬ</h3>
    <p><strong>Общая стоимость работ:</strong> {{total_cost}} рублей</p>
</div>',
                'css_content' => 'body { font-family: "Times New Roman", serif; margin: 20px; line-height: 1.6; }
h1 { font-size: 18px; font-weight: bold; }
h3 { font-size: 14px; font-weight: bold; margin-top: 20px; }
p { font-size: 12px; margin-bottom: 8px; }'
            ],
            [
                'name' => 'Смета на работы (системная)',
                'description' => 'Готовый шаблон сметы от сервиса',
                'category' => 'estimate',
                'sort_order' => 2,
                'template_variables' => [
                    ['name' => 'estimate_number', 'label' => 'Номер сметы', 'placeholder' => 'СМ-001/2025', 'default' => ''],
                    ['name' => 'client_name', 'label' => 'Заказчик', 'placeholder' => 'ООО "Название"', 'default' => ''],
                ],
                'html_content' => '<div class="document-header" style="text-align: center; margin-bottom: 30px;">
    <h1>СМЕТА НА СТРОИТЕЛЬНЫЕ РАБОТЫ</h1>
    <p>№ {{estimate_number}}</p>
</div>

<div class="project-info" style="margin-bottom: 20px;">
    <p><strong>Заказчик:</strong> {{client_name}}</p>
</div>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #f8f9fa;">
            <th style="border: 1px solid #ddd; padding: 8px;">№</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Наименование работ</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Сумма, руб.</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #ddd; padding: 8px;">1</td>
            <td style="border: 1px solid #ddd; padding: 8px;">Строительные работы</td>
            <td style="border: 1px solid #ddd; padding: 8px;">500 000</td>
        </tr>
    </tbody>
</table>',
                'css_content' => 'body { font-family: "Times New Roman", serif; margin: 20px; line-height: 1.4; }
h1 { font-size: 16px; font-weight: bold; }
table { margin-top: 20px; }
th { background-color: #f8f9fa; font-weight: bold; font-size: 11px; }
td { font-size: 11px; }'
            ]
        ];

        foreach ($templates as $templateData) {
            // Проверяем, не существует ли уже такой шаблон
            $existing = DocumentTemplate::where('name', $templateData['name'])->first();
            if ($existing) {
                $this->command->info("Шаблон '{$templateData['name']}' уже существует, пропускаем.");
                continue;
            }

            DocumentTemplate::create([
                'name' => $templateData['name'],
                'description' => $templateData['description'],
                'category' => $templateData['category'],
                'html_content' => $templateData['html_content'],
                'css_content' => $templateData['css_content'],
                'template_variables' => $templateData['template_variables'],
                'created_by' => $systemUser->id,
                'sort_order' => $templateData['sort_order'],
                'is_active' => true
            ]);

            $this->command->info("Создан шаблон: {$templateData['name']}");
        }
    }
}
