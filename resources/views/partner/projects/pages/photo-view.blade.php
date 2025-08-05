@extends('partner.projects.layouts.project-base')

@section('styles')
@parent
<link href="{{ asset('css/photos-standard.css') }}" rel="stylesheet">
<style>
.photo-viewer {
    background: #000;
    padding: 2rem 0;
    min-height: 60vh;
}

.photo-container {
    text-align: center;
}

.photo-main {
    max-width: 100%;
    max-height: 70vh;
    border-radius: 8px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    transition: transform 0.3s ease;
}

.photo-main:hover {
    transform: scale(1.02);
}

.photo-meta {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 2rem;
}

.meta-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
}

.meta-item:last-child {
    margin-bottom: 0;
}

.meta-icon {
    width: 20px;
    margin-right: 10px;
    color: #6c757d;
}

.meta-value {
    font-weight: 500;
}

.action-buttons {
    position: fixed;
    top: 100px;
    right: 20px;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.action-buttons .btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    background: rgba(255,255,255,0.9);
    border: 1px solid rgba(255,255,255,0.8);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: scale(1.1);
    background: rgba(255,255,255,1);
}

@media (max-width: 768px) {
    .action-buttons {
        position: static;
        flex-direction: row;
        justify-content: center;
        margin-top: 1rem;
    }
    
    .photo-viewer {
        padding: 1rem 0;
    }
    
    .photo-meta {
        margin: 1rem;
        padding: 1rem;
    }
}

.breadcrumb-custom {
    background: transparent;
    padding: 0;
    margin-bottom: 1rem;
}

.breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
}
</style>
@endsection

