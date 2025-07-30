@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2>Проверка ролей пользователя</h2>
            
            @if(Auth::check())
                <div class="card">
                    <div class="card-body">
                        <h5>Информация о пользователе: {{ Auth::user()->name }}</h5>
                        
                        <h6>Роли:</h6>
                        <ul>
                            <li>isAdmin(): {{ Auth::user()->isAdmin() ? 'true' : 'false' }}</li>
                            <li>isPartner(): {{ Auth::user()->isPartner() ? 'true' : 'false' }}</li>
                            <li>isEmployee(): {{ Auth::user()->isEmployee() ? 'true' : 'false' }}</li>
                            <li>isForeman(): {{ Auth::user()->isForeman() ? 'true' : 'false' }}</li>
                            <li>isEstimator(): {{ Auth::user()->isEstimator() ? 'true' : 'false' }}</li>
                            <li>isClient(): {{ Auth::user()->isClient() ? 'true' : 'false' }}</li>
                        </ul>

                        <h6>Роль по умолчанию:</h6>
                        <p>{{ Auth::user()->defaultRole ? Auth::user()->defaultRole->name : 'Нет' }}</p>

                        <h6>Дополнительные роли:</h6>
                        <ul>
                            @foreach(Auth::user()->roles as $role)
                                <li>{{ $role->name }}</li>
                            @endforeach
                        </ul>

                        <h6>hasRole() тесты:</h6>
                        <ul>
                            <li>hasRole('admin'): {{ Auth::user()->hasRole('admin') ? 'true' : 'false' }}</li>
                            <li>hasRole('partner'): {{ Auth::user()->hasRole('partner') ? 'true' : 'false' }}</li>
                            <li>hasRole('employee'): {{ Auth::user()->hasRole('employee') ? 'true' : 'false' }}</li>
                            <li>hasRole('foreman'): {{ Auth::user()->hasRole('foreman') ? 'true' : 'false' }}</li>
                            <li>hasRole('estimator'): {{ Auth::user()->hasRole('estimator') ? 'true' : 'false' }}</li>
                            <li>hasRole('client'): {{ Auth::user()->hasRole('client') ? 'true' : 'false' }}</li>
                        </ul>

                        <h6>Доступы к разделам меню:</h6>
                        <ul>
                            <li>Объекты: {{ (Auth::user()->isAdmin() || Auth::user()->isPartner() || Auth::user()->isEmployee() || Auth::user()->isForeman()) ? 'Да' : 'Нет' }}</li>
                            <li>Сметы: {{ (Auth::user()->isAdmin() || Auth::user()->isPartner() || Auth::user()->isEmployee() || Auth::user()->isEstimator() || Auth::user()->isForeman()) ? 'Да' : 'Нет' }}</li>
                            <li>Сотрудники: {{ (Auth::user()->isPartner() || Auth::user()->isEmployee() || Auth::user()->isAdmin()) ? 'Да' : 'Нет' }}</li>
                        </ul>
                    </div>
                </div>
            @else
                <p>Пользователь не авторизован</p>
            @endif
        </div>
    </div>
</div>
@endsection
