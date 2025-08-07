@extends('layouts.app')

@section('title', $template->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i>{{ $template->name }}
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('documents.create', ['template' => $template->id]) }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Создать документ
                        </a>
                        <a href="{{ route('document-templates.edit', $template->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Редактировать
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Информация о шаблоне -->
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Информация о шаблоне</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <small class="text-muted">Название:</small><br>
                                        <strong>{{ $template->name }}</strong>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">Тип документа:</small><br>
                                        <span class="badge bg-{{ $template->document_type == 'contract' ? 'primary' : ($template->document_type == 'invoice' ? 'success' : 'secondary') }}">
                                            {{ ucfirst($template->document_type) }}
                                        </span>
                                    </div>
                                    
                                    @if($template->description)
                                        <div class="mb-3">
                                            <small class="text-muted">Описание:</small><br>
                                            <p>{{ $template->description }}</p>
                                        </div>
                                    @endif
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">Создан:</small><br>
                                        <strong>{{ $template->created_at->format('d.m.Y в H:i') }}</strong>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">Автор:</small><br>
                                        <strong>{{ $template->creator->name ?? 'Система' }}</strong>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">Последнее изменение:</small><br>
                                        <strong>{{ $template->updated_at->format('d.m.Y в H:i') }}</strong>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">Статус:</small><br>
                                        <span class="badge bg-{{ $template->is_active ? 'success' : 'danger' }}">
                                            {{ $template->is_active ? 'Активен' : 'Неактивен' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Доступные переменные -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Переменные в шаблоне</h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $variables = [];
                                        preg_match_all('/@\{\{([^}]+)\}\}/', $template->content, $matches);
                                        if (!empty($matches[1])) {
                                            $variables = array_unique($matches[1]);
                                        }
                                    @endphp
                                    
                                    @if(count($variables) > 0)
                                        <div class="row g-2">
                                            @foreach($variables as $variable)
                                                <div class="col-12">
                                                    <code class="variable-tag">@{{{{ $variable }}}}</code>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">В шаблоне не используются переменные</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Содержимое шаблона -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Содержимое шаблона</h6>
                                    <button type="button" class="btn btn-outline-info btn-sm" id="showPreviewBtn">
                                        <i class="fas fa-eye"></i> Предварительный просмотр
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="template-content border rounded p-3" style="max-height: 600px; overflow-y: auto; white-space: pre-wrap; font-family: monospace; background: #f8f9fa;">{{ $template->content }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('document-templates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Назад к списку
                        </a>
                        <div class="btn-group">
                            <a href="{{ route('documents.create', ['template' => $template->id]) }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Создать документ
                            </a>
                            <a href="{{ route('document-templates.edit', $template->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Редактировать
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно предварительного просмотра -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Предварительный просмотр: {{ $template->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent" style="font-family: 'Times New Roman', serif; line-height: 1.6;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <a href="{{ route('documents.create', ['template' => $template->id]) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Создать документ на основе шаблона
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.variable-tag {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    margin: 0.1rem;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.template-content {
    color: #333;
    line-height: 1.6;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Показать предварительный просмотр в модальном окне
    document.getElementById('showPreviewBtn').addEventListener('click', function() {
        const templateContent = @json($template->content);
        
        // Простая замена переменных для предварительного просмотра
        let previewContent = templateContent
            .replace(/@\{\{client_name\}\}/g, '<strong class="text-primary">Иван Иванов</strong>')
            .replace(/@\{\{current_date\}\}/g, '<strong class="text-primary">' + new Date().toLocaleDateString('ru-RU') + '</strong>')
            .replace(/@\{\{client_phone\}\}/g, '<strong class="text-primary">+7 (XXX) XXX-XX-XX</strong>')
            .replace(/@\{\{object_address\}\}/g, '<strong class="text-primary">г. Москва, ул. Примерная, д. 1</strong>')
            .replace(/@\{\{contract_number\}\}/g, '<strong class="text-primary">№ 001/2024</strong>')
            .replace(/@\{\{contract_date\}\}/g, '<strong class="text-primary">' + new Date().toLocaleDateString('ru-RU') + '</strong>')
            .replace(/@\{\{total_cost\}\}/g, '<strong class="text-primary">100 000 руб.</strong>')
            .replace(/@\{\{payment_terms\}\}/g, '<strong class="text-primary">50% предоплата, 50% по факту</strong>')
            .replace(/\n/g, '<br>');
        
        document.getElementById('previewContent').innerHTML = previewContent;
        
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    });
});
</script>
@endpush
