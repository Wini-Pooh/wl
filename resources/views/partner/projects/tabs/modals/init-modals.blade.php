<!-- Исправленная инициализация модальных окон для всех вкладок проекта -->
<!-- Версия 3.0 - Исправление дублирования событий -->

<!-- Базовый контейнер для уведомлений -->
<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>

<!-- Модальные окна работают без AJAX запросов -->
<div id="modalContainer"></div>

<!-- Общие стили для модальных окон -->
<style>
.modal-dialog {
    max-width: 800px;
}

.img-thumbnail {
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.progress {
    height: 8px;
    background-color: #e9ecef;
    border-radius: 4px;
}

.progress-bar {
    height: 100%;
    background-color: #007bff;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.upload-zone {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-zone:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.upload-zone.dragover {
    border-color: #007bff;
    background-color: #e7f1ff;
}

.file-list {
    max-height: 300px;
    overflow-y: auto;
}

.file-item {
    display: flex;
    align-items: center;
    padding: 8px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    margin-bottom: 8px;
    background: white;
}

.file-item .file-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    border-radius: 4px;
    background: #f8f9fa;
}

.file-item .file-info {
    flex: 1;
    min-width: 0;
}

.file-item .file-name {
    font-weight: 500;
    margin-bottom: 2px;
    word-break: break-all;
}

.file-item .file-size {
    font-size: 0.875rem;
    color: #6c757d;
}

.file-item .file-actions {
    display: flex;
    gap: 8px;
}

.modal-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
}

.toast {
    border: none;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}
</style>

<script>
/**
 * Исправленный менеджер модальных окон для проектов
 * Версия 3.0 - Исправление дублирования событий
 */
class ProjectModalManagerFixed {
    constructor(projectId) {
        this.projectId = projectId;
        this.activeModal = null;
        this.isInitialized = false;
        this.modalHandlers = new Map(); // Карта для отслеживания обработчиков
        
        console.log('🚀 Инициализация ProjectModalManagerFixed для проекта:', this.projectId);
        
        // Предотвращаем повторную инициализацию
        if (window.modalManagerInstance) {
            console.warn('⚠️ Modal Manager уже инициализирован, используем существующий экземпляр');
            return window.modalManagerInstance;
        }
        
        window.modalManagerInstance = this;
        this.init();
    }

    init() {
        if (this.isInitialized) {
            console.warn('⚠️ Modal Manager уже инициализирован');
            return;
        }
        
        console.log('🎯 Инициализация ProjectModalManagerFixed...');
        
        // Очищаем любые остаточные backdrop'ы
        this.cleanupBackdrops();
        
        // Инициализируем обработчики для всех модальных кнопок
        this.initModalHandlers();
        
        // AJAX настройки отключены (AJAX запросы удалены)
        // this.setupAjax();
        
        this.isInitialized = true;
        console.log('✅ Modal Manager успешно инициализирован');
    }

    setupAjax() {
        // AJAX настройки отключены
        console.log('ℹ️ AJAX настройки отключены');
        /*
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        */
    }

    initModalHandlers() {
        console.log('🔧 Инициализация обработчиков модальных окон...');
        
        // КРИТИЧЕСКИ ВАЖНО: Полностью удаляем ВСЕ старые обработчики
        this.removeAllModalHandlers();
        
        // Добавляем ЕДИНСТВЕННЫЙ универсальный обработчик с уникальным namespace
        $(document).on('click.modalManagerFixed', '[data-modal-type]', (e) => {
            e.preventDefault();
            e.stopImmediatePropagation(); // Останавливаем распространение немедленно
            
            const $button = $(e.currentTarget);
            const modalType = $button.data('modal-type');
            
            console.log('🔘 Клик по модальной кнопке:', modalType, 'time:', Date.now());
            
            // Проверяем, не заблокирована ли кнопка
            if ($button.prop('disabled') || $button.hasClass('loading')) {
                console.log('🚫 Кнопка заблокирована, игнорируем клик');
                return false;
            }
            
            // Усиленная защита от двойных кликов
            const lastClick = $button.data('last-click') || 0;
            const currentTime = Date.now();
            
            if (currentTime - lastClick < 1500) { // Увеличиваем время до 1.5 секунд
                console.log('🚫 Двойной клик обнаружен, игнорируем. Last:', lastClick, 'Current:', currentTime);
                return false;
            }
            
            $button.data('last-click', currentTime);
            
            // Блокируем кнопку на время обработки
            $button.prop('disabled', true).addClass('loading');
            
            console.log('✅ Обработка клика по модальной кнопке:', modalType);
            
            // Обрабатываем клик без задержки
            this.handleModalClick(modalType, $button);
            
            return false;
        });
        
        // Обработчик для очистки потерянных backdrop'ов
        $(document).on('click.modalManagerFixed', '.modal-backdrop', (e) => {
            console.log('🧹 Клик по backdrop, проверяем необходимость очистки...');
            this.cleanupBackdropsDelayed();
        });
        
        console.log('✅ Обработчики модальных окон инициализированы');
    }

