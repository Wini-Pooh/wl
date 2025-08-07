<div class="row">
    @if($templates->count() > 0)
        @foreach($templates as $template)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card template-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-file-alt text-primary me-2"></i>
                            {{ $template->name }}
                        </h5>
                        <p class="card-text text-muted">
                            {{ Str::limit($template->description, 100) }}
                        </p>
                        <div class="template-meta">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $template->created_at->format('d.m.Y') }}
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('documents.create', ['template' => $template->id]) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Создать документ
                            </a>
                            <button type="button" 
                                    class="btn btn-outline-secondary btn-sm"
                                    onclick="previewTemplate({{ $template->id }})"
                                    title="Предварительный просмотр">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" 
                                    class="btn btn-outline-info btn-sm"
                                    onclick="editTemplate({{ $template->id }})"
                                    title="Редактировать">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Шаблоны документов не найдены</h4>
                <p class="text-muted">Создайте первый шаблон для автоматизации создания документов</p>
                <a href="{{ route('document-templates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Создать шаблон
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Пагинация -->
@if($templates->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $templates->withQueryString()->links() }}
    </div>
@endif

<!-- Модальное окно для предварительного просмотра шаблона -->
<div class="modal fade" id="templatePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Предварительный просмотр шаблона</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="templatePreviewContent">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Загрузка...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" id="useTemplateBtn">
                    Использовать шаблон
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.template-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #e9ecef;
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.template-meta {
    border-top: 1px solid #f8f9fa;
    padding-top: 10px;
    margin-top: 10px;
}

.card-footer .btn-group .btn {
    flex: 1;
}

.card-footer .btn-group .btn:not(:first-child) {
    flex: 0 0 auto;
    min-width: 40px;
}
</style>

<script>
function previewTemplate(templateId) {
    // Показываем модальное окно
    const modal = new bootstrap.Modal(document.getElementById('templatePreviewModal'));
    modal.show();
    
    // Загружаем содержимое шаблона
    fetch(`/document-templates/${templateId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('templatePreviewContent').innerHTML = `
                <div class="mb-3">
                    <h6><strong>Название:</strong> ${data.name}</h6>
                    <p class="text-muted">${data.description || 'Описание отсутствует'}</p>
                </div>
                <div class="border p-3 bg-light">
                    <h6>Содержимое шаблона:</h6>
                    <div class="template-content">${data.content}</div>
                </div>
                ${data.variables && data.variables.length > 0 ? `
                <div class="mt-3">
                    <h6>Переменные в шаблоне:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        ${data.variables.map(variable => `
                            <span class="badge bg-secondary">\$\{'{{'}\$\{variable\}\$\{'}}'}</span>
                        `).join('')}
                    </div>
                </div>
                ` : ''}
            `;
            
            // Обновляем кнопку использования шаблона
            document.getElementById('useTemplateBtn').onclick = function() {
                window.location.href = `/documents/create?template=${templateId}`;
            };
        })
        .catch(error => {
            document.getElementById('templatePreviewContent').innerHTML = `
                <div class="alert alert-danger">
                    Ошибка загрузки шаблона: ${error.message}
                </div>
            `;
        });
}

function editTemplate(templateId) {
    window.location.href = `/document-templates/${templateId}/edit`;
}
</script>
