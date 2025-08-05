@extends('partner.projects.layouts.project-base')

@section('styles')
@parent
<link href="{{ asset('css/photos-standard.css') }}" rel="stylesheet">
@endsection

@section('page-content')
<div class="container-fluid">
    <!-- Заголовок страницы -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-camera me-2"></i>
            Фотографии проекта 
            <span class="badge bg-primary">{{ $photos->total() }}</span>
        </h4>
        
        @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                <i class="bi bi-plus-lg me-1"></i>
                <span class="d-none d-md-inline">Загрузить фотографии</span>
                <span class="d-md-none">Загрузить</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Форма фильтров -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="bi bi-funnel me-2"></i>
                Фильтры и поиск
                @if(count(array_filter($filters)))
                    <span class="badge bg-info ms-2">Активно: {{ count(array_filter($filters)) }}</span>
                @endif
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('partner.projects.photos', $project) }}">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label for="category" class="form-label">Категория</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Все категории</option>
                            @foreach($categoryOptions as $value => $label)
                                <option value="{{ $value }}" {{ $filters['category'] == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <label for="location" class="form-label">Помещение</label>
                        <select class="form-select" id="location" name="location">
                            <option value="">Все помещения</option>
                            @foreach($locationOptions as $value => $label)
                                <option value="{{ $value }}" {{ $filters['location'] == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <label for="sort" class="form-label">Сортировка</label>
                        <select class="form-select" id="sort" name="sort">
                            @foreach($sortOptions as $value => $label)
                                <option value="{{ $value }}" {{ $filters['sort'] == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <label for="search" class="form-label">Поиск</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ $filters['search'] }}" placeholder="Поиск по названию...">
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="document.getElementById('search').value=''; this.form.submit();">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>
                                Применить фильтры
                            </button>
                            <a href="{{ route('partner.projects.photos', $project) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Сбросить все
                            </a>
                            @if(count(array_filter($filters)))
                                <div class="text-muted align-self-center">
                                    <small>Найдено: {{ $photos->total() }} фотографий</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Галерея фотографий -->
    @if($photos->count() > 0)
        <div class="row g-3 mb-4">
            @foreach($photos as $photo)
                @php
                    $categoryName = $categoryOptions[$photo->category] ?? $photo->category ?? 'Без категории';
                    $locationName = $locationOptions[$photo->location] ?? $photo->location ?? '';
                    
                    // Генерируем URL для изображения
                    $imageUrl = $photo->url ?? asset('storage/' . $photo->path);
                @endphp
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="card photo-card h-100">
                        <div class="position-relative">
                            <img src="{{ $imageUrl }}" 
                                 alt="{{ $photo->original_name ?? $photo->filename }}" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover;"
                                 loading="lazy">
                            
                            <!-- Бейджи категории и помещения -->
                            <div class="position-absolute top-0 start-0 p-2">
                                <span class="badge bg-primary me-1">{{ $categoryName }}</span>
                                @if($locationName)
                                    <span class="badge bg-secondary">{{ $locationName }}</span>
                                @endif
                            </div>
                            
                            <!-- Кнопки действий -->
                            <div class="position-absolute top-0 end-0 p-2">
                                <div class="btn-group-vertical" role="group">
                                    <a href="{{ route('partner.projects.photos.show', [$project, $photo->id]) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-light mb-1" 
                                       title="Просмотр в полном размере">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                                        <button type="button" 
                                                class="btn btn-sm btn-light" 
                                                onclick="confirmDelete({{ $photo->id }}, '{{ $photo->original_name ?? $photo->filename }}')"
                                                title="Удалить">
                                            <i class="bi bi-trash text-danger"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <h6 class="card-title text-truncate mb-2" 
                                title="{{ $photo->original_name ?? $photo->filename }}">
                                {{ $photo->original_name ?? $photo->filename }}
                            </h6>
                            
                            <div class="text-muted small">
                                @if($photo->file_size)
                                    <div><i class="bi bi-file-earmark me-1"></i>{{ number_format($photo->file_size / 1024, 1) }} КБ</div>
                                @endif
                                @if($photo->created_at)
                                    <div><i class="bi bi-calendar me-1"></i>{{ $photo->created_at->format('d.m.Y H:i') }}</div>
                                @endif
                                @if($photo->comment)
                                    <div class="mt-2">
                                        <i class="bi bi-chat-text me-1"></i>
                                        <span class="text-break">{{ Str::limit($photo->comment, 100) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Пагинация -->
        @if($photos->hasPages())
            <div class="d-flex justify-content-center">
                {{ $photos->links('custom.pagination') }}
            </div>
        @endif
        
    @else
        <!-- Пустое состояние -->
        <div class="text-center py-5">
            <i class="bi bi-images display-1 text-muted"></i>
            <h5 class="mt-3">Фотографии не найдены</h5>
            <p class="text-muted">
                @if(count(array_filter($filters)))
                    По заданным фильтрам фотографии не найдены.<br>
                    <a href="{{ route('partner.projects.photos', $project) }}" class="btn btn-outline-primary mt-2">
                        Сбросить фильтры
                    </a>
                @else
                    В этом проекте пока нет фотографий.<br>
                    @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                            <i class="bi bi-plus-lg me-1"></i>
                            Загрузить первые фотографии
                        </button>
                    @endif
                @endif
            </p>
        </div>
    @endif

    <!-- Скрытые формы для удаления -->
    @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
        @foreach($photos as $photo)
            <form id="deleteForm{{ $photo->id }}" 
                  method="POST" 
                  action="{{ route('partner.projects.photos.delete', [$project, $photo->id]) }}" 
                  style="display: none;">
                @csrf
            </form>
        @endforeach
    @endif
</div>

<!-- Модальное окно загрузки фотографий -->
@if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
@include('partner.projects.modals.upload-photo-standard')
@endif
@endsection

@section('scripts')
@parent
<script>
// Функция подтверждения удаления
function confirmDelete(photoId, filename) {
    if (confirm(`Вы уверены, что хотите удалить фотографию "${filename}"?\n\nЭто действие нельзя отменить.`)) {
        document.getElementById('deleteForm' + photoId).submit();
    }
}

// Автоотправка формы фильтров при изменении select
document.addEventListener('DOMContentLoaded', function() {
    const filterSelects = document.querySelectorAll('#category, #location, #sort');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    // Поиск с задержкой
    const searchInput = document.getElementById('search');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 1000); // Отправляем форму через 1 секунду после окончания ввода
    });
});

// Функция для предпросмотра выбранных файлов
function previewSelectedFiles() {
    const fileInput = document.getElementById('photoFiles');
    const preview = document.getElementById('filePreview');
    
    if (fileInput.files.length > 0) {
        let html = '<h6>Выбранные файлы:</h6><ul class="list-group">';
        
        Array.from(fileInput.files).forEach((file, index) => {
            const size = (file.size / 1024 / 1024).toFixed(2);
            html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-image me-2"></i>${file.name}</span>
                        <span class="badge bg-secondary">${size} МБ</span>
                     </li>`;
        });
        
        html += '</ul>';
        preview.innerHTML = html;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

// Сообщения об успехе/ошибке
@if(session('success'))
    alert('✅ {{ session('success') }}');
@endif

@if(session('error'))
    alert('❌ {{ session('error') }}');
@endif

@if($errors->any())
    alert('❌ Ошибка: {{ $errors->first() }}');
@endif
</script>
@endsection
