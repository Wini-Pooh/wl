@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Тестирование API сотрудников</h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>API сотрудников (EmployeeController)</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary" onclick="testEmployeesAPI()">Загрузить через /partner/employees/api</button>
                    <div id="employees-api-result" class="mt-3"></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>API назначаемых сотрудников (ProjectController)</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-success" onclick="testAssignableAPI()">Загрузить через /partner/projects/assignable-employees</button>
                    <div id="assignable-api-result" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testEmployeesAPI() {
    const resultDiv = document.getElementById('employees-api-result');
    resultDiv.innerHTML = '<div class="spinner-border"></div> Загрузка...';
    
    fetch('/partner/employees/api', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
    })
    .catch(error => {
        resultDiv.innerHTML = '<div class="alert alert-danger">Ошибка: ' + error.message + '</div>';
    });
}

function testAssignableAPI() {
    const resultDiv = document.getElementById('assignable-api-result');
    resultDiv.innerHTML = '<div class="spinner-border"></div> Загрузка...';
    
    fetch('/partner/projects/assignable-employees', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
    })
    .catch(error => {
        resultDiv.innerHTML = '<div class="alert alert-danger">Ошибка: ' + error.message + '</div>';
    });
}
</script>
@endsection
