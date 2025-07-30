@extends('layouts.auth')

@section('content')
    <div class="auth-brand">
        <h1><i class="bi bi-building-gear me-2"></i>{{ config('app.name', 'REM System') }}</h1>
        <p>Восстановление пароля</p>
    </div>

    <div class="auth-form">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email адрес</label>
                <input id="email" type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autocomplete="email" 
                       autofocus
                       placeholder="Введите ваш email">

                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-envelope me-2"></i>Отправить ссылку восстановления
            </button>

            <div class="auth-links">
                <span class="text-muted">Вспомнили пароль?</span>
                <a href="{{ route('login') }}" class="ms-1">
                    Войти
                </a>
            </div>
        </form>
    </div>
@endsection

@section('image-content')
    <h2>Восстановление пароля</h2>
    <p>Введите ваш email адрес и мы отправим вам ссылку для восстановления пароля.</p>
    <div class="mt-4">
        <i class="bi bi-key" style="font-size: 4rem; opacity: 0.7;"></i>
    </div>
@endsection
