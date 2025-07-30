/**
 * Система автосохранения для редактора смет
 */
class EstimateAutosave {
    constructor(estimateId, options = {}) {
        this.estimateId = estimateId;
        this.options = {
            interval: 15000, // 15 секунд
            maxRetries: 3,
            retryDelay: 2000, // 2 секунды
            enableDebug: false,
            ...options
        };
        
        this.isActive = false;
        this.hasChanges = false;
        this.lastSavedData = null;
        this.saveInProgress = false;
        this.intervalId = null;
        this.retryCount = 0;
        this.statusElement = null;
        
        this.init();
    }
    
    init() {
        this.createStatusIndicator();
        this.attachEventListeners();
        this.start();
        
        if (this.options.enableDebug) {
            console.log('EstimateAutosave initialized for estimate:', this.estimateId);
        }
    }
    
    createStatusIndicator() {
        // Создаем индикатор статуса автосохранения
        const statusHtml = `
            <div id="autosave-status" class="position-fixed" style="top: 20px; right: 20px; z-index: 1050;">
                <div class="alert alert-info py-1 px-2 mb-0" style="font-size: 0.75rem; display: none;">
                    <i class="bi bi-clock me-1"></i>
                    <span id="autosave-message">Автосохранение...</span>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', statusHtml);
        this.statusElement = document.getElementById('autosave-status');
    }
    
    attachEventListeners() {
        // Отслеживаем изменения в форме
        const form = document.getElementById('estimateForm');
        if (form) {
            form.addEventListener('input', () => this.markAsChanged());
            form.addEventListener('change', () => this.markAsChanged());
        }
        
        // Отслеживаем изменения в динамически создаваемых элементах
        document.addEventListener('input', (e) => {
            if (e.target.closest('#estimate-editor')) {
                this.markAsChanged();
            }
        });
        
        document.addEventListener('change', (e) => {
            if (e.target.closest('#estimate-editor')) {
                this.markAsChanged();
            }
        });
        
        // Сохраняем при потере фокуса страницы
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && this.hasChanges) {
                this.saveNow();
            }
        });
        
        // Сохраняем при закрытии страницы
        window.addEventListener('beforeunload', () => {
            if (this.hasChanges) {
                this.saveNow();
            }
        });
    }
    
    start() {
        if (this.isActive) return;
        
        this.isActive = true;
        this.intervalId = setInterval(() => {
            if (this.hasChanges && !this.saveInProgress) {
                this.saveNow();
            }
        }, this.options.interval);
        
        if (this.options.enableDebug) {
            console.log('Autosave started with interval:', this.options.interval);
        }
    }
    
    stop() {
        if (!this.isActive) return;
        
        this.isActive = false;
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
        
        if (this.options.enableDebug) {
            console.log('Autosave stopped');
        }
    }
    
    markAsChanged() {
        this.hasChanges = true;
        if (this.options.enableDebug) {
            console.log('Form marked as changed');
        }
    }
    
    async saveNow() {
        if (this.saveInProgress) {
            if (this.options.enableDebug) {
                console.log('Save already in progress, skipping');
            }
            return;
        }
        
        this.saveInProgress = true;
        this.showStatus('Сохранение...', 'info');
        
        try {
            const formData = this.collectFormData();
            
            // Проверяем, изменились ли данные с последнего сохранения
            const currentDataString = JSON.stringify(formData);
            if (this.lastSavedData === currentDataString) {
                if (this.options.enableDebug) {
                    console.log('No changes detected, skipping save');
                }
                this.saveInProgress = false;
                this.hideStatus();
                return;
            }
            
            const response = await this.sendSaveRequest(formData);
            
            if (response.success) {
                this.hasChanges = false;
                this.lastSavedData = currentDataString;
                this.retryCount = 0;
                this.showStatus(`Сохранено ${response.timestamp}`, 'success');
                
                if (this.options.enableDebug) {
                    console.log('Autosave successful:', response);
                }
            } else {
                throw new Error(response.message || 'Ошибка сохранения');
            }
            
        } catch (error) {
            console.error('Autosave error:', error);
            
            if (this.retryCount < this.options.maxRetries) {
                this.retryCount++;
                this.showStatus(`Ошибка сохранения. Повтор ${this.retryCount}/${this.options.maxRetries}`, 'warning');
                
                setTimeout(() => {
                    this.saveInProgress = false;
                    this.saveNow();
                }, this.options.retryDelay);
                
                return;
            } else {
                this.showStatus('Ошибка автосохранения', 'danger');
                this.retryCount = 0;
            }
        }
        
        this.saveInProgress = false;
        
        // Скрываем статус через 3 секунды
        setTimeout(() => {
            this.hideStatus();
        }, 3000);
    }
    
    collectFormData() {
        const formData = {};
        
        // Добавляем обязательные поля для валидации
        formData.type = 'main'; // Тип по умолчанию
        formData.version = '1.0';
        formData.meta = {
            template_name: 'Основная смета работ',
            is_template: false,
            created_at: new Date().toISOString(),
            description: 'Смета строительных и отделочных работ'
        };
        
        // Добавляем структуру колонок
        formData.structure = {
            columns: [
                { "title": "№", "width": 50, "type": "numeric", "readonly": true },
                { "title": "Наименование работ/материалов", "width": 300, "type": "text" },
                { "title": "Ед.изм.", "width": 80, "type": "text" },
                { "title": "Кол-во", "width": 80, "type": "numeric" },
                { "title": "Цена", "width": 100, "type": "currency" },
                { "title": "Стоимость", "width": 120, "type": "currency", "formula": "quantity*price", "readonly": true },
                { "title": "Наценка %", "width": 80, "type": "numeric", "default": 20 },
                { "title": "Скидка %", "width": 80, "type": "numeric", "default": 0 },
                { "title": "Цена клиента", "width": 120, "type": "currency", "formula": "price*(1+markup/100)*(1-discount/100)", "readonly": true },
                { "title": "Сумма клиента", "width": 120, "type": "currency", "formula": "quantity*client_price", "readonly": true }
            ],
            settings: {
                readonly_columns: [0, 5, 8, 9],
                formula_columns: [5, 8, 9],
                numeric_columns: [0, 3, 4, 5, 6, 7, 8, 9],
                currency_columns: [4, 5, 8, 9]
            }
        };
        
        // Собираем данные секций
        formData.sections = {};
        
        document.querySelectorAll('.section').forEach(section => {
            const sectionId = section.getAttribute('data-section-id');
            if (!sectionId) return;
            
            const sectionData = {
                id: sectionId,
                title: section.querySelector('.section-title')?.value || section.querySelector('.section-title')?.textContent || '',
                type: 'section',
                items: {}
            };
            
            // Собираем строки секции
            section.querySelectorAll('.estimate-row').forEach(row => {
                const rowId = row.getAttribute('data-row-id');
                if (!rowId) return;
                
                const rowData = {
                    name: row.querySelector(`input[name*="[name]"]`)?.value || '',
                    unit: row.querySelector(`select[name*="[unit]"]`)?.value || 'шт',
                    quantity: parseFloat(row.querySelector(`input[name*="[quantity]"]`)?.value) || 0,
                    price: parseFloat(row.querySelector(`input[name*="[price]"]`)?.value) || 0,
                    markup: parseFloat(row.querySelector(`input[name*="[markup]"]`)?.value) || 20,
                    discount: parseFloat(row.querySelector(`input[name*="[discount]"]`)?.value) || 0,
                    amount: parseFloat(row.querySelector(`input[name*="[amount]"]`)?.value) || 0,
                    client_price: parseFloat(row.querySelector(`input[name*="[client_price]"]`)?.value) || 0,
                    client_amount: parseFloat(row.querySelector(`input[name*="[client_amount]"]`)?.value) || 0
                };
                
                sectionData.items[rowId] = rowData;
            });
            
            formData.sections[sectionId] = sectionData;
        });
        
        // Собираем итоги
        formData.totals = {
            work_total: parseFloat(document.getElementById('work_total')?.value) || 0,
            materials_total: parseFloat(document.getElementById('materials_total')?.value) || 0,
            grand_total: parseFloat(document.getElementById('grand_total')?.value) || 0,
            markup_percent: parseFloat(document.getElementById('markup_percent')?.value) || 20,
            discount_percent: parseFloat(document.getElementById('discount_percent')?.value) || 0
        };
        
        // Добавляем footer
        formData.footer = {
            items: []
        };
        
        return formData;
    }
    
    async sendSaveRequest(data) {
        const url = `/partner/estimates/${this.estimateId}/autosave`;
        
        // Получаем CSRF токен
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            console.error('CSRF токен не найден');
            throw new Error('CSRF токен не найден');
        }
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ data })
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('HTTP Error:', response.status, errorText);
            throw new Error(`HTTP error! status: ${response.status}, response: ${errorText}`);
        }
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Неизвестная ошибка сервера');
        }
        
        return result;
    }
    
    showStatus(message, type = 'info') {
        if (!this.statusElement) return;
        
        const alertDiv = this.statusElement.querySelector('.alert');
        const messageSpan = this.statusElement.querySelector('#autosave-message');
        
        // Удаляем предыдущие классы
        alertDiv.className = 'alert py-1 px-2 mb-0';
        
        // Добавляем новый класс
        alertDiv.classList.add(`alert-${type}`);
        
        // Обновляем иконку в зависимости от типа
        let icon = 'bi-clock';
        switch (type) {
            case 'success':
                icon = 'bi-check-circle';
                break;
            case 'warning':
                icon = 'bi-exclamation-triangle';
                break;
            case 'danger':
                icon = 'bi-x-circle';
                break;
        }
        
        messageSpan.innerHTML = `<i class="bi ${icon} me-1"></i>${message}`;
        
        // Показываем статус
        alertDiv.style.display = 'block';
    }
    
    hideStatus() {
        if (this.statusElement) {
            const alertDiv = this.statusElement.querySelector('.alert');
            alertDiv.style.display = 'none';
        }
    }
    
    // Публичные методы для управления автосохранением
    pause() {
        this.stop();
    }
    
    resume() {
        this.start();
    }
    
    forceSave() {
        this.markAsChanged();
        this.saveNow();
    }
    
    destroy() {
        this.stop();
        
        // Удаляем индикатор статуса
        if (this.statusElement) {
            this.statusElement.remove();
        }
        
        if (this.options.enableDebug) {
            console.log('EstimateAutosave destroyed');
        }
    }
}

// Экспортируем класс для использования в других файлах
window.EstimateAutosave = EstimateAutosave;
