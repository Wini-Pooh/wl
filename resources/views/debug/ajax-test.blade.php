@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2>Тест AJAX запросов для шаблонов документов</h2>
            
            @if(Auth::check())
                <div class="alert alert-info">
                    <strong>Пользователь:</strong> {{ Auth::user()->name ?? 'Неизвестно' }}<br>
                    <strong>Email:</strong> {{ Auth::user()->email }}<br>
                    <strong>Роли:</strong> {{ Auth::user()->roles->pluck('name')->implode(', ') }}<br>
                    <strong>Роль по умолчанию:</strong> {{ Auth::user()->defaultRole->name ?? 'Не установлена' }}
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Тест загрузки сотрудников</h5>
                            </div>
                            <div class="card-body">
                                <button id="testEmployees" class="btn btn-primary">Загрузить сотрудников</button>
                                <div id="employeesResult" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Тест загрузки проектов</h5>
                            </div>
                            <div class="card-body">
                                <button id="testProjects" class="btn btn-primary">Загрузить проекты</button>
                                <div id="projectsResult" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Логи запросов</h5>
                            </div>
                            <div class="card-body">
                                <div id="logOutput" style="height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; font-family: monospace; white-space: pre-wrap;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    Пользователь не авторизован. Пожалуйста, войдите в систему.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const logOutput = document.getElementById('logOutput');
    
    function addLog(message) {
        const timestamp = new Date().toISOString();
        logOutput.textContent += `[${timestamp}] ${message}\n`;
        logOutput.scrollTop = logOutput.scrollHeight;
    }
    
    function displayResult(elementId, data, isError = false) {
        const element = document.getElementById(elementId);
        if (isError) {
            element.innerHTML = `<div class="alert alert-danger">${data}</div>`;
        } else {
            element.innerHTML = `<div class="alert alert-success"><pre>${JSON.stringify(data, null, 2)}</pre></div>`;
        }
    }
    
    // Тест загрузки сотрудников
    document.getElementById('testEmployees').addEventListener('click', function() {
        addLog('Начинаем тест загрузки сотрудников...');
        
        fetch('/partner/document-templates/employees', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            addLog(`Ответ получен. Статус: ${response.status} ${response.statusText}`);
            addLog(`Заголовки ответа: ${JSON.stringify(Object.fromEntries(response.headers.entries()))}`);
            
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            addLog(`Данные получены: ${JSON.stringify(data)}`);
            displayResult('employeesResult', data);
        })
        .catch(error => {
            addLog(`Ошибка: ${error.message}`);
            displayResult('employeesResult', error.message, true);
        });
    });
    
    // Тест загрузки проектов
    document.getElementById('testProjects').addEventListener('click', function() {
        addLog('Начинаем тест загрузки проектов...');
        
        fetch('/partner/document-templates/projects', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            addLog(`Ответ получен. Статус: ${response.status} ${response.statusText}`);
            addLog(`Заголовки ответа: ${JSON.stringify(Object.fromEntries(response.headers.entries()))}`);
            
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            addLog(`Данные получены: ${JSON.stringify(data)}`);
            displayResult('projectsResult', data);
        })
        .catch(error => {
            addLog(`Ошибка: ${error.message}`);
            displayResult('projectsResult', error.message, true);
        });
    });
    
    addLog('Тестовая страница загружена. Готова к тестированию.');
});
</script>
@endpush
