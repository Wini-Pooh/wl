@extends('layouts.auth')

@section('content')
    <div class="auth-brand">
        <h1><i class="bi bi-building-gear me-2"></i>{{ config('app.name', 'REM System') }}</h1>
        <p>Создание нового пароля</p>
    </div>

    <div class="auth-form">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email" class="form-label">Email адрес</label>
                <input id="email" type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       name="email" 
                       value="{{ $email ?? old('email') }}" 
                       required 
                       autocomplete="email" 
                       autofocus
                       readonly>

                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Новый пароль</label>
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
                <i class="bi bi-check-circle me-2"></i>Сбросить пароль
            </button>

            <div class="auth-links">
                <a href="{{ route('login') }}">
                    Вернуться ко входу
                </a>
            </div>
        </form>
    </div>
@endsection

@section('image-content')
    <h2>Новый пароль</h2>
    <p>Создайте новый надежный пароль для вашего аккаунта.</p>
    <div class="mt-4">
        <i class="bi bi-shield-check" style="font-size: 4rem; opacity: 0.7;"></i>
    </div>
@endsection
