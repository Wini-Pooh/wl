<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест навигации проекта</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Тест системы навигации проекта</h1>
        
        <div class="alert alert-info">
            <h5>Статус системы навигации проекта:</h5>
            <ul>
                <li>✅ Маршруты созданы с префиксом /page/</li>
                <li>✅ Контроллер имеет все нужные методы</li>
                <li>✅ View файлы созданы для всех страниц</li>
                <li>✅ Базовый layout настроен</li>
                <li>✅ CSS и JavaScript подключены</li>
            </ul>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>Доступные страницы проекта (ID: 12)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Основные страницы:</h6>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <a href="/partner/projects/12/page/main" class="text-decoration-none">
                                    <i class="bi bi-info-circle me-2"></i>Основная информация
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="/partner/projects/12/page/finance" class="text-decoration-none">
                                    <i class="bi bi-cash-coin me-2"></i>Финансы
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="/partner/projects/12/page/schedule" class="text-decoration-none">
                                    <i class="bi bi-calendar3 me-2"></i>График работ
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="/partner/projects/12/page/photos" class="text-decoration-none">
                                    <i class="bi bi-camera me-2"></i>Фотографии
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Файлы и документы:</h6>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <a href="/partner/projects/12/page/design" class="text-decoration-none">
                                    <i class="bi bi-palette me-2"></i>Дизайн
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="/partner/projects/12/page/schemes" class="text-decoration-none">
                                    <i class="bi bi-diagram-3 me-2"></i>Схемы
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="/partner/projects/12/page/documents" class="text-decoration-none">
                                    <i class="bi bi-file-text me-2"></i>Документы
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h5>Инструкция:</h5>
            <ol>
                <li>Нажмите на любую ссылку выше для перехода на соответствующую страницу</li>
                <li>Каждая страница теперь является отдельной страницей с собственным URL</li>
                <li>Навигация между страницами осуществляется через меню в верхней части</li>
                <li>Все AJAX функции и модальные окна работают на каждой странице</li>
            </ol>
        </div>
        
        <div class="alert alert-success mt-4">
            <h6>Что было изменено:</h6>
            <ul class="mb-0">
                <li><strong>Маршруты:</strong> Добавлены отдельные маршруты для каждой вкладки с префиксом <code>/page/</code></li>
                <li><strong>Контроллер:</strong> Каждый метод возвращает отдельную страницу</li>
                <li><strong>Навигация:</strong> Теперь переходы происходят между страницами, а не вкладками</li>
                <li><strong>URL:</strong> Каждая секция имеет свой уникальный URL для прямых ссылок</li>
            </ul>
        </div>
        
        <div class="alert alert-warning mt-4">
            <h6>Для полноценного тестирования:</h6>
            <p>Перейдите по адресу: <strong>https://rem/partner/projects/12/page/main</strong></p>
            <p>Или воспользуйтесь любой из ссылок выше.</p>
        </div>
    </div>
</body>
</html>
