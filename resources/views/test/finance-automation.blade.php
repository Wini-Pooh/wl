@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Тест автообновления финансовых показателей</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6>Как работает система:</h6>
                        <ul>
                            <li><strong>Стоимость работ</strong> - автоматически рассчитывается из смет типа "main"</li>
                            <li><strong>Стоимость материалов</strong> - автоматически рассчитывается из смет типа "materials"</li>
                            <li><strong>Дополнительные работы</strong> - автоматически рассчитывается из смет типа "additional"</li>
                            <li><strong>Общая стоимость</strong> - сумма всех трех показателей</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <strong>Важно:</strong> Финансовые поля в проекте доступны только для чтения. 
                        Они обновляются автоматически при сохранении смет.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h6>Пример обновления:</h6>
                            <ol>
                                <li>Создайте проект</li>
                                <li>Создайте смету основных работ (тип "main") с итоговой суммой 100,000 ₽</li>
                                <li>Создайте смету материалов (тип "materials") с итоговой суммой 50,000 ₽</li>
                                <li>Создайте смету дополнительных работ (тип "additional") с итоговой суммой 20,000 ₽</li>
                                <li>Проверьте, что в проекте:</li>
                                <ul>
                                    <li>Стоимость работ = 100,000 ₽</li>
                                    <li>Стоимость материалов = 50,000 ₽</li>
                                    <li>Дополнительные работы = 20,000 ₽</li>
                                    <li>Общая стоимость = 170,000 ₽</li>
                                </ul>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <a href="{{ route('partner.projects.index') }}" class="btn btn-primary">
                                <i class="bi bi-folder me-2"></i>
                                Перейти к проектам
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('partner.estimates.index') }}" class="btn btn-success">
                                <i class="bi bi-calculator me-2"></i>
                                Перейти к сметам
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
