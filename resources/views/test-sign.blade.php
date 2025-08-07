<!DOCTYPE html>
<html>
<head>
    <title>Тест подписи документов</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Тест подписи документов</h1>
        
        <div class="card">
            <div class="card-body">
                <h5>Информация о пользователе</h5>
                <p>ID: {{ auth()->id() }}</p>
                <p>Имя: {{ auth()->user()->name ?? 'Не указано' }}</p>
                <p>Email: {{ auth()->user()->email ?? 'Не указан' }}</p>
                <p>Телефон: {{ auth()->user()->phone ?? 'Не указан' }}</p>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <h5>Тестовая форма подписи</h5>
                <form id="testSignForm">
                    <div class="mb-3">
                        <label for="documentId" class="form-label">ID документа</label>
                        <input type="number" class="form-control" id="documentId" value="5">
                    </div>
                    <div class="mb-3">
                        <label for="signature" class="form-label">Подпись</label>
                        <input type="text" class="form-control" id="signature" value="Тестовая подпись">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agreement" checked>
                            <label class="form-check-label" for="agreement">
                                Согласен с условиями
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Подписать документ</button>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <h5>Результат</h5>
                <pre id="result"></pre>
            </div>
        </div>
    </div>
    
    <script>
    document.getElementById('testSignForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const documentId = document.getElementById('documentId').value;
        const signature = document.getElementById('signature').value;
        const agreement = document.getElementById('agreement').checked;
        
        const resultDiv = document.getElementById('result');
        resultDiv.textContent = 'Отправляем запрос...';
        
        fetch(`/documents/${documentId}/sign`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                signature: signature,
                agreement: agreement
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);
            
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    throw new Error(`Сервер вернул HTML вместо JSON: ${text.substring(0, 200)}...`);
                });
            }
            
            return response.json();
        })
        .then(data => {
            resultDiv.textContent = JSON.stringify(data, null, 2);
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.textContent = 'Ошибка: ' + error.message;
        });
    });
    </script>
</body>
</html>