    removeAllModalHandlers() {
        console.log('🧹 Удаление всех старых обработчиков модальных окон...');
        
        // Удаляем все обработчики с любыми modal namespace
        $(document).off('.modalManager');
        $(document).off('.modalManagerFixed');
        $(document).off('.modalClick');
        $(document).off('.projectModal');
        
        // Удаляем все другие возможные обработчики для модальных кнопок
        $(document).off('click', '[data-modal-type]');
        $('[data-modal-type]').off('click');
        
        // Очищаем карту обработчиков
        this.modalHandlers.clear();
        
        // Сбрасываем флаги инициализации файловых обработчиков
        window.photoUploadHandlersInitialized = false;
        window.schemeUploadHandlersInitialized = false;
        window.designUploadHandlersInitialized = false;
        window.documentUploadHandlersInitialized = false;
        
        console.log('🧹 Все обработчики модальных окон удалены');
    }

    handleModalClick(modalType, $button) {
        console.log('🎯 Обработка клика по модальному окну:', modalType);
        
        try {
            // Определяем, является ли модальное окно статическим
            const staticModals = ['photo', 'scheme', 'design', 'document', 'stage', 'event'];
            
            if (staticModals.includes(modalType)) {
                this.showStaticModal(modalType);
            } else {
                this.loadDynamicModal(modalType);
            }
        } catch (error) {
            console.error('❌ Ошибка при обработке клика по модальному окну:', error);
            this.showErrorToast('Ошибка открытия модального окна');
        } finally {
            // Разблокируем кнопку через 2 секунды для предотвращения быстрых двойных кликов
            setTimeout(() => {
                $button.prop('disabled', false).removeClass('loading');
            }, 2000);
        }
    }

    showStaticModal(modalType) {
        console.log(`📋 Открытие статического модального окна: ${modalType}`);
        
        // Определяем ID модального окна
        let modalId;
        switch(modalType) {
            case 'photo':
                modalId = 'uploadPhotoModal';
                break;
            case 'scheme':
                modalId = 'uploadSchemeModal';
                break;
            case 'design':
                modalId = 'uploadDesignModal';
                break;
            case 'document':
                modalId = 'documentPageModal';
                break;
            case 'stage':
                modalId = 'stageModal';
                break;
            case 'event':
                modalId = 'eventModal';
                break;
            default:
                console.error(`❌ Неизвестный тип статического модального окна: ${modalType}`);
                this.showErrorToast(`Неизвестный тип модального окна: ${modalType}`);
                return;
        }
        
        const modalElement = document.getElementById(modalId);
        if (!modalElement) {
            console.error(`❌ Модальное окно ${modalId} не найдено в DOM`);
            this.showErrorToast(`Модальное окно ${modalType} не найдено`);
            return;
        }
        
        // Закрываем любые открытые модальные окна
        this.closeActiveModal();
        
        // Очищаем backdrop'ы
        this.cleanupBackdrops();
        
        try {
            // Инициализируем Bootstrap модальное окно
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: true,
                focus: true
            });
            
            this.activeModal = modal;
            
            // Инициализируем обработчики для конкретного типа модального окна
            this.initSpecificModalHandlers(modalType, modalElement);
            
            // Показываем модальное окно
            modal.show();
            
            // Обработчик закрытия с уникальным namespace
            $(modalElement).off('hidden.bs.modal.staticModal').on('hidden.bs.modal.staticModal', () => {
                this.onModalClosed();
            });
            
