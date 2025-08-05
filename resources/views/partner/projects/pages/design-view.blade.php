@extends('partner.projects.layouts.project-base')

@section('styles')
@parent
<link href="{{ asset('css/design-standard.css') }}" rel="stylesheet">
<style>
.design-viewer {
    background: #f8f9fa;
    min-height: 100vh;
}

.design-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
}

.design-main {
    max-width: 100%;
    height: auto;
    cursor: zoom-in;
    transition: transform 0.3s ease;
}

.design-main:hover {
    transform: scale(1.02);
}

.design-meta {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.meta-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f3f4;
    display: flex;
    align-items: center;
}

.meta-item:last-child {
    border-bottom: none;
}

.meta-icon {
    width: 20px;
    margin-right: 12px;
    color: #6c757d;
}

.meta-value {
    font-weight: 500;
    color: #495057;
}

.action-buttons {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    padding: 1.5rem;
}

.action-buttons .btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

@media (max-width: 768px) {
    .design-viewer {
        padding: 1rem;
    }
    
    .action-buttons {
        position: fixed;
        bottom: 1rem;
        left: 1rem;
        right: 1rem;
        z-index: 1000;
    }
    
    .action-buttons .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

.breadcrumb-custom {
    background: transparent;
    padding: 0;
    margin-bottom: 1.5rem;
}

.breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
    content: '›';
    color: #6c757d;
}
</style>
@endsection

