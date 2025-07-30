@extends('layouts.auth')

@section('head')
    <script src="{{ asset('js/phone-mask.js') }}"></script>
@endsection

@section('content')
    <div class="auth-brand">
        <h1><i class="bi bi-building-gear me-2"></i>{{ config('app.name', 'REM System') }}</h1>
        <p>Система управления ремонтными проектами</p>
    </div>

    <div class="auth-form">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="phone" class="form-label">Номер телефона</label>
                <input id="phone" type="tel" 
                       class="form-control @error('phone') is-invalid @enderror" 
                       name="phone" 
                       value="{{ old('phone') }}" 
                       required 
                       autocomplete="tel" 
                       autofocus
                       placeholder="+7 (999) 123-45-67">

                @error('phone')
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
                       autocomplete="current-password">

                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    Запомнить меня
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right me-2"></i>Войти
            </button>

            <div class="auth-links">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        Забыли пароль?
                    </a>
                @endif
                
                @if (Route::has('register'))
                    <div class="mt-2">
                        <span class="text-muted">Нет аккаунта?</span>
                        <a href="{{ route('register') }}" class="ms-1">
                            Зарегистрироваться
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
@endsection

@section('image-content')
    <h2>Добро пожаловать!</h2>
    <p>Войдите в свой аккаунт для управления ремонтными проектами, создания смет и контроля финансов.</p>
    <div class="mt-4">
        <i class="bi bi-tools" style="font-size: 4rem; opacity: 0.7;"></i>
    </div>
@endsection
