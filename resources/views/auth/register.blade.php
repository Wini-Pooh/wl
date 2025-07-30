@extends('layouts.auth')

@section('head')
    <script src="{{ asset('js/phone-mask.js') }}"></script>
@endsection

@section('content')
    <div class="auth-brand">
        <h1><i class="bi bi-building-gear me-2"></i>{{ config('app.name', 'REM System') }}</h1>
        <p>Создайте аккаунт для работы с проектами</p>
    </div>

    <div class="auth-form">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Имя</label>
                <input id="name" type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       autocomplete="name" 
                       autofocus
                       placeholder="Введите ваше имя">

                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Номер телефона</label>
                <input id="phone" type="tel" 
                       class="form-control @error('phone') is-invalid @enderror" 
                       name="phone" 
                       value="{{ old('phone') }}" 
                       required 
                       autocomplete="tel"
                       placeholder="+7 (999) 123-45-67">

                @error('phone')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email <span class="text-muted">(необязательно)</span></label>
                <input id="email" type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       name="email" 
                       value="{{ old('email') }}" 
                       autocomplete="email"
                       placeholder="example@mail.com">

                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Пароль</label>
                <input id="password" type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       name="password" 
                       required 
                       autocomplete="new-password"
                       placeholder="Минимум 8 символов">

                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password-confirm" class="form-label">Подтверждение пароля</label>
                <input id="password-confirm" type="password" 
                       class="form-control" 
                       name="password_confirmation" 
                       required 
                       autocomplete="new-password"
                       placeholder="Повторите пароль">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i>Зарегистрироваться
            </button>

            <div class="auth-links">
                <span class="text-muted">Уже есть аккаунт?</span>
                <a href="{{ route('login') }}" class="ms-1">
                    Войти
                </a>
            </div>
        </form>
    </div>
@endsection

@section('image-content')
    <h2>Присоединяйтесь к нам!</h2>
    <p>Создайте аккаунт и начните эффективно управлять своими ремонтными проектами уже сегодня.</p>
    <div class="mt-4">
        <i class="bi bi-house-gear" style="font-size: 4rem; opacity: 0.7;"></i>
    </div>
@endsection