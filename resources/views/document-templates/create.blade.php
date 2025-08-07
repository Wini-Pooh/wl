@extends('layouts.app')

@section('title', 'Создание шаблона документа')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-plus me-2"></i>Создание нового шаблона документа
                    </h3>
                </div>
                
                <form action="{{ route('document-templates.store') }}" method="POST" id="templateForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Основная форма -->
                            <div class="col-lg-8">
                                <!-- Базовая информация -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Основная информация</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-8">
                                                <label for="name" class="form-label">Название шаблона *</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                       id="name" name="name" value="{{ old('name') }}" required
                                                       placeholder="Введите название шаблона">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="document_type" class="form-label">Тип документа *</label>
                                                <select class="form-select @error('document_type') is-invalid @enderror" 
                                                        id="document_type" name="document_type" required>
                                                    <option value="">Выберите тип</option>
                                                    <option value="contract" {{ old('document_type') == 'contract' ? 'selected' : '' }}>Договор</option>
                                                    <option value="act" {{ old('document_type') == 'act' ? 'selected' : '' }}>Акт выполненных работ</option>
                                                    <option value="invoice" {{ old('document_type') == 'invoice' ? 'selected' : '' }}>Счет на оплату</option>
                                                    <option value="estimate" {{ old('document_type') == 'estimate' ? 'selected' : '' }}>Смета</option>
                                                    <option value="technical" {{ old('document_type') == 'technical' ? 'selected' : '' }}>Техническая документация</option>
                                                    <option value="other" {{ old('document_type') == 'other' ? 'selected' : '' }}>Прочее</option>
                                                </select>
                                                @error('document_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Описание</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="3" 
                                                      placeholder="Краткое описание шаблона">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Содержимое шаблона -->
                                        <div class="mb-3">
                                            <label for="content" class="form-label">Содержимое шаблона *</label>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="insertText('{{ '{{client_name}}' }}')">
                                                        Имя клиента
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary" onclick="insertText('{{ '{{current_date}}' }}')">
                                                        Текущая дата
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary" onclick="insertText('{{ '{{contract_number}}' }}')">
                                                        № договора
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary" onclick="insertText('{{ '{{object_address}}' }}')">
                                                        Адрес объекта
                                                    </button>
                                                </div>
                                                <button type="button" class="btn btn-outline-info btn-sm" id="previewBtn">
                                                    <i class="fas fa-eye"></i> Предварительный просмотр
                                                </button>
                                            </div>
                                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                                      id="content" name="content" rows="15" required
                                                      placeholder="Введите содержимое шаблона">{{ old('content') }}</textarea>
                                            <div class="form-text">
                                                Используйте переменные в двойных фигурных скобках для автозаполнения: {{ '{{client_name}}' }}, {{ '{{current_date}}' }} и т.д.
                                            </div>
                                            @error('content')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Боковая панель -->
                            <div class="col-lg-4">
                                <!-- Предварительный просмотр -->
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Предварительный просмотр</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="updatePreview">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="templatePreview" class="border rounded p-3" style="max-height: 400px; overflow-y: auto; font-size: 0.9rem; line-height: 1.4; background: #f8f9fa;">
                                            <p class="text-muted text-center">
                                                <i class="fas fa-eye-slash"></i><br>
                                                Введите содержимое шаблона для предварительного просмотра
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Доступные переменные -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Доступные переменные</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <small class="text-muted">Основные:</small>
                                            </div>
                                            <div class="col-6">
                                                <code class="variable-tag">{{ '{{client_name}}' }}</code>
                                            </div>
                                            <div class="col-6">
                                                <code class="variable-tag">{{ '{{current_date}}' }}</code>
                                            </div>
                                            <div class="col-6">
                                                <code class="variable-tag">{{ '{{client_phone}}' }}</code>
                                            </div>
                                            <div class="col-6">
                                                <code class="variable-tag">{{ '{{object_address}}' }}</code>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <small class="text-muted">Договоры:</small>
                                            </div>
                                            <div class="col-6">
                                                <code class="variable-tag">{{ '{{contract_number}}' }}</code>
                                            </div>
                                            <div class="col-6">
                                                <code class="variable-tag">{{ '{{contract_date}}' }}</code>
                                            </div>
                                            <div class="col-6">
                                                <code class="variable-tag">{{ '{{total_cost}}' }}</code>
                                            </div>
                                            <div class="col-6">
                                                <code class="variable-tag">{{ '{{payment_terms}}' }}</code>
                                            </div>
                                        </div>
                                        <div class="form-text mt-2">
                                            Нажмите на переменную, чтобы вставить её в шаблон
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('document-templates.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Отмена
                                </a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Сохранить шаблон
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно предварительного просмотра -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Предварительный просмотр шаблона</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div id="fullPreviewContent" style="font-family: 'Times New Roman', serif; line-height: 1.6;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentField = document.getElementById('content');
    const previewDiv = document.getElementById('templatePreview');
    
    // Обновление предварительного просмотра
    function updatePreview() {
        const content = contentField.value;
        if (content.trim() === '') {
            previewDiv.innerHTML = `
                <p class="text-muted text-center">
                    <i class="fas fa-eye-slash"></i><br>
                    Введите содержимое шаблона для предварительного просмотра
                </p>
            `;
            return;
        }
        
        // Простая замена переменных для предварительного просмотра
        let previewContent = content
            .replace(/@\{\{client_name\}\}/g, '<span class="text-primary">Иван Иванов</span>')
            .replace(/@\{\{current_date\}\}/g, '<span class="text-primary">' + new Date().toLocaleDateString('ru-RU') + '</span>')
            .replace(/@\{\{client_phone\}\}/g, '<span class="text-primary">+7 (XXX) XXX-XX-XX</span>')
            .replace(/@\{\{object_address\}\}/g, '<span class="text-primary">г. Москва, ул. Примерная, д. 1</span>')
            .replace(/@\{\{contract_number\}\}/g, '<span class="text-primary">№ 001/2024</span>')
            .replace(/@\{\{contract_date\}\}/g, '<span class="text-primary">' + new Date().toLocaleDateString('ru-RU') + '</span>')
            .replace(/@\{\{total_cost\}\}/g, '<span class="text-primary">100 000 руб.</span>')
            .replace(/@\{\{payment_terms\}\}/g, '<span class="text-primary">50% предоплата, 50% по факту</span>')
            .replace(/\n/g, '<br>');
        
        previewDiv.innerHTML = previewContent;
    }
    
    // Автообновление предварительного просмотра
    contentField.addEventListener('input', updatePreview);
    
    // Кнопка обновления предварительного просмотра
    document.getElementById('updatePreview').addEventListener('click', updatePreview);
    
    // Показать полный предварительный просмотр в модальном окне
    document.getElementById('previewBtn').addEventListener('click', function() {
        const content = contentField.value;
        if (content.trim() === '') {
            alert('Введите содержимое шаблона для предварительного просмотра');
            return;
        }
        
        let fullPreviewContent = content
            .replace(/@\{\{client_name\}\}/g, '<strong>Иван Иванов</strong>')
            .replace(/@\{\{current_date\}\}/g, '<strong>' + new Date().toLocaleDateString('ru-RU') + '</strong>')
            .replace(/@\{\{client_phone\}\}/g, '<strong>+7 (XXX) XXX-XX-XX</strong>')
            .replace(/@\{\{object_address\}\}/g, '<strong>г. Москва, ул. Примерная, д. 1</strong>')
            .replace(/@\{\{contract_number\}\}/g, '<strong>№ 001/2024</strong>')
            .replace(/@\{\{contract_date\}\}/g, '<strong>' + new Date().toLocaleDateString('ru-RU') + '</strong>')
            .replace(/@\{\{total_cost\}\}/g, '<strong>100 000 руб.</strong>')
            .replace(/@\{\{payment_terms\}\}/g, '<strong>50% предоплата, 50% по факту</strong>')
            .replace(/\n/g, '<br>');
        
        document.getElementById('fullPreviewContent').innerHTML = fullPreviewContent;
        
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    });
    
    // Первоначальное обновление предварительного просмотра
    updatePreview();
});

// Глобальная функция для вставки текста
window.insertText = function(text) {
    const contentField = document.getElementById('content');
    const start = contentField.selectionStart;
    const end = contentField.selectionEnd;
    const currentText = contentField.value;
    
    const newText = currentText.substring(0, start) + text + currentText.substring(end);
    contentField.value = newText;
    
    // Установить курсор после вставленного текста
    const newCursorPos = start + text.length;
    contentField.setSelectionRange(newCursorPos, newCursorPos);
    contentField.focus();
    
    // Обновить предварительный просмотр
    const event = new Event('input');
    contentField.dispatchEvent(event);
};

// Обработчики для переменных
document.addEventListener('DOMContentLoaded', function() {
    const variableTags = document.querySelectorAll('.variable-tag');
    variableTags.forEach(tag => {
        tag.style.cursor = 'pointer';
        tag.addEventListener('click', function() {
            insertText(this.textContent);
        });
    });
});
</script>
@endpush

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
    cursor: pointer;
    transition: all 0.2s ease;
}

.variable-tag:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    transform: translateY(-1px);
}

.template-preview {
    font-family: 'Times New Roman', serif;
    line-height: 1.6;
    color: #333;
}
</style>
@endpush
