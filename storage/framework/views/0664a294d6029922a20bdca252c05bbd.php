<div class="row">
    <div class="col-12">
        <!-- Панель управления -->
        <?php if($documents->count() > 0): ?>
            <div class="card mb-3 bg-light">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    Выбрать все
                                </label>
                            </div>
                            <div class="btn-group" style="display: none;" id="bulkActions">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="bulkSend">
                                    <i class="fas fa-paper-plane"></i> Отправить
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" id="bulkSign">
                                    <i class="fas fa-signature"></i> Подписать
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="bulkReject">
                                    <i class="fas fa-times"></i> Отклонить
                                </button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">
                                Показано <?php echo e($documents->count()); ?> из <?php echo e($documents->total()); ?>

                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($documents->count() > 0): ?>
            <!-- Карточный вид документов -->
            <div class="row g-2">
                <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                        <div class="card document-card h-100 shadow-sm" data-document-id="<?php echo e($document->id); ?>">
                            <div class="card-header d-flex justify-content-between align-items-start p-1 bg-light">
                                <div class="form-check mb-0">
                                    <input class="form-check-input document-checkbox" type="checkbox" 
                                           value="<?php echo e($document->id); ?>">
                                </div>
                                <div class="d-flex flex-column align-items-end gap-1">
                                    <?php switch($document->status):
                                        case ('draft'): ?>
                                            <span class="badge bg-secondary badge-sm">Черновик</span>
                                            <?php break; ?>
                                        <?php case ('sent'): ?>
                                            <span class="badge bg-info badge-sm">Отправлен</span>
                                            <?php break; ?>
                                        <?php case ('received'): ?>
                                            <span class="badge bg-primary badge-sm">Получен</span>
                                            <?php break; ?>
                                        <?php case ('signed'): ?>
                                            <span class="badge bg-success badge-sm">Подписан</span>
                                            <?php break; ?>
                                    <?php endswitch; ?>
                                    
                                    <?php if($document->signature_status === 'pending'): ?>
                                        <span class="badge bg-warning badge-sm">Ожидает</span>
                                    <?php elseif($document->signature_status === 'signed'): ?>
                                        <span class="badge bg-success badge-sm">✓</span>
                                    <?php elseif($document->signature_status === 'rejected'): ?>
                                        <span class="badge bg-danger badge-sm">✗</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="card-body p-2">
                                <div class="document-preview text-center mb-2">
                                    <?php if($document->file_path): ?>
                                        <i class="fas fa-file-alt fa-2x text-primary"></i>
                                    <?php else: ?>
                                        <i class="fas fa-file-text fa-2x text-secondary"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <h6 class="card-title mb-2 text-center" style="font-size: 0.75rem; line-height: 1.1;">
                                    <a href="<?php echo e(route('documents.show', $document)); ?>" 
                                       class="text-decoration-none text-truncate d-block" 
                                       title="<?php echo e($document->title); ?>">
                                        <?php echo e(Str::limit($document->title, 20)); ?>

                                    </a>
                                </h6>
                                
                                <div class="mb-2 text-center">
                                    <span class="badge bg-secondary badge-sm" style="font-size: 0.6rem;">
                                        <?php echo e(Str::limit($document->type_name ?? 'Тип', 10)); ?>

                                    </span>
                                    <?php if($document->template): ?>
                                        <small class="text-info d-block mt-1" style="font-size: 0.65rem;">
                                            <i class="fas fa-file-alt"></i> <?php echo e(Str::limit($document->template->name, 12)); ?>

                                        </small>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if($document->project): ?>
                                    <div class="mb-2 text-center">
                                        <small class="text-muted" style="font-size: 0.65rem;">
                                            <i class="fas fa-building"></i>
                                            <?php echo e(Str::limit($document->project->client_last_name . ' ' . $document->project->client_first_name, 12)); ?>

                                        </small>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="text-muted text-center" style="font-size: 0.65rem;">
                                    <?php echo e($document->created_at->format('d.m.Y')); ?>

                                    <br><?php echo e($document->created_at->format('H:i')); ?>

                                    <?php if($document->expires_at): ?>
                                        <div class="text-warning mt-1">
                                            <i class="fas fa-clock"></i> <?php echo e($document->expires_at->format('d.m')); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="card-footer p-1">
                                <div class="btn-group w-100" role="group">
                                    <a href="<?php echo e(route('documents.show', $document)); ?>" 
                                       class="btn btn-outline-primary btn-sm" title="Просмотр">
                                        <i class="fas fa-eye" style="font-size: 0.7rem;"></i>
                                    </a>
                                    
                                    <?php if($document->file_path): ?>
                                        <a href="<?php echo e(route('documents.download', $document)); ?>" 
                                           class="btn btn-outline-secondary btn-sm" title="Скачать">
                                            <i class="fas fa-download" style="font-size: 0.7rem;"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if($document->signature_status === 'pending' && 
                                        ($document->recipient_id === auth()->id() || 
                                         $document->recipient_email === auth()->user()->email || 
                                         $document->recipient_phone === auth()->user()->phone)): ?>
                                        <button type="button" 
                                                class="btn btn-outline-success btn-sm btn-sign" 
                                                data-document-id="<?php echo e($document->id); ?>" 
                                                data-document-title="<?php echo e($document->title); ?>"
                                                title="Подписать">
                                            <i class="fas fa-signature" style="font-size: 0.7rem;"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-sm btn-reject" 
                                                data-document-id="<?php echo e($document->id); ?>" 
                                                data-document-title="<?php echo e($document->title); ?>"
                                                title="Отклонить">
                                            <i class="fas fa-times" style="font-size: 0.7rem;"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if($tab === 'created' && $document->status === 'draft'): ?>
                                        <button type="button" 
                                                class="btn btn-outline-info btn-sm btn-send" 
                                                data-document-id="<?php echo e($document->id); ?>" title="Отправить">
                                            <i class="fas fa-paper-plane" style="font-size: 0.7rem;"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <!-- Пагинация -->
            <div class="d-flex justify-content-center mt-4">
                <?php if($documents->hasPages()): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            
                            <?php if($documents->onFirstPage()): ?>
                                <li class="page-item disabled"><span class="page-link">‹</span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="#" data-page="<?php echo e($documents->currentPage() - 1); ?>" data-tab="<?php echo e($tab); ?>">‹</a></li>
                            <?php endif; ?>

                            
                            <?php
                                $start = max($documents->currentPage() - 2, 1);
                                $end = min($start + 4, $documents->lastPage());
                                $start = max($end - 4, 1);
                            ?>

                            <?php if($start > 1): ?>
                                <li class="page-item"><a class="page-link" href="#" data-page="1" data-tab="<?php echo e($tab); ?>">1</a></li>
                                <?php if($start > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for($page = $start; $page <= $end; $page++): ?>
                                <?php if($page == $documents->currentPage()): ?>
                                    <li class="page-item active"><span class="page-link"><?php echo e($page); ?></span></li>
                                <?php else: ?>
                                    <li class="page-item"><a class="page-link" href="#" data-page="<?php echo e($page); ?>" data-tab="<?php echo e($tab); ?>"><?php echo e($page); ?></a></li>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if($end < $documents->lastPage()): ?>
                                <?php if($end < $documents->lastPage() - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item"><a class="page-link" href="#" data-page="<?php echo e($documents->lastPage()); ?>" data-tab="<?php echo e($tab); ?>"><?php echo e($documents->lastPage()); ?></a></li>
                            <?php endif; ?>

                            
                            <?php if($documents->hasMorePages()): ?>
                                <li class="page-item"><a class="page-link" href="#" data-page="<?php echo e($documents->currentPage() + 1); ?>" data-tab="<?php echo e($tab); ?>">›</a></li>
                            <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">›</span></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-inbox fa-4x text-muted"></i>
                </div>
                <h5 class="text-muted">
                    <?php if($tab === 'received'): ?>
                        Нет полученных документов
                    <?php elseif($tab === 'created'): ?>
                        Вы еще не создали ни одного документа
                    <?php elseif($tab === 'signed'): ?>
                        Нет подписанных документов
                    <?php endif; ?>
                </h5>
                <p class="text-muted mb-4">
                    <?php if($tab === 'received'): ?>
                        Здесь будут отображаться документы, которые вам отправили на подписание или ознакомление
                    <?php elseif($tab === 'created'): ?>
                        Здесь будут отображаться отправленные вами документы
                    <?php elseif($tab === 'signed'): ?>
                        Здесь будут отображаться подписанные вами документы
                    <?php endif; ?>
                </p>
                
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Множественный выбор
    const selectAllCheckbox = document.getElementById('selectAll');
    const documentCheckboxes = document.querySelectorAll('.document-checkbox');
    const bulkActions = document.getElementById('bulkActions');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            documentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    documentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActions();
            
            // Обновляем состояние "Выбрать все"
            if (selectAllCheckbox) {
                const checkedCount = document.querySelectorAll('.document-checkbox:checked').length;
                selectAllCheckbox.checked = checkedCount === documentCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < documentCheckboxes.length;
            }
        });
    });

    function updateBulkActions() {
        const checkedCount = document.querySelectorAll('.document-checkbox:checked').length;
        if (bulkActions) {
            bulkActions.style.display = checkedCount > 0 ? 'block' : 'none';
        }
    }

    // Принудительная очистка backdrop при закрытии боковых панелей
    document.addEventListener('hidden.bs.offcanvas', function (event) {
        // Удаляем все backdrop элементы
        const backdrops = document.querySelectorAll('.modal-backdrop, .offcanvas-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        
        // Восстанавливаем прокрутку body
        document.body.classList.remove('modal-open', 'offcanvas-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });

    // Инициализация обработчиков кнопок
    console.log('Initializing document handlers...'); // Отладка
    initializeAllDocumentHandlers();
});

// Глобальная функция для инициализации всех обработчиков
window.initializeAllDocumentHandlers = function() {
    console.log('Initializing all document handlers...'); // Отладка
    initializeSignatureHandlers();
    initializeDocumentHandlers();
    initializeBulkActions();
}

// Инициализация массовых действий
window.initializeBulkActions = function() {
    const bulkSignBtn = document.getElementById('bulkSign');
    const bulkRejectBtn = document.getElementById('bulkReject');
    const bulkSendBtn = document.getElementById('bulkSend');
    
    if (bulkSignBtn) {
        bulkSignBtn.addEventListener('click', function() {
            const selectedDocuments = getSelectedDocuments();
            if (selectedDocuments.length === 0) {
                showAlert('warning', 'Выберите документы для подписания');
                return;
            }
            
            if (confirm(`Вы уверены, что хотите подписать ${selectedDocuments.length} документ(ов)?`)) {
                bulkActionSignDocuments(selectedDocuments);
            }
        });
    }
    
    if (bulkRejectBtn) {
        bulkRejectBtn.addEventListener('click', function() {
            const selectedDocuments = getSelectedDocuments();
            if (selectedDocuments.length === 0) {
                showAlert('warning', 'Выберите документы для отклонения');
                return;
            }
            
            const reason = prompt(`Укажите причину отклонения ${selectedDocuments.length} документ(ов):`);
            if (reason && reason.trim()) {
                bulkActionRejectDocuments(selectedDocuments, reason.trim());
            }
        });
    }
    
    if (bulkSendBtn) {
        bulkSendBtn.addEventListener('click', function() {
            const selectedDocuments = getSelectedDocuments();
            if (selectedDocuments.length === 0) {
                showAlert('warning', 'Выберите документы для отправки');
                return;
            }
            
            if (confirm(`Вы уверены, что хотите отправить ${selectedDocuments.length} документ(ов)?`)) {
                bulkActionSendDocuments(selectedDocuments);
            }
        });
    }
}

// Получить выбранные документы
window.getSelectedDocuments = function() {
    const checkboxes = document.querySelectorAll('.document-checkbox:checked');
    return Array.from(checkboxes).map(checkbox => checkbox.value);
}

// Массовое подписание документов
window.bulkActionSignDocuments = function(documentIds) {
    const promises = documentIds.map(documentId => {
        return signDocumentRequest(documentId, '<?php echo e(auth()->user()->name); ?>', true);
    });
    
    Promise.allSettled(promises)
        .then(results => {
            const successful = results.filter(result => result.status === 'fulfilled').length;
            const failed = results.filter(result => result.status === 'rejected').length;
            
            if (successful > 0) {
                showAlert('success', `Успешно подписано: ${successful} документ(ов)`);
            }
            if (failed > 0) {
                showAlert('error', `Ошибка при подписании: ${failed} документ(ов)`);
            }
            
            // Перезагружаем текущую вкладку
            const currentTab = document.querySelector('.nav-link.active')?.dataset.tab;
            if (currentTab && typeof window.loadTabContent === 'function') {
                window.loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
            } else {
                window.location.reload();
            }
        });
}

// Массовое отклонение документов
function bulkActionRejectDocuments(documentIds, reason) {
    const promises = documentIds.map(documentId => {
        return fetch(`/documents/${documentId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                reason: reason
            })
        });
    });
    
    Promise.allSettled(promises)
        .then(results => {
            const successful = results.filter(result => result.status === 'fulfilled').length;
            const failed = results.filter(result => result.status === 'rejected').length;
            
            if (successful > 0) {
                showAlert('success', `Успешно отклонено: ${successful} документ(ов)`);
            }
            if (failed > 0) {
                showAlert('error', `Ошибка при отклонении: ${failed} документ(ов)`);
            }
            
            // Перезагружаем текущую вкладку
            const currentTab = document.querySelector('.nav-link.active')?.dataset.tab;
            if (currentTab && typeof window.loadTabContent === 'function') {
                window.loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
            } else {
                window.location.reload();
            }
        });
}

// Массовая отправка документов
function bulkActionSendDocuments(documentIds) {
    const promises = documentIds.map(documentId => {
        return fetch(`/documents/${documentId}/send`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        });
    });
    
    Promise.allSettled(promises)
        .then(results => {
            const successful = results.filter(result => result.status === 'fulfilled').length;
            const failed = results.filter(result => result.status === 'rejected').length;
            
            if (successful > 0) {
                showAlert('success', `Успешно отправлено: ${successful} документ(ов)`);
            }
            if (failed > 0) {
                showAlert('error', `Ошибка при отправке: ${failed} документ(ов)`);
            }
            
            // Перезагружаем текущую вкладку
            const currentTab = document.querySelector('.nav-link.active')?.dataset.tab;
            if (currentTab && typeof window.loadTabContent === 'function') {
                window.loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
            } else {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error in bulk send:', error);
            showAlert('error', 'Произошла ошибка при массовой отправке документов');
        });
}

// Инициализация обработчиков подписи
window.initializeSignatureHandlers = function() {
    console.log('Initializing signature handlers...'); // Отладка
    
    // Проверяем, что Bootstrap доступен
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not loaded!');
        return;
    }
    
    const signButtons = document.querySelectorAll('.btn-sign');
    console.log('Found sign buttons:', signButtons.length); // Отладка
    
    signButtons.forEach(button => {
        // Удаляем предыдущие обработчики, если есть
        button.removeEventListener('click', button._signHandler);
        
        const signHandler = function() {
            const documentId = this.dataset.documentId;
            const documentTitle = this.dataset.documentTitle;
            
            console.log('Sign button clicked:', {documentId, documentTitle}); // Для отладки
            
            // Тестовое уведомление для проверки работы обработчика
            showAlert('info', 'Кнопка подписания была нажата! ID документа: ' + documentId);
            
            // Очищаем существующие backdrop перед открытием нового offcanvas
            cleanupModalBackdrops();
            
            // Устанавливаем данные в боковую панель подписания  
            const signatureTextElement = document.getElementById('signatureText');
            const agreementElement = document.getElementById('signAgreement');
            const documentIdElement = document.getElementById('signDocumentId');
            
            if (signatureTextElement) signatureTextElement.value = '';
            if (agreementElement) agreementElement.checked = false;
            if (documentIdElement) documentIdElement.value = documentId;
            
            // Устанавливаем название документа
            const titleElement = document.getElementById('signDocumentTitle');
            if (titleElement) titleElement.textContent = documentTitle;
            
            const offcanvasElement = document.getElementById('signOffcanvas');
            if (offcanvasElement) {
                console.log('Opening signOffcanvas...');
                const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
                offcanvas.show();
            } else {
                console.error('signOffcanvas element not found!');
            }
        };
        
        // Сохраняем ссылку на обработчик и добавляем его
        button._signHandler = signHandler;
        button.addEventListener('click', signHandler);
    });
}

// Инициализация обработчиков документов
window.initializeDocumentHandlers = function() {
    console.log('Initializing document handlers...'); // Отладка
    
    // Проверяем, что Bootstrap доступен
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not loaded!');
        return;
    }
    
    // Обработка отправки документов
    document.querySelectorAll('.btn-send').forEach(button => {
        button.addEventListener('click', function() {
            const documentId = this.dataset.documentId;
            
            if (confirm('Вы уверены, что хотите отправить этот документ?')) {
                fetch(`/documents/${documentId}/send`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        // Перезагружаем текущую вкладку
                        const currentTab = document.querySelector('.nav-link.active')?.dataset.tab;
                        if (currentTab && typeof window.loadTabContent === 'function') {
                            window.loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
                        } else {
                            window.location.reload();
                        }
                    } else {
                        showAlert('error', data.message);
                    }
                })
                .catch(error => {
                    showAlert('error', 'Ошибка при отправке документа');
                });
            }
        });
    });

    // Обработка отклонения документов
    const rejectButtons = document.querySelectorAll('.btn-reject');
    console.log('Found reject buttons:', rejectButtons.length); // Отладка
    
    rejectButtons.forEach(button => {
        // Удаляем предыдущие обработчики, если есть
        button.removeEventListener('click', button._rejectHandler);
        
        const rejectHandler = function() {
            const documentId = this.dataset.documentId;
            const documentTitle = this.dataset.documentTitle;
            
            console.log('Reject button clicked:', {documentId, documentTitle}); // Для отладки
            
            // Тестовое уведомление для проверки работы обработчика
            showAlert('warning', 'Кнопка отклонения была нажата! ID документа: ' + documentId);
            
            // Очищаем существующие backdrop перед открытием нового offcanvas
            cleanupModalBackdrops();
            
            // Устанавливаем данные в боковую панель отклонения
            const documentIdElement = document.getElementById('rejectDocumentId');
            const titleElement = document.getElementById('rejectDocumentTitle');
            const reasonElement = document.getElementById('rejectionReason');
            
            if (documentIdElement) documentIdElement.value = documentId;
            if (titleElement) titleElement.textContent = documentTitle;
            if (reasonElement) reasonElement.value = '';
            
            const offcanvasElement = document.getElementById('rejectOffcanvas');
            if (offcanvasElement) {
                console.log('Opening rejectOffcanvas...');
                const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
                offcanvas.show();
            } else {
                console.error('rejectOffcanvas element not found!');
            }
        };
        
        // Сохраняем ссылку на обработчик и добавляем его
        button._rejectHandler = rejectHandler;
        button.addEventListener('click', rejectHandler);
    });
}

// Функция очистки backdrop'ов
window.cleanupModalBackdrops = function() {
    // Удаляем все существующие backdrop
    const backdrops = document.querySelectorAll('.modal-backdrop, .offcanvas-backdrop');
    backdrops.forEach(backdrop => backdrop.remove());
    
    // Восстанавливаем прокрутку body
    document.body.classList.remove('modal-open', 'offcanvas-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

// Универсальная функция для подписи документа
window.signDocumentRequest = function(documentId, signature, agreement = true) {
    console.log('Signing document:', {
        documentId: documentId,
        signature: signature,
        agreement: agreement
    });
    
    // Используем FormData вместо JSON для лучшей совместимости
    const formData = new FormData();
    formData.append('signature', signature);
    formData.append('agreement', agreement ? '1' : '0');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    return fetch(`/documents/${documentId}/sign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        // Проверяем, является ли ответ JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error(`Сервер вернул неожиданный тип ответа: ${contentType}`);
        }
        
        // Проверяем статус ответа
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(errorData.message || `HTTP Error: ${response.status}`);
            });
        }
        
        return response.json();
    });
}

