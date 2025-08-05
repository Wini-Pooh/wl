@extends('partner.projects.layouts.project-base')

@section('styles')
@parent
<link href="{{ asset('css/design-standard.css') }}" rel="stylesheet">
@endsection

@section('page-content')
<div class="container-fluid">
    <!-- Заголовок страницы -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="bi bi-paint-bucket me-2"></i>
            <span class="d-none d-md-inline">Дизайн проекта</span>
            <span class="d-md-none">Дизайн</span>
            (<span>{{ $designFiles->total() ?? 0 }}</span>)
        </h5>
        @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDesignModal">
            <i class="bi bi-upload me-1"></i>
            <span class="d-none d-md-inline">Загрузить дизайн</span>
            <span class="d-md-none">Загрузить</span>
        </button>
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
            <form method="GET" action="{{ route('partner.projects.design', $project) }}">
                <div class="row g-3">
                    <!-- Поиск -->
                    <div class="col-12">
                        <label for="search" class="form-label">Поиск по названию</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ $filters['search'] ?? '' }}"
                                   placeholder="Введите название файла дизайна...">
                            @if(!empty($filters['search']))
                            <a href="{{ route('partner.projects.design', $project) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Тип дизайна -->
                    <div class="col-md-3">
                        <label for="design_type" class="form-label">Тип дизайна</label>
                        <select class="form-select" id="design_type" name="design_type" onchange="this.form.submit()">
                            <option value="">Все типы</option>
                            @foreach($designTypeOptions as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['design_type'] ?? '') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Помещение -->
                    <div class="col-md-3">
                        <label for="room" class="form-label">Помещение</label>
                        <select class="form-select" id="room" name="room" onchange="this.form.submit()">
                            <option value="">Все помещения</option>
                            @foreach($roomOptions as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['room'] ?? '') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Стиль -->
                    <div class="col-md-3">
                        <label for="style" class="form-label">Стиль</label>
                        <select class="form-select" id="style" name="style" onchange="this.form.submit()">
                            <option value="">Все стили</option>
                            @foreach($styleOptions as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['style'] ?? '') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Сортировка -->
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Сортировка</label>
                        <select class="form-select" id="sort" name="sort" onchange="this.form.submit()">
                            @foreach($sortOptions as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['sort'] ?? 'newest') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-funnel"></i> Применить фильтры
                        </button>
                        <a href="{{ route('partner.projects.design', $project) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Сбросить
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Список файлов дизайна -->
    <div class="row g-3" id="designGallery">
        @if($designFiles->count() > 0)
            @foreach($designFiles as $designFile)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card design-card h-100">
                        <div class="design-preview position-relative">
                            @if($designFile->isImage())
                                <img src="{{ $designFile->url }}" alt="{{ $designFile->original_name }}" 
                                     class="card-img-top design-image"
                                     onclick="openDesignView('{{ $designFile->id }}')">
                            @else
                                <div class="design-file-icon text-center p-4" onclick="openDesignView('{{ $designFile->id }}')">
                                    @php
                                        $extension = strtolower(pathinfo($designFile->original_name, PATHINFO_EXTENSION));
                                        $iconClass = match($extension) {
                                            'pdf' => 'bi-file-pdf',
                                            'psd' => 'bi-file-image',
                                            'ai', 'eps' => 'bi-file-earmark-richtext',
                                            'dwg', 'dxf' => 'bi-file-earmark-ruled',
                                            'xd', 'fig' => 'bi-palette',
                                            default => 'bi-file-earmark'
                                        };
                                    @endphp
                                    <i class="{{ $iconClass }} display-4 text-primary"></i>
                                    <div class="mt-2 small text-muted">{{ strtoupper($extension) }}</div>
                                </div>
                            @endif
                            
                            <!-- Действия с файлом -->
                            @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                            <div class="design-actions">
                                <a href="{{ route('partner.projects.design.download', [$project, $designFile]) }}" 
                                   class="btn btn-sm btn-outline-light" title="Скачать">
                                    <i class="bi bi-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="confirmDelete('{{ $designFile->id }}', '{{ $designFile->original_name }}')" 
                                        title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            @endif
                            
                            <!-- Бейджи -->
                            <div class="design-badges">
                                @if($designFile->design_type)
                                    <span class="badge bg-primary">{{ $designFile->design_type_name ?? $designFile->design_type }}</span>
                                @endif
                                @if($designFile->room)
                                    <span class="badge bg-secondary">{{ $designFile->room_name ?? $designFile->room }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <h6 class="card-title text-truncate" title="{{ $designFile->original_name }}">
                                {{ $designFile->original_name }}
                            </h6>
                            
                            <div class="design-meta small text-muted">
                                @if($designFile->style)
                                    <div><i class="bi bi-palette me-1"></i>{{ $designFile->style_name ?? $designFile->style }}</div>
                                @endif
                                @if($designFile->designer)
                                    <div><i class="bi bi-person me-1"></i>{{ $designFile->designer }}</div>
                                @endif
                                <div><i class="bi bi-file-earmark me-1"></i>{{ $designFile->formatted_size }}</div>
                                <div><i class="bi bi-clock me-1"></i>{{ $designFile->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-folder2-open display-1 text-muted"></i>
                    <h5 class="mt-3">Нет файлов дизайна</h5>
                    <p class="text-muted">
                        @if(count(array_filter($filters)))
                            По заданным фильтрам ничего не найдено. 
                            <a href="{{ route('partner.projects.design', $project) }}">Сбросить фильтры</a>
                        @else
                            Загрузите файлы дизайна проекта, нажав кнопку "Загрузить дизайн" вверху страницы
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- Пагинация -->
    @if($designFiles->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $designFiles->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Модальное окно загрузки дизайна -->
@if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
@include('partner.projects.modals.upload-design-standard')
@endif
@endsection

@section('scripts')
@parent
<script>
// Функция подтверждения удаления
function confirmDelete(designId, filename) {
    if (confirm(`Вы уверены, что хотите удалить файл "${filename}"? Это действие нельзя отменить.`)) {
        // Создаем форму для DELETE запроса
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('partner.projects.design.destroy', [$project, '__ID__']) }}`.replace('__ID__', designId);
        
        // Добавляем CSRF токен
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Добавляем method override
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Функция открытия просмотра файла
function openDesignView(designId) {
    window.open(`{{ route('partner.projects.design.view', [$project, '__ID__']) }}`.replace('__ID__', designId), '_blank');
}

// Автоотправка формы фильтров при изменении select
document.addEventListener('DOMContentLoaded', function() {
    // Сообщения об успехе/ошибке
    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif
    
    @if($errors->any())
        @foreach($errors->all() as $error)
            showToast('{{ $error }}', 'error');
        @endforeach
    @endif
});

// Функция для показа toast уведомлений
function showToast(message, type = 'info') {
    // Создаем контейнер для toast, если его нет
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    container.appendChild(toast);
    
    // Показываем toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Удаляем элемент после скрытия
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

console.log('✅ Скрипты страницы дизайна загружены (без AJAX)');
</script>
@endsection
