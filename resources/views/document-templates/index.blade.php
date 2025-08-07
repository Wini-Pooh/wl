@extends('layouts.app')

@section('title', 'Шаблоны документов')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i>Шаблоны документов
                    </h3>
                    <a href="{{ route('document-templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Создать шаблон
                    </a>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
                        </div>
                    @endif

                    @if($templates->count() > 0)
                        <div class="row">
                            @foreach($templates as $template)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card template-card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ $template->name }}</h6>
                                            <span class="badge bg-{{ $template->document_type == 'contract' ? 'primary' : ($template->document_type == 'invoice' ? 'success' : 'secondary') }}">
                                                {{ ucfirst($template->document_type) }}
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            @if($template->description)
                                                <p class="card-text text-muted">{{ Str::limit($template->description, 100) }}</p>
                                            @endif
                                            <div class="template-meta">
                                                <small class="text-muted">
                                                    <i class="fas fa-user me-1"></i>{{ $template->creator->name ?? 'Система' }}<br>
                                                    <i class="fas fa-calendar me-1"></i>{{ $template->created_at->format('d.m.Y') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-outline-info btn-sm" 
                                                        onclick="previewTemplate({{ $template->id }})">
                                                    <i class="fas fa-eye"></i> Просмотр
                                                </button>
                                                <a href="{{ route('document-templates.edit', $template->id) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-edit"></i> Изменить
                                                </a>
                                                <a href="{{ route('documents.create', ['template' => $template->id]) }}" 
                                                   class="btn btn-outline-success btn-sm">
                                                    <i class="fas fa-plus"></i> Использовать
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Пагинация -->
                        @if($templates->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $templates->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Шаблоны не найдены</h4>
                            <p class="text-muted">Создайте первый шаблон документа для упрощения работы</p>
                            <a href="{{ route('document-templates.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Создать первый шаблон
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для предварительного просмотра шаблона -->
<div class="modal fade" id="templatePreviewModal" tabindex="-1" aria-labelledby="templatePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="templatePreviewModalLabel">Предварительный просмотр шаблона</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div id="templatePreviewContent" style="font-family: 'Times New Roman', serif; line-height: 1.6;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" id="useTemplateBtn">
                    <i class="fas fa-plus"></i> Создать документ на основе шаблона
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.template-card {
    transition: transform 0.2s ease;
    border: 1px solid #dee2e6;
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.template-meta {
    font-size: 0.875rem;
}

.card-footer .btn-group .btn {
    border-radius: 0;
}

.card-footer .btn-group .btn:first-child {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.card-footer .btn-group .btn:last-child {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}
</style>
@endpush

@push('scripts')
<script>
let currentTemplateId = null;

function previewTemplate(templateId) {
    currentTemplateId = templateId;
    
    // Загружаем данные шаблона через AJAX
    fetch(`/document-templates/${templateId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Заполняем модальное окно
        document.getElementById('templatePreviewModalLabel').textContent = `Предварительный просмотр: ${data.name}`;
        
        // Простая замена переменных для предварительного просмотра
        let previewContent = data.content
            .replace(/@\{\{client_name\}\}/g, '<strong class="text-primary">Иван Иванов</strong>')
            .replace(/@\{\{current_date\}\}/g, '<strong class="text-primary">' + new Date().toLocaleDateString('ru-RU') + '</strong>')
            .replace(/@\{\{client_phone\}\}/g, '<strong class="text-primary">+7 (XXX) XXX-XX-XX</strong>')
            .replace(/@\{\{object_address\}\}/g, '<strong class="text-primary">г. Москва, ул. Примерная, д. 1</strong>')
            .replace(/@\{\{contract_number\}\}/g, '<strong class="text-primary">№ 001/2024</strong>')
            .replace(/@\{\{contract_date\}\}/g, '<strong class="text-primary">' + new Date().toLocaleDateString('ru-RU') + '</strong>')
            .replace(/@\{\{total_cost\}\}/g, '<strong class="text-primary">100 000 руб.</strong>')
            .replace(/@\{\{payment_terms\}\}/g, '<strong class="text-primary">50% предоплата, 50% по факту</strong>')
            .replace(/\n/g, '<br>');
        
        document.getElementById('templatePreviewContent').innerHTML = previewContent;
        
        // Показываем модальное окно
        const modal = new bootstrap.Modal(document.getElementById('templatePreviewModal'));
        modal.show();
    })
    .catch(error => {
        console.error('Ошибка загрузки шаблона:', error);
        alert('Ошибка загрузки шаблона');
    });
}

// Обработчик кнопки "Создать документ на основе шаблона"
document.getElementById('useTemplateBtn').addEventListener('click', function() {
    if (currentTemplateId) {
        window.location.href = `/documents/create?template=${currentTemplateId}`;
    }
});
</script>
@endpush
