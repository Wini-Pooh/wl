@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Диагностика AJAX запросов</h1>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Тест загрузки документов</h3>
                </div>
                <div class="card-body">
                    <button id="testDocuments" class="btn btn-primary">Тестировать загрузку документов</button>
                    <div id="result" class="mt-3"></div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Информация о запросе</h3>
                </div>
                <div class="card-body">
                    <p><strong>CSRF Token:</strong> <span id="csrfToken"></span></p>
                    <p><strong>Route URL:</strong> {{ route('documents.index') }}</p>
                    <p><strong>User:</strong> {{ Auth::user()->name ?? 'Not authenticated' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Показываем информацию о CSRF токене
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    document.getElementById('csrfToken').textContent = csrfToken ? 'Найден' : 'НЕ НАЙДЕН';
    
    // Тест загрузки документов
    document.getElementById('testDocuments').addEventListener('click', function() {
        const resultDiv = document.getElementById('result');
        resultDiv.innerHTML = '<div class="alert alert-info">Отправляем запрос...</div>';
        
        const url = new URL('{{ route('documents.index') }}');
        url.searchParams.set('tab', 'created');
        
        console.log('Отправляем запрос на:', url.toString());
        console.log('CSRF Token:', csrfToken);
        
        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Response Status:', response.status);
            console.log('Response Headers:', Object.fromEntries(response.headers.entries()));
            
            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            // Проверяем тип контента
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return response.text().then(text => {
                    throw new Error('Сервер вернул не JSON: ' + text.substring(0, 200));
                });
            }
        })
        .then(data => {
            console.log('Success:', data);
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <h4>Успешный ответ!</h4>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h4>Ошибка!</h4>
                    <p>${error.message}</p>
                </div>
            `;
        });
    });
});
</script>
@endsection