@section('page-content')
<div class="container-fluid">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item">
                <a href="{{ route('partner.projects.show', $project) }}">Проект #{{ $project->id }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('partner.projects.photos', $project) }}">Фотографии</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $photo->original_name ?? $photo->filename }}
            </li>
        </ol>
    </nav>

    <!-- Заголовок -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-image me-2"></i>
            Просмотр фотографии
        </h4>
        
        <div class="d-flex gap-2">
            <a href="{{ route('partner.projects.photos', $project) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Назад к галерее
            </a>
        </div>
    </div>

    <!-- Основное изображение -->
    <div class="photo-viewer">
        <div class="photo-container">
            @php
                $imageUrl = $photo->url ?? asset('storage/' . $photo->path);
            @endphp
            
            <img src="{{ $imageUrl }}" 
                 alt="{{ $photo->original_name ?? $photo->filename }}" 
                 class="photo-main"
                 onclick="toggleFullscreen(this)">
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="action-buttons d-none d-md-flex">
        <a href="{{ $imageUrl }}" 
           target="_blank" 
           class="btn btn-light" 
           title="Открыть в новой вкладке">
            <i class="bi bi-arrow-up-right-square"></i>
        </a>
        
        <a href="{{ route('partner.projects.photos.download', [$project, $photo->id]) }}" 
           class="btn btn-light" 
           title="Скачать">
            <i class="bi bi-download"></i>
        </a>
        
        @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
            <button type="button" 
                    class="btn btn-light text-danger" 
                    onclick="confirmDelete()"
                    title="Удалить">
                <i class="bi bi-trash"></i>
            </button>
            
            <!-- Скрытая форма для удаления -->
            <form id="deleteForm" 
                  method="POST" 
                  action="{{ route('partner.projects.photos.delete', [$project, $photo->id]) }}" 
                  style="display: none;">
                @csrf
            </form>
        @endif
    </div>

    <div class="row">
        <!-- Метаданные фотографии -->
        <div class="col-lg-8">
            <div class="photo-meta">
                <h5 class="mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Информация о фотографии
                </h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="meta-item">
                            <i class="bi bi-file-earmark meta-icon"></i>
                            <div>
                                <small class="text-muted d-block">Название файла</small>
                                <span class="meta-value">{{ $photo->original_name ?? $photo->filename }}</span>
                            </div>
                        </div>
                        
                        @if($photo->file_size)
                        <div class="meta-item">
                            <i class="bi bi-hdd meta-icon"></i>
                            <div>
                                <small class="text-muted d-block">Размер файла</small>
                                <span class="meta-value">{{ number_format($photo->file_size / 1024, 1) }} КБ</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($photo->created_at)
                        <div class="meta-item">
                            <i class="bi bi-calendar meta-icon"></i>
                            <div>
                                <small class="text-muted d-block">Дата загрузки</small>
                                <span class="meta-value">{{ $photo->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($photo->mime_type)
                        <div class="meta-item">
                            <i class="bi bi-filetype-jpg meta-icon"></i>
                            <div>
                                <small class="text-muted d-block">Тип файла</small>
                                <span class="meta-value">{{ strtoupper(str_replace('image/', '', $photo->mime_type)) }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="col-md-6">
                        @if($photo->category)
                        <div class="meta-item">
                            <i class="bi bi-tags meta-icon"></i>
                            <div>
                                <small class="text-muted d-block">Категория</small>
                                <span class="badge bg-primary">{{ $categoryOptions[$photo->category] ?? $photo->category }}</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($photo->location)
                        <div class="meta-item">
                            <i class="bi bi-geo-alt meta-icon"></i>
                            <div>
                                <small class="text-muted d-block">Помещение</small>
                                <span class="badge bg-secondary">{{ $locationOptions[$photo->location] ?? $photo->location }}</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($photo->comment)
                        <div class="meta-item">
                            <i class="bi bi-chat-text meta-icon"></i>
                            <div>
                                <small class="text-muted d-block">Описание</small>
                                <span class="meta-value">{{ $photo->comment }}</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($photo->is_optimized)
                        <div class="meta-item">
                            <i class="bi bi-speedometer meta-icon"></i>
                            <div>
                                <small class="text-muted d-block">Оптимизация</small>
                                <span class="badge bg-success">Оптимизировано</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Мобильные кнопки действий -->
        <div class="col-lg-4 d-md-none">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Действия</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ $imageUrl }}" 
                           target="_blank" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-arrow-up-right-square me-2"></i>
                            Открыть в новой вкладке
                        </a>
                        
                        <a href="{{ route('partner.projects.photos.download', [$project, $photo->id]) }}" 
                           class="btn btn-outline-info">
                            <i class="bi bi-download me-2"></i>
                            Скачать фотографию
                        </a>
                        
                        @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                            <button type="button" 
                                    class="btn btn-outline-danger" 
                                    onclick="confirmDelete()">
                                <i class="bi bi-trash me-2"></i>
                                Удалить фотографию
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
// Функция подтверждения удаления
function confirmDelete() {
    if (confirm('Вы уверены, что хотите удалить эту фотографию?\n\nЭто действие нельзя отменить.')) {
        document.getElementById('deleteForm').submit();
    }
}

// Функция полноэкранного просмотра
function toggleFullscreen(img) {
    if (document.fullscreenElement) {
        document.exitFullscreen();
    } else {
        img.requestFullscreen().catch(err => {
            console.log('Fullscreen not supported or denied');
        });
    }
}

// Обработка клавиш
document.addEventListener('keydown', function(e) {
    switch(e.key) {
        case 'Escape':
            if (document.fullscreenElement) {
                document.exitFullscreen();
            }
            break;
        case 'f':
        case 'F':
            if (!document.fullscreenElement) {
                const img = document.querySelector('.photo-main');
                if (img) {
                    toggleFullscreen(img);
                }
            }
            break;
        case 'ArrowLeft':
            // Можно добавить навигацию к предыдущей фотографии
            break;
        case 'ArrowRight':
            // Можно добавить навигацию к следующей фотографии
            break;
    }
});

// Показываем подсказку о горячих клавишах
document.addEventListener('DOMContentLoaded', function() {
    // Можно добавить tooltip с подсказками
    const img = document.querySelector('.photo-main');
    if (img) {
        img.title = 'Нажмите для полноэкранного просмотра (или клавишу F)';
    }
});

// Сообщения об успехе/ошибке
@if(session('success'))
    alert('✅ {{ session('success') }}');
@endif

@if(session('error'))
    alert('❌ {{ session('error') }}');
@endif
</script>
@endsection