// Обработчик для формы подписания документа (только если не обработан в родительской странице)
document.addEventListener('DOMContentLoaded', function() {
    const signForm = document.getElementById('signDocumentForm');
    if (signForm && !signForm._submitHandler) {
        const submitHandler = function(e) {
            e.preventDefault();
            
            const documentIdElement = document.getElementById('signDocumentId');
            const signatureElement = document.getElementById('signatureText');
            const agreementElement = document.getElementById('signAgreement');
            
            const documentId = documentIdElement ? documentIdElement.value : '';
            const signature = signatureElement ? signatureElement.value : '';
            const agreement = agreementElement ? agreementElement.checked : false;
            
            if (!signature.trim()) {
                showAlert('error', 'Введите вашу подпись');
                return;
            }
            
            if (!agreement) {
                showAlert('error', 'Необходимо согласиться с условиями');
                return;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]') || document.querySelector('button[form="signDocumentForm"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Подписание...';
            }
            
            signDocumentRequest(documentId, signature, agreement)
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || 'Документ успешно подписан');
                    
                    // Закрываем боковую панель и очищаем backdrop
                    const offcanvasElement = document.getElementById('signOffcanvas');
                    if (offcanvasElement) {
                        const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                        if (offcanvas) {
                            offcanvas.hide();
                        }
                    }
                    cleanupModalBackdrops();
                    
                    // Перезагружаем текущую вкладку
                    const currentTab = document.querySelector('.nav-link.active')?.dataset.tab;
                    if (currentTab && typeof window.loadTabContent === 'function') {
                        window.loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
                    } else {
                        // Если функция недоступна, перезагружаем страницу
                        window.location.reload();
                    }
                } else {
                    showAlert('error', data.message || 'Ошибка при подписании документа');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', error.message || 'Произошла ошибка при подписании документа');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-signature me-1"></i>Подписать';
                }
            });
        };
        
        // Сохраняем ссылку на обработчик и добавляем его
        signForm._submitHandler = submitHandler;
        signForm.addEventListener('submit', submitHandler);
    }
    
    // Обработчик для формы отклонения документа
    const rejectForm = document.getElementById('rejectDocumentForm');
    if (rejectForm && !rejectForm._submitHandler) {
        const rejectSubmitHandler = function(e) {
            e.preventDefault();
            
            const documentId = document.getElementById('rejectDocumentId').value;
            const reason = document.getElementById('rejectionReason').value;
            
            if (!reason.trim()) {
                showAlert('error', 'Укажите причину отклонения');
                return;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]') || document.querySelector('button[form="rejectDocumentForm"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Отклонение...';
            }
            
            fetch(`/documents/${documentId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || 'Документ отклонен');
                    
                    // Закрываем боковую панель и очищаем backdrop
                    const offcanvasElement = document.getElementById('rejectOffcanvas');
                    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if (offcanvas) {
                        offcanvas.hide();
                    }
                    cleanupModalBackdrops();
                    
                    // Перезагружаем текущую вкладку
                    const currentTab = document.querySelector('.nav-link.active')?.dataset.tab;
                    if (currentTab && typeof window.loadTabContent === 'function') {
                        window.loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
                    } else {
                        // Если функция недоступна, перезагружаем страницу
                        window.location.reload();
                    }
                } else {
                    showAlert('error', data.message || 'Ошибка при отклонении документа');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Ошибка при отклонении документа');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-times me-1"></i>Отклонить';
                }
            });
        };
        
        // Сохраняем ссылку на обработчик и добавляем его
        rejectForm._submitHandler = rejectSubmitHandler;
        rejectForm.addEventListener('submit', rejectSubmitHandler);
    }
});

// Функция показа уведомлений
window.showAlert = function(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>

<!-- Боковая панель подписания документа -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="signOffcanvas" aria-labelledby="signOffcanvasLabel">
    <div class="offcanvas-header bg-success text-white">
        <h5 class="offcanvas-title" id="signOffcanvasLabel">
            <i class="fas fa-signature me-2"></i>Подписание документа
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="signDocumentForm">
            <?php echo csrf_field(); ?>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Вы подписываете документ: <strong id="signDocumentTitle"></strong>
            </div>
            
            <div class="mb-3">
                <label for="signatureText" class="form-label">Введите вашу подпись:</label>
                <input type="text" class="form-control" id="signatureText" name="signature" 
                       placeholder="Ваше полное имя" required>
                <div class="form-text">Введите ваше полное имя в качестве электронной подписи</div>
            </div>
            
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="signAgreement" name="agreement" required>
                <label class="form-check-label" for="signAgreement">
                    Я подтверждаю, что ознакомился с содержанием документа и согласен с его условиями
                </label>
            </div>
            
            <input type="hidden" id="signDocumentId" name="document_id">
        </form>
    </div>
    
    <div class="offcanvas-footer border-top p-3">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                <i class="fas fa-times me-1"></i>Отмена
            </button>
            <button type="submit" form="signDocumentForm" class="btn btn-success flex-fill">
                <i class="fas fa-signature me-1"></i>Подписать
            </button>
        </div>
    </div>
</div>

<!-- Боковая панель отклонения документа -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="rejectOffcanvas" aria-labelledby="rejectOffcanvasLabel">
    <div class="offcanvas-header bg-danger text-white">
        <h5 class="offcanvas-title" id="rejectOffcanvasLabel">
            <i class="fas fa-times me-2"></i>Отклонение документа
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="rejectDocumentForm">
            <?php echo csrf_field(); ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Вы отклоняете документ: <strong id="rejectDocumentTitle"></strong>
            </div>
            
            <div class="mb-3">
                <label for="rejectionReason" class="form-label required">Причина отклонения</label>
                <textarea class="form-control" id="rejectionReason" name="reason" rows="4" 
                          placeholder="Укажите причину отклонения документа..." required></textarea>
                <div class="form-text">Опишите причину, по которой вы отклоняете этот документ</div>
            </div>
            
            <input type="hidden" id="rejectDocumentId" name="document_id">
        </form>
    </div>
    
    <div class="offcanvas-footer border-top p-3">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                <i class="fas fa-arrow-left me-1"></i>Назад
            </button>
            <button type="submit" form="rejectDocumentForm" class="btn btn-danger flex-fill">
                <i class="fas fa-times me-1"></i>Отклонить
            </button>
        </div>
    </div>
</div>

<style>
/* Стили для боковых панелей */
.offcanvas-end {
    width: 450px !important;
    max-width: 90vw;
}

.offcanvas-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.offcanvas-header {
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

/* Стили для обязательных полей */
.form-label.required::after {
    content: " *";
    color: #dc3545;
}

/* Адаптивность для мобильных устройств */
@media (max-width: 768px) {
    .offcanvas-end {
        width: 100% !important;
    }
}

/* Компактные карточки документов */
.document-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #e9ecef;
    max-height: 280px;
    min-height: 240px;
    border-radius: 0.5rem;
}

.document-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    border-color: #007bff;
}

.document-card .card-header {
    min-height: 35px;
    border-bottom: 1px solid #e9ecef;
    padding: 0.25rem !important;
}

.document-card .card-body {
    padding: 6px !important;
    overflow: hidden;
}

.document-card .card-footer {
    padding: 2px !important;
    border-top: 1px solid #e9ecef;
    background-color: #f8f9fa;
}

.document-card .card-title {
    font-size: 0.75rem !important;
    line-height: 1.1 !important;
    height: 2.2rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    margin-bottom: 0.5rem !important;
}

.document-card .btn-group .btn {
    font-size: 0.7rem;
    padding: 0.15rem 0.25rem;
    border-radius: 0.2rem;
    line-height: 1;
}

.document-card .btn-group .btn i {
    font-size: 0.7rem;
}

/* Компактные бейджи */
.badge-sm {
    font-size: 0.6rem;
    padding: 0.2em 0.3em;
    line-height: 1;
}

/* Улучшенная типографика */
.document-card small {
    font-size: 0.65rem;
    line-height: 1.1;
}

/* Адаптивная сетка */
@media (max-width: 576px) {
    .document-card {
        min-height: 220px;
        max-height: 250px;
    }
    
    .document-card .card-title {
        font-size: 0.7rem !important;
        height: 2rem;
        -webkit-line-clamp: 2;
    }
    
    .document-card .btn-group .btn {
        font-size: 0.65rem;
        padding: 0.1rem 0.2rem;
    }
    
    .document-card .btn-group .btn i {
        font-size: 0.65rem;
    }
}

@media (min-width: 576px) and (max-width: 768px) {
    .col-sm-4 {
        flex: 0 0 auto;
        width: 25%;
    }
}

@media (min-width: 768px) and (max-width: 992px) {
    .col-md-3 {
        flex: 0 0 auto;
        width: 20%;
    }
}

@media (min-width: 992px) {
    .col-lg-2 {
        flex: 0 0 auto;
        width: 16.66666667%;
    }
}

@media (min-width: 1200px) {
    .col-xl-2 {
        flex: 0 0 auto;
        width: 14.285714%; /* 1/7 */
    }
}

@media (min-width: 1400px) {
    .col-xxl-2 {
        flex: 0 0 auto;
        width: 12.5%; /* 1/8 */
    }
}

/* Анимация загрузки */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

/* Устранение проблем с backdrop */
.modal.show {
    display: block !important;
}

.modal-backdrop.show {
    opacity: 0.5;
}

/* Фиксация проблем с z-index Bootstrap */
.modal-backdrop.fade.show {
    z-index: 1054 !important;
}

.modal.fade.show {
    z-index: 1055 !important;
}

.modal-dialog {
    z-index: 1056 !important;
    position: relative;
}
</style>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/documents/partials/documents-tab.blade.php ENDPATH**/ ?>