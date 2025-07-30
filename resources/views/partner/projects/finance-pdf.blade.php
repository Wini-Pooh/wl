<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Финансовый отчет - {{ $project->name }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        
        .summary h2 {
            color: #007bff;
            margin-top: 0;
            font-size: 16px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 10px;
        }
        
        .summary-item {
            background: white;
            padding: 10px;
            border-radius: 3px;
            border-left: 4px solid #007bff;
        }
        
        .summary-item h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #333;
        }
        
        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
        }
        
        .summary-item .count {
            font-size: 11px;
            color: #666;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section h2 {
            color: #007bff;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
            border-radius: 3px;
        }
        
        .badge-primary {
            background-color: #007bff;
            color: white;
        }
        
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        /* Стили для печати */
        @media print {
            body {
                margin: 0;
                padding: 15px;
                font-size: 11px;
            }
            
            .header {
                margin-bottom: 20px;
                padding-bottom: 10px;
            }
            
            .summary {
                margin-bottom: 20px;
                padding: 10px;
            }
            
            .section {
                margin-bottom: 20px;
            }
            
            .no-data {
                padding: 10px;
            }
            
            .footer {
                margin-top: 20px;
                padding-top: 10px;
            }
            
            table {
                margin-bottom: 10px;
            }
            
            th, td {
                padding: 6px;
            }
        }
        
        /* Кнопка для печати */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
        
        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Кнопка для печати -->
    <button class="print-button" onclick="window.print()">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 5px;">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
        </svg>
        Печать / Сохранить как PDF
    </button>

    <!-- Заголовок -->
    <div class="header">
        <h1>Финансовый отчет</h1>
        <p><strong>Проект:</strong> {{ $project->name }}</p>
        <p><strong>Адрес:</strong> {{ $project->address ?? 'Не указан' }}</p>
        <p><strong>Дата формирования:</strong> {{ $generated_at }}</p>
    </div>

    <!-- Сводка -->
    <div class="summary">
        <h2>Финансовая сводка</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <h3>Работы</h3>
                <div class="value">{{ $summary['works']['total'] ?? '0 ₽' }}</div>
                <div class="count">{{ $summary['works']['count'] ?? '0' }} позиций</div>
            </div>
            <div class="summary-item">
                <h3>Материалы</h3>
                <div class="value">{{ $summary['materials']['total'] ?? '0 ₽' }}</div>
                <div class="count">{{ $summary['materials']['count'] ?? '0' }} позиций</div>
            </div>
            <div class="summary-item">
                <h3>Транспорт</h3>
                <div class="value">{{ $summary['transport']['total'] ?? '0 ₽' }}</div>
                <div class="count">{{ $summary['transport']['count'] ?? '0' }} позиций</div>
            </div>
            <div class="summary-item">
                <h3>Общая сумма</h3>
                <div class="value">{{ $summary['grand_total'] ?? '0 ₽' }}</div>
                <div class="count">Итого по проекту</div>
            </div>
        </div>
    </div>

    <!-- Работы -->
    @if(!empty($works))
    <div class="section">
        <h2>Работы по проекту</h2>
        <table>
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Тип</th>
                    <th>Количество</th>
                    <th>Цена</th>
                    <th>Сумма</th>
                    <th>Оплачено</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                @foreach($works as $work)
                <tr>
                    <td>{{ $work['name'] ?? '-' }}</td>
                    <td>{{ $work['type'] ?? '-' }}</td>
                    <td class="text-center">{{ $work['quantity'] ?? '-' }}</td>
                    <td class="text-right">{{ $work['price'] ?? '-' }}</td>
                    <td class="text-right">{{ $work['amount'] ?? '-' }}</td>
                    <td class="text-right">{{ $work['paid'] ?? '-' }}</td>
                    <td class="text-center">{{ $work['status'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="section">
        <h2>Работы по проекту</h2>
        <div class="no-data">Работы не добавлены</div>
    </div>
    @endif

    <!-- Материалы -->
    @if(!empty($materials))
    <div class="section">
        <h2>Материалы для проекта</h2>
        <table>
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Количество</th>
                    <th>Единица</th>
                    <th>Цена за ед.</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materials as $material)
                <tr>
                    <td>{{ $material['name'] ?? '-' }}</td>
                    <td class="text-center">{{ $material['quantity'] ?? '-' }}</td>
                    <td class="text-center">{{ $material['unit'] ?? '-' }}</td>
                    <td class="text-right">{{ $material['price'] ?? '-' }}</td>
                    <td class="text-right">{{ $material['amount'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="section">
        <h2>Материалы для проекта</h2>
        <div class="no-data">Материалы не добавлены</div>
    </div>
    @endif

    <!-- Транспорт -->
    @if(!empty($transports))
    <div class="section">
        <h2>Транспорт для проекта</h2>
        <table>
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Сумма</th>
                    <th>Оплачено</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transports as $transport)
                <tr>
                    <td>{{ $transport['name'] ?? '-' }}</td>
                    <td class="text-right">{{ $transport['amount'] ?? '-' }}</td>
                    <td class="text-right">{{ $transport['paid'] ?? '-' }}</td>
                    <td class="text-center">{{ $transport['status'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="section">
        <h2>Транспорт для проекта</h2>
        <div class="no-data">Транспорт не добавлен</div>
    </div>
    @endif

    <!-- Подвал -->
    <div class="footer">
        <p>Отчет сформирован автоматически системой управления проектами</p>
        <p>Дата и время формирования: {{ $generated_at }}</p>
    </div>

    <script>
        // Автоматически открыть диалог печати при загрузке страницы
        window.addEventListener('load', function() {
            // Небольшая задержка для корректного отображения
            setTimeout(() => {
                // Фокус на окне для активации
                window.focus();
                // Открыть диалог печати
                window.print();
            }, 500);
        });
        
        // Обработчик для кнопки печати
        document.querySelector('.print-button').addEventListener('click', function() {
            window.print();
        });
    </script>
</body>
</html>
