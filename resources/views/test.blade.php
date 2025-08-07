<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест системы документооборота</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h2 class="mb-0"><i class="fas fa-check-circle"></i> Тест успешен</h2>
                    </div>
                    <div class="card-body">
                        <h4>{{ $message }}</h4>
                        <p class="text-muted">Время: {{ $timestamp }}</p>
                        
                        <div class="mt-4">
                            <h5>Доступные функции:</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <a href="{{ route('documents.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Создать документ
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('documents.index') }}" class="btn btn-info">
                                        <i class="fas fa-list"></i> Список документов
                                    </a>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <h6>Статус системы:</h6>
                            <div class="alert alert-info">
                                <strong>Режим:</strong> Тестирование без базы данных<br>
                                <strong>Аутентификация:</strong> Используется мок-пользователь<br>
                                <strong>Данные:</strong> Используются фиктивные данные
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
</body>
</html>