@section('page-content')
<div class="design-viewer">
    <div class="container-fluid">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="breadcrumb-custom">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('partner.projects.show', $project) }}">
                        <i class="bi bi-house-door me-1"></i>{{ $project->title }}
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('partner.projects.design', $project) }}">
                        <i class="bi bi-paint-bucket me-1"></i>Дизайн
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $designFile->original_name }}
                </li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Основное содержимое -->
            <div class="col-lg-8">
                <div class="design-container">
                    @if($designFile->isImage())
                        <img src="{{ $designFile->url }}" alt="{{ $designFile->original_name }}" 
                             class="design-main w-100"
                             onclick="openFullscreen(this)">
                    @else
                        <div class="text-center p-5">
                            @php
                                $extension = strtolower(pathinfo($designFile->original_name, PATHINFO_EXTENSION));
                                $iconClass = match($extension) {
                                    'pdf' => 'bi-file-pdf text-danger',
                                    'psd' => 'bi-file-image text-info',
                                    'ai', 'eps' => 'bi-file-earmark-richtext text-warning',
                                    'dwg', 'dxf' => 'bi-file-earmark-ruled text-success',
                                    'xd', 'fig' => 'bi-palette text-primary',
                                    default => 'bi-file-earmark text-secondary'
                                };
                            @endphp
                            <i class="{{ $iconClass }}" style="font-size: 8rem;"></i>
                            <h4 class="mt-3">{{ strtoupper($extension) }} файл</h4>
                            <p class="text-muted">Предпросмотр недоступен для данного типа файла</p>
                            
                            @if($extension === 'pdf')
                                <iframe src="{{ $designFile->url }}" width="100%" height="600" class="border rounded"></iframe>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Боковая панель с информацией -->
            <div class="col-lg-4">
                <!-- Метаданные файла -->
                <div class="design-meta p-3 mb-4">
                    <h5 class="mb-3">
                        <i class="bi bi-info-circle me-2"></i>Информация о файле
                    </h5>
                    
                    <div class="meta-item">
                        <i class="bi bi-file-earmark meta-icon"></i>
                        <div>
                            <div class="meta-value">{{ $designFile->original_name }}</div>
                            <small class="text-muted">Название файла</small>
                        </div>
                    </div>
                    
                    @if($designFile->design_type)
                    <div class="meta-item">
                        <i class="bi bi-tag meta-icon"></i>
                        <div>
                            <div class="meta-value">{{ $designFile->design_type_name ?? $designFile->design_type }}</div>
                            <small class="text-muted">Тип дизайна</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($designFile->room)
                    <div class="meta-item">
                        <i class="bi bi-house-door meta-icon"></i>
                        <div>
                            <div class="meta-value">{{ $designFile->room_name ?? $designFile->room }}</div>
                            <small class="text-muted">Помещение</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($designFile->style)
                    <div class="meta-item">
                        <i class="bi bi-palette meta-icon"></i>
                        <div>
                            <div class="meta-value">{{ $designFile->style_name ?? $designFile->style }}</div>
                            <small class="text-muted">Стиль</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($designFile->stage)
                    <div class="meta-item">
                        <i class="bi bi-flag meta-icon"></i>
                        <div>
                            <div class="meta-value">{{ $designFile->stage }}</div>
                            <small class="text-muted">Этап проекта</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($designFile->designer)
                    <div class="meta-item">
                        <i class="bi bi-person meta-icon"></i>
                        <div>
                            <div class="meta-value">{{ $designFile->designer }}</div>
                            <small class="text-muted">Дизайнер</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($designFile->software)
                    <div class="meta-item">
                        <i class="bi bi-pc-display meta-icon"></i>
                        <div>
                            <div class="meta-value">{{ $designFile->software }}</div>
                            <small class="text-muted">Программное обеспечение</small>
                        </div>
                    </div>
                    @endif
                    
                    <div class="meta-item">
                        <i class="bi bi-hdd meta-icon"></i>
                        <div>
                            <div class="meta-value">{{ $designFile->formatted_size }}</div>
                            <small class="text-muted">Размер файла</small>
                        </div>
                    </div>
                    
                    <div class="meta-item">
                        <i class="bi bi-calendar meta-icon"></i>
                        <div>
                            <div class="meta-value">{{ $designFile->created_at->format('d.m.Y H:i') }}</div>
                            <small class="text-muted">Дата загрузки</small>
                        </div>
                    </div>
                    
                    @if($designFile->uploader)
                    <div class="meta-item">
                        <i class="bi bi-person-check meta-icon"></i>
                        <div>
                            <div class="meta-value">{{ $designFile->uploader->name }}</div>
                            <small class="text-muted">Загрузил</small>
                        </div>
                    </div>
                    @endif
                </div>

                @if($designFile->description)
                <!-- Описание -->
                <div class="design-meta p-3 mb-4">
                    <h6 class="mb-3">
                        <i class="bi bi-chat-left-text me-2"></i>Описание
                    </h6>
                    <p class="mb-0">{{ $designFile->description }}</p>
                </div>
                @endif

                <!-- Действия -->
                @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                <div class="action-buttons">
                    <h6 class="mb-3">
                        <i class="bi bi-gear me-2"></i>Действия
                    </h6>
                    
                    <a href="{{ route('partner.projects.design.download', [$project, $designFile]) }}" 
                       class="btn btn-primary d-block mb-2">
                        <i class="bi bi-download me-2"></i>Скачать файл
                    </a>
                    
                    <a href="{{ route('partner.projects.design', $project) }}" 
                       class="btn btn-outline-secondary d-block mb-2">
                        <i class="bi bi-arrow-left me-2"></i>Вернуться к списку
                    </a>
                    
                    <form method="POST" action="{{ route('partner.projects.design.destroy', [$project, $designFile]) }}" 
                          class="d-inline w-100" onsubmit="return confirm('Вы уверены, что хотите удалить этот файл? Это действие нельзя отменить.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger d-block w-100">
                            <i class="bi bi-trash me-2"></i>Удалить файл
                        </button>
                    </form>
                </div>
                @else
                <div class="action-buttons">
                    <a href="{{ route('partner.projects.design.download', [$project, $designFile]) }}" 
                       class="btn btn-primary d-block mb-2">
                        <i class="bi bi-download me-2"></i>Скачать файл
                    </a>
                    
                    <a href="{{ route('partner.projects.design', $project) }}" 
                       class="btn btn-outline-secondary d-block">
                        <i class="bi bi-arrow-left me-2"></i>Вернуться к списку
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
// Функция для открытия изображения в полноэкранном режиме
function openFullscreen(img) {
    if (img.requestFullscreen) {
        img.requestFullscreen();
    } else if (img.mozRequestFullScreen) {
        img.mozRequestFullScreen();
    } else if (img.webkitRequestFullscreen) {
        img.webkitRequestFullscreen();
    } else if (img.msRequestFullscreen) {
        img.msRequestFullscreen();
    }
}

// Добавляем подсказку для изображений
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('.design-main');
    images.forEach(img => {
        img.title = 'Нажмите для просмотра в полноэкранном режиме';
    });
});

console.log('✅ Скрипты просмотра дизайна загружены');
</script>
@endsection
