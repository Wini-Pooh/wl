@extends('layouts.app')

@section('title', $template->name)

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-0">{{ $template->name }}</h3>
                            <small class="text-muted">
                                {{ ucfirst($template->document_type) }} • 
                                Создан {{ $template->creator->name }} • 
                                {{ $template->created_at->format('d.m.Y') }}
                            </small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($template->is_active)
                                <span class="badge bg-success">Активен</span>
                            @else
                                <span class="badge bg-secondary">Неактивен</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if($template->description)
                        <div class="alert alert-info mb-4">
                            <h6>Описание:</h6>
                            {{ $template->description }}
                        </div>
                    @endif

                    <!-- Предварительный просмотр (на всю ширину) -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Предварительный просмотр документа</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="generatePreview()">
                                    <i class="fas fa-eye"></i> Обновить просмотр
                                </button>
                                <a href="{{ route('documents.create', ['template' => $template->id]) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus"></i> Создать документ
                                </a>
                                <a href="{{ route('document-templates.edit', $template) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Редактировать
                                </a>
                                <button type="button" class="btn btn-sm btn-danger btn-delete" data-template-id="{{ $template->id }}">
                                    <i class="fas fa-trash"></i> Удалить
                                </button>
                                <a href="{{ route('documents.index', ['tab' => 'templates']) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Назад
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="preview" class="border rounded p-4" style="background-color: white; min-height: 500px;">
                                <div style="white-space: pre-wrap; line-height: 1.6; font-family: 'Times New Roman', serif; font-size: 14px;">
                                    {{ $template->renderTemplate([
                                        'client_name' => 'Иванов Иван Иванович',
                                        'contract_date' => '24.07.2025',
                                        'contract_number' => '001',
                                        'object_address' => 'г. Москва, ул. Примерная, д. 1',
                                        'work_description' => 'Ремонт квартиры с заменой электропроводки и сантехники',
                                        'total_cost' => '500 000',
                                        'start_date' => '01.08.2025',
                                        'end_date' => '31.08.2025',
                                        'payment_terms' => '50% предоплата, 50% по завершении работ',
                                        'client_phone' => '+7 (999) 123-45-67',
                                        'act_number' => 'АКТ-001',
                                        'act_date' => '31.08.2025',
                                        'work_period_start' => '01.08.2025',
                                        'work_period_end' => '31.08.2025'
                                    ]) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработка удаления шаблона
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const templateId = this.dataset.templateId;
            
            if (confirm('Вы уверены, что хотите удалить этот шаблон? Это действие нельзя отменить.')) {
                fetch(`/document-templates/${templateId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        window.location.href = '/documents?tab=templates';
                    } else {
                        showAlert('error', data.message || 'Произошла ошибка при удалении');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Произошла ошибка при удалении шаблона');
                });
            }
        });
    });

    // Функция показа уведомлений
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
});

// Функция обновления предварительного просмотра
function generatePreview() {
    showAlert('info', 'Предварительный просмотр показан с примерами данных для демонстрации');
}

// Функция показа уведомлений
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : (type === 'info' ? 'alert-info' : 'alert-danger');
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}
</script>
@endpush