            console.log(`✅ Статическое модальное окно ${modalType} успешно открыто`);
            
        } catch (error) {
            console.error(`❌ Ошибка при открытии статического модального окна ${modalType}:`, error);
            this.showErrorToast('Ошибка открытия модального окна');
        }
    }

    initSpecificModalHandlers(modalType, modalElement) {
        console.log(`🔧 Инициализация специфичных обработчиков для: ${modalType}`);
        
        // Проверяем, не были ли уже инициализированы обработчики для этого модального окна
        const handlerKey = `${modalType}_${modalElement.id}`;
        if (this.modalHandlers.has(handlerKey)) {
            console.log(`ℹ️ Обработчики для ${modalType} уже инициализированы`);
            return;
        }
        
        switch(modalType) {
            case 'photo':
                this.initPhotoHandlers(modalElement);
                break;
            case 'scheme':
                this.initSchemeHandlers(modalElement);
                break;
            case 'design':
                this.initDesignHandlers(modalElement);
                break;
            case 'document':
                this.initDocumentHandlers(modalElement);
                break;
            case 'stage':
                this.initStageHandlers(modalElement);
                break;
            case 'event':
                this.initEventHandlers(modalElement);
                break;
        }
        
        // Отмечаем, что обработчики для этого модального окна инициализированы
        this.modalHandlers.set(handlerKey, true);
    }

    initPhotoHandlers(modalElement) {
        console.log('📸 Инициализация обработчиков фотографий...');
        if (typeof initPhotoModalHandlers === 'function') {
            initPhotoModalHandlers();
        }
    }
    
    initSchemeHandlers(modalElement) {
        console.log('📋 Инициализация обработчиков схем...');
        if (typeof initSchemeModalHandlers === 'function') {
            initSchemeModalHandlers();
        }
    }
    
    initDesignHandlers(modalElement) {
        console.log('🎨 Инициализация обработчиков дизайна...');
        if (typeof initDesignModalHandlers === 'function') {
            initDesignModalHandlers();
        }
    }
    
    initDocumentHandlers(modalElement) {
        console.log('📄 Инициализация обработчиков документов...');
        if (typeof initDocumentModalHandlers === 'function') {
            initDocumentModalHandlers();
        }
    }
    
    initEventHandlers(modalElement) {
        console.log('📅 Инициализация обработчиков событий...');
        if (typeof initEventModalHandlers === 'function') {
            initEventModalHandlers();
        }
    }

    initPhotoHandlers(modalElement) {
        console.log('📸 Инициализация обработчиков фотографий...');
        
        // Находим элементы внутри модального окна
        const uploadZone = modalElement.querySelector('#photoUploadZone');
        const fileInput = modalElement.querySelector('#photoFileInput');
        const uploadBtn = modalElement.querySelector('#uploadPhotoBtn');
        
        if (!uploadZone || !fileInput) {
            console.error('❌ Не найдены необходимые элементы в модальном окне фотографий');
            return;
        }
        
        // Убираем старые обработчики если они есть
        const newUploadZone = uploadZone.cloneNode(true);
        uploadZone.parentNode.replaceChild(newUploadZone, uploadZone);
        
        const newFileInput = fileInput.cloneNode(true);
        fileInput.parentNode.replaceChild(newFileInput, fileInput);
        
        // Массив для хранения выбранных файлов
        let selectedFiles = [];
        
        // Обработчики drag & drop для новых элементов
        newUploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            newUploadZone.classList.add('dragover');
        });
        
        newUploadZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            newUploadZone.classList.remove('dragover');
        });
        
        newUploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            newUploadZone.classList.remove('dragover');
            
            const files = Array.from(e.dataTransfer.files);
            this.handlePhotoFileSelection(files, modalElement);
        });
        
        // Обработчик клика по зоне загрузки
        newUploadZone.addEventListener('click', (e) => {
            // Проверяем, что клик не по кнопке выбора файлов
            if (!e.target.closest('button')) {
                newFileInput.click();
            }
        });
        
        // Обработчик выбора файлов
        newFileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            this.handlePhotoFileSelection(files, modalElement);
        });
        
        // Обработчик кнопки загрузки
        if (uploadBtn) {
            // Убираем старые обработчики и добавляем новый
            const newUploadBtn = uploadBtn.cloneNode(true);
            uploadBtn.parentNode.replaceChild(newUploadBtn, uploadBtn);
            
            newUploadBtn.addEventListener('click', () => {
                this.uploadPhotos(modalElement);
            });
        }
        
        console.log('✅ Обработчики фотографий инициализированы');
    }

    handlePhotoFileSelection(files, modalElement) {
        console.log('📁 Обработка выбора файлов:', files.length);
        
        // Фильтруем только изображения
        const imageFiles = files.filter(file => file.type.startsWith('image/'));
        
        if (imageFiles.length === 0) {
            this.showErrorToast('Выберите файлы изображений');
            return;
        }
        
        if (imageFiles.length !== files.length) {
            this.showWarningToast(`Отфильтровано ${files.length - imageFiles.length} не-изображений`);
        }
        
        // Отображаем выбранные файлы
        this.displaySelectedPhotos(imageFiles, modalElement);
        
        // Активируем кнопку загрузки
        const uploadBtn = modalElement.querySelector('#uploadPhotoBtn');
        if (uploadBtn) {
            uploadBtn.disabled = false;
        }
    }

    displaySelectedPhotos(files, modalElement) {
        const fileList = modalElement.querySelector('#photoFileList');
        const fileItems = modalElement.querySelector('#photoFileItems');
        
        if (!fileList || !fileItems) return;
        
        fileItems.innerHTML = '';
        
        files.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <div class="file-icon">
                    <i class="bi bi-image text-primary"></i>
                </div>
                <div class="file-info">
                    <div class="file-name">${file.name}</div>
                    <div class="file-size">${this.formatFileSize(file.size)}</div>
                </div>
                <div class="file-actions">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.file-item').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            fileItems.appendChild(fileItem);
        });
        
        fileList.style.display = 'block';
    }

    uploadPhotos(modalElement) {
        console.log('📤 Загрузка фотографий (без AJAX)...');
        
        const fileInput = modalElement.querySelector('#photoFileInput');
        const form = modalElement.querySelector('#uploadPhotoForm');
        
        if (!fileInput.files.length) {
            this.showErrorToast('Выберите файлы для загрузки');
            return;
        }
        
        // Простое уведомление вместо AJAX запроса
        this.showSuccessToast('Функция загрузки фотографий временно отключена');
        this.closeActiveModal();
        
        // Обновляем список фотографий если есть функция
        if (typeof window.reloadPhotos === 'function') {
            window.reloadPhotos();
        }
    }
    }

    showPhotoUploadProgress(modalElement) {
        const progress = modalElement.querySelector('#photoUploadProgress');
        if (progress) {
            progress.style.display = 'block';
        }
    }

    updatePhotoProgress(percent, modalElement) {
        const progressBar = modalElement.querySelector('#photoProgressBar');
        const progressText = modalElement.querySelector('#photoProgressText');
        
        if (progressBar) {
            progressBar.style.width = percent + '%';
        }
        if (progressText) {
            progressText.textContent = Math.round(percent) + '%';
        }
    }

    hidePhotoUploadProgress(modalElement) {
        const progress = modalElement.querySelector('#photoUploadProgress');
        if (progress) {
            progress.style.display = 'none';
        }
    }

    closeActiveModal() {
        if (this.activeModal) {
            try {
                this.activeModal.hide();
            } catch (error) {
                console.warn('⚠️ Ошибка при закрытии активного модального окна:', error);
            }
        }
    }

    onModalClosed() {
        console.log('🚪 Модальное окно закрыто');
        this.activeModal = null;
        this.cleanupBackdropsDelayed();
    }

    cleanupBackdrops() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css({
            'overflow': '',
            'padding-right': ''
        });
    }

    cleanupBackdropsDelayed() {
        setTimeout(() => {
            if ($('.modal:visible').length === 0) {
                this.cleanupBackdrops();
            }
        }, 150);
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Байт';
        const k = 1024;
        const sizes = ['Байт', 'КБ', 'МБ', 'ГБ'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    showSuccessToast(message) {
        this.showToast(message, 'success');
    }

    showErrorToast(message) {
        this.showToast(message, 'error');
    }

    showWarningToast(message) {
        this.showToast(message, 'warning');
    }

    showToast(message, type = 'info') {
        const toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) return;
        
        const toastId = 'toast_' + Date.now();
        const toastHtml = `
            <div class="toast" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bi bi-${this.getToastIcon(type)} me-2 text-${this.getToastColor(type)}"></i>
                    <strong class="me-auto">${this.getToastTitle(type)}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toast = new bootstrap.Toast(document.getElementById(toastId), {
            autohide: true,
            delay: 5000
        });
        
        toast.show();
    }

    getToastIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || icons.info;
    }

    getToastColor(type) {
        const colors = {
            success: 'success',
            error: 'danger',
            warning: 'warning',
            info: 'info'
        };
        return colors[type] || colors.info;
    }

    getToastTitle(type) {
        const titles = {
            success: 'Успешно',
            error: 'Ошибка',
            warning: 'Предупреждение',
            info: 'Информация'
        };
        return titles[type] || titles.info;
    }

    // Заглушки для других типов модальных окон
    initStageHandlers(modalElement) {
        console.log('📝 Инициализация обработчиков этапов...');
        // Здесь будет код для инициализации обработчиков этапов
    }

    // Динамические модальные окна (для будущего использования)
    async loadDynamicModal(modalType) {
        console.log(`📦 Загрузка динамического модального окна: ${modalType}`);
        // Реализация загрузки динамических модальных окон
    }
}

// Инициализация менеджера модальных окон
$(document).ready(function() {
    // Проверяем, что у нас есть projectId
    const projectId = window.projectId || $('meta[name="project-id"]').attr('content');
    
    if (!projectId) {
        console.error('❌ Project ID не найден, невозможно инициализировать Modal Manager');
        return;
    }
    
    // Предотвращаем повторную инициализацию
    if (window.modalManagerInstance) {
        console.log('ℹ️ Modal Manager уже инициализирован');
        return;
    }
    
    // Инициализируем исправленный менеджер модальных окон
    window.modalManager = new ProjectModalManagerFixed(projectId);
    
    console.log('✅ Исправленный Modal Manager инициализирован для проекта:', projectId);
});
</script>

<!-- Подключаем хотфикс для исправления масок -->
<script src="{{ asset('js/mask-hotfix.js') }}"></script>
