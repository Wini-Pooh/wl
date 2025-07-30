@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Редактирование ролей пользователя') }}</div>

                <div class="card-body">
                    <h5>Пользователь: {{ $user->name }} ({{ $user->email }})</h5>
                    <hr>
                    
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="default_role_id" class="form-label">{{ __('Роль по умолчанию') }}</label>
                            <select class="form-select" id="default_role_id" name="default_role_id">
                                <option value="">{{ __('Нет роли по умолчанию') }}</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->default_role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }} ({{ $role->description }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Дополнительные роли') }}</label>
                            <div class="form-check">
                                @foreach ($roles as $role)
                                    <div class="mb-2">
                                        <input class="form-check-input" type="checkbox" 
                                            name="roles[]" 
                                            value="{{ $role->id }}" 
                                            id="role{{ $role->id }}"
                                            {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role{{ $role->id }}">
                                            {{ $role->name }} ({{ $role->description }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Сохранить изменения') }}
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                {{ __('Отмена') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
