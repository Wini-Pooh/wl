<!-- Документы проекта -->
<div id="documents-tab-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="bi bi-file-text me-2"></i>
            <span class="d-none d-md-inline">Документы проекта</span>
            <span class="d-md-none">Документы</span>
            ({{ $documents->total() }})
        </h5>
        @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
            <i class="bi bi-plus-lg"></i>
            <span class="d-none d-md-inline ms-2">Загрузить документ</span>
        </button>
        @endif
    </div>

    <!-- Фильтры -->
    <div class="card mb-4" id="documentFiltersCard">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Фильтры и сортировка</h6>
            @php
                $activeFiltersCount = 0;
                if (!empty($filters['search'] ?? '')) $activeFiltersCount++;
                if (!empty($filters['document_type'] ?? '')) $activeFiltersCount++;
                if (!empty($filters['status'] ?? '')) $activeFiltersCount++;
                if (!empty($filters['date_from'] ?? '')) $activeFiltersCount++;
                if (!empty($filters['date_to'] ?? '')) $activeFiltersCount++;
            @endphp
            <small class="text-muted">Активных фильтров: {{ $activeFiltersCount }}</small>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('partner.projects.documents', $project) }}" class="needs-validation" novalidate id="documentFilters">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <label for="documentTypeFilter" class="form-label">Тип документа</label>
                        <select class="form-select" id="document_type" name="document_type">
                            <option value="">Все типы</option>
                            @foreach($documentTypeOptions as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['document_type'] ?? '') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="statusFilter" class="form-label">Статус</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Все статусы</option>
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['status'] ?? '') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="sortFilter" class="form-label">Сортировка</label>
                        <select class="form-select" id="sort" name="sort">
                            @foreach($sortOptions as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['sort'] ?? 'created_at_desc') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-secondary" id="resetFilters" title="Сбросить все фильтры">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="toggleDocumentFiltersBtn" title="Расширенные фильтры">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Расширенные фильтры (скрываемые) -->
                <div class="row g-3 mt-2" id="advancedDocumentFilters" style="display: none;">
                    <div class="col-md-4">
                        <label for="searchFilter" class="form-label">Поиск по названию</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ $filters['search'] ?? '' }}" placeholder="Введите название...">
                    </div>
                    <div class="col-md-3">
                        <label for="dateFromFilter" class="form-label">Дата с</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="dateToFilter" class="form-label">Дата по</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>Найти
                        </button>
                    </div>
                </div>
                
                <!-- Скрытая кнопка применения для основных фильтров -->
                <button type="submit" style="display: none;" id="hiddenSubmitBtn"></button>
            </form>
        </div>
    </div>

    <!-- Список документов -->
    <div id="documentsGallery">
        @if($documents->count() > 0)
            <div class="row g-3">
                @foreach($documents as $document)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 document-card" data-document-id="{{ $document->id }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1" title="{{ $document->original_name }}">
                                            <i class="bi bi-file-text me-2 text-primary"></i>
                                            {{ Str::limit($document->original_name, 30) }}
                                        </h6>
                                        @if($document->name != $document->original_name)
                                            <small class="text-muted">Системное имя: {{ Str::limit($document->name, 25) }}</small>
                                        @endif
                                    </div>
                                    @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('partner.projects.documents.download', [$project, $document->id]) }}" target="_blank">
                                                    <i class="bi bi-download me-2"></i>Скачать
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="deleteDocument({{ $document->id }})">
                                                    <i class="bi bi-trash me-2"></i>Удалить
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="document-info">
                                    @if($document->document_type)
                                        <span class="badge bg-info me-2">{{ $documentTypeOptions[$document->document_type] ?? $document->document_type }}</span>
                                    @endif
                                    @if($document->status)
                                        <span class="badge bg-{{ $document->status == 'active' ? 'success' : ($document->status == 'draft' ? 'warning' : 'secondary') }}">
                                            {{ $statusOptions[$document->status] ?? $document->status }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted d-block">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $document->created_at->format('d.m.Y H:i') }}
                                    </small>
                                    @if($document->file_size)
                                        <small class="text-muted d-block">
                                            <i class="bi bi-hdd me-1"></i>
                                            {{ number_format($document->file_size / 1024 / 1024, 2) }} МБ
                                        </small>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-sm btn-outline-primary flex-grow-1 me-2" 
                                            onclick="openDocumentView({{ $document->id }})">
                                        <i class="bi bi-eye me-1"></i>Открыть
                                    </button>
                                    @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteDocument({{ $document->id }})" 
                                            title="Удалить документ">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Пагинация -->
            @if($documents->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $documents->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="bi bi-file-text" style="font-size: 4rem; color: #dee2e6;"></i>
                <h5 class="mt-3 text-muted">Документы не найдены</h5>
                <p class="text-muted mb-4">
                    @if($activeFiltersCount > 0)
                        Попробуйте изменить параметры фильтрации
                    @else
                        Загрузите первый документ для этого проекта
                    @endif
                </p>
                @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                    <i class="bi bi-plus-lg me-2"></i>Загрузить документ
                </button>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Модальное окно загрузки документов -->
@if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentModalLabel">
                    <i class="bi bi-upload me-2"></i>Загрузка документов
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <form action="{{ route('partner.projects.documents.upload', $project) }}" method="POST" enctype="multipart/form-data" id="uploadDocumentForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="documentFiles" class="form-label">Выберите документы</label>
                        <input type="file" class="form-control" id="documentFiles" name="documents[]" multiple 
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.rtf,.zip,.rar,.7z,.jpg,.jpeg,.png,.gif,.webp,.svg">
                        <div class="form-text">
                            Поддерживаемые форматы: PDF, Word, Excel, PowerPoint, текстовые файлы, архивы, изображения. 
                            Максимальный размер файла: 50 МБ.
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="documentType" class="form-label">Тип документа</label>
                                <select class="form-select" id="documentType" name="document_type">
                                    <option value="">Выберите тип</option>
                                    @foreach($documentTypeOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="documentStatus" class="form-label">Статус</label>
                                <select class="form-select" id="documentStatus" name="status">
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ $value == 'active' ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="documentDescription" class="form-label">Описание (необязательно)</label>
                        <textarea class="form-control" id="documentDescription" name="description" rows="3" 
                                  placeholder="Краткое описание документа..."></textarea>
                    </div>
                    
                    <!-- Предварительный просмотр выбранных файлов -->
                    <div id="documentPreview" style="display: none;">
                        <hr>
                        <h6>Выбранные файлы:</h6>
                        <div id="documentPreviewList"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary" id="uploadDocumentBtn">
                        <i class="bi bi-upload me-2"></i>Загрузить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    $(document).ready(function() {
        // Обработчик для переключения расширенных фильтров
        $('#toggleDocumentFiltersBtn').on('click', function() {
            $('#advancedDocumentFilters').toggle();
            $(this).find('i').toggleClass('bi-search bi-search-heart');
        });
        
        // Автоотправка формы при изменении основных фильтров
        $('#document_type, #status, #sort').on('change', function() {
            $('#hiddenSubmitBtn').click();
        });
        
        // Предварительный просмотр файлов
        $('#documentFiles').on('change', function() {
            const files = this.files;
            const previewList = $('#documentPreviewList');
            const preview = $('#documentPreview');
            
            previewList.empty();
            
            if (files.length > 0) {
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    
                    previewList.append(`
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <strong>${file.name}</strong>
                                <small class="text-muted d-block">${fileSize} МБ</small>
                            </div>
                            <i class="bi bi-file-text text-primary"></i>
                        </div>
                    `);
                }
                preview.show();
            } else {
                preview.hide();
            }
        });
        
        // Обработчик отправки формы
        $('#uploadDocumentForm').on('submit', function() {
            $('#uploadDocumentBtn').prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-2"></i>Загрузка...');
        });
    });
</script>
