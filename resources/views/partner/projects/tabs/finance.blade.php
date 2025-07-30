<!-- Финансовая информация проекта -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Финансовая сводка (загружается через AJAX) -->
<div class="finance-summary mb-4" id="financeSummary">
    <div class="d-flex justify-content-center align-items-center py-5">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка финансовых данных...</span>
            </div>
            <p class="mt-2 text-muted">Загрузка финансовых данных...</p>
        </div>
    </div>
</div>

<!-- Вкладки финансовых данных -->
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="finance-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="works-tab" data-bs-toggle="tab" data-bs-target="#works-pane" type="button" role="tab" aria-controls="works-pane" aria-selected="true">
                    <i class="bi bi-tools me-1"></i>Планирование работ
                    <span class="badge bg-primary ms-2" id="worksCount">0</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials-pane" type="button" role="tab" aria-controls="materials-pane" aria-selected="false">
                    <i class="bi bi-box-seam me-1"></i>Планирование материалов
                    <span class="badge bg-success ms-2" id="materialsCount">0</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="transport-tab" data-bs-toggle="tab" data-bs-target="#transport-pane" type="button" role="tab" aria-controls="transport-pane" aria-selected="false">
                    <i class="bi bi-truck me-1"></i>Планирование транспорта
                    <span class="badge bg-warning ms-2" id="transportCount">0</span>
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="finance-tabs-content">
            
            <!-- Вкладка Работы -->
            <div class="tab-pane fade show active" id="works-pane" role="tabpanel" aria-labelledby="works-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0">Планирование работ</h6>
                        <small class="text-muted">Детализация работ для планирования (не влияет на финансовые показатели)</small>
                    </div>
                    @if(\App\Helpers\UserRoleHelper::canManageProjects())
                    <button class="btn btn-primary btn-sm" type="button" id="addWorkBtn">
                        <i class="bi bi-plus-circle me-1"></i>Добавить работу
                    </button>
                    @endif
                </div>
                <div id="worksContainer">
                    <div class="d-flex justify-content-center align-items-center py-4">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Загрузка работ...</span>
                            </div>
                            <p class="mt-2 text-muted">Загрузка работ...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Вкладка Материалы -->
            <div class="tab-pane fade" id="materials-pane" role="tabpanel" aria-labelledby="materials-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0">Планирование материалов</h6>
                        <small class="text-muted">Детализация материалов для планирования (не влияет на финансовые показатели)</small>
                    </div>
                    @if(\App\Helpers\UserRoleHelper::canManageProjects())
                    <button class="btn btn-success btn-sm" type="button" id="addMaterialBtn">
                        <i class="bi bi-plus-circle me-1"></i>Добавить материал
                    </button>
                    @endif
                </div>
                <div id="materialsContainer">
                    <div class="d-flex justify-content-center align-items-center py-4">
                        <div class="text-center">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Загрузка материалов...</span>
                            </div>
                            <p class="mt-2 text-muted">Загрузка материалов...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Вкладка Транспорт -->
            <div class="tab-pane fade" id="transport-pane" role="tabpanel" aria-labelledby="transport-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0">Планирование транспорта</h6>
                        <small class="text-muted">Детализация транспорта для планирования (не влияет на финансовые показатели)</small>
                    </div>
                    @if(\App\Helpers\UserRoleHelper::canManageProjects())
                    <button class="btn btn-warning btn-sm" type="button" id="addTransportBtn">
                        <i class="bi bi-plus-circle me-1"></i>Добавить транспорт
                    </button>
                    @endif
                </div>
                <div id="transportContainer">
                    <div class="d-flex justify-content-center align-items-center py-4">
                        <div class="text-center">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Загрузка транспорта...</span>
                            </div>
                            <p class="mt-2 text-мuted">Загрузка транспорта...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
// Глобальные переменные для финансовой вкладки
window.projectId = {{ $project->id ?? 'null' }};
window.financeInitialized = false;

console.log('=== ФИНАНСОВАЯ ВКЛАДКА ЗАГРУЖЕНА ===');
console.log('Project ID:', window.projectId);

// Проверяем наличие функции showMessage, если нет - создаем заглушку
if (typeof window.showMessage !== 'function') {
    window.showMessage = function(message, type) {
        console.log(`Message (${type}): ${message}`);
        // Простое уведомление через alert как резервный вариант
        if (type === 'danger' || type === 'error') {
            console.error(message);
        }
    };
}

// Основная функция инициализации финансовой вкладки
function initFinanceTabContent() {
    if (window.financeInitialized) {
        console.log('Финансовая вкладка уже инициализирована');
        return;
    }

    console.log('Инициализация содержимого финансовой вкладки...');
    
    // Проверяем готовность modalManager
    if (!window.modalManager || !window.modalManager.loadModal) {
        console.warn('modalManager не готов, попробуем через 500ms...');
        setTimeout(function() {
            if (window.modalManager && window.modalManager.loadModal) {
                console.log('modalManager готов, продолжаем инициализацию');
                initFinanceTabContentInternal();
            } else {
                console.error('modalManager так и не был инициализирован');
                initFinanceTabContentInternal(); // Пробуем всё равно
            }
        }, 500);
        return;
    }
    
    initFinanceTabContentInternal();
}

// Внутренняя функция инициализации
function initFinanceTabContentInternal() {
    // Инициализация обработчиков событий
    initFinanceHandlers();
    
    // Загружаем данные
    loadFinanceData();
    
    // Помечаем как инициализированную
    window.financeInitialized = true;
    
    console.log('Финансовая вкладка успешно инициализирована');
}

// Инициализация обработчиков событий
function initFinanceHandlers() {
    console.log('Инициализация обработчиков событий...');
    
    // Очищаем старые обработчики
    $(document).off('click.finance');
    $(document).off('submit.finance');
    
    // Обработчики кнопок
    $(document).on('click.finance', '#addWorkBtn, #addFirstWorkBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Клик по кнопке добавления работы');
        openAddWorkModal();
    });
    
    $(document).on('click.finance', '#addMaterialBtn, #addFirstMaterialBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Клик по кнопке добавления материала');
        openAddMaterialModal();
    });
    
    $(document).on('click.finance', '#addTransportBtn, #addFirstTransportBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Клик по кнопке добавления транспорта');
        openAddTransportModal();
    });
    
    // Обработчики форм
    $(document).on('submit.finance', '#addWorkForm', function(e) {
        e.preventDefault();
        console.log('Отправка формы работы');
        handleWorkForm(this);
    });
    
    $(document).on('submit.finance', '#addMaterialForm', function(e) {
        e.preventDefault();
        console.log('Отправка формы материала');
        handleMaterialForm(this);
    });
    
    $(document).on('submit.finance', '#addTransportForm', function(e) {
        e.preventDefault();
        console.log('Отправка формы транспорта');
        handleTransportForm(this);
    });
    
    // Обработчики редактирования и удаления
    $(document).on('click.finance', '.edit-work', function(e) {
        e.preventDefault();
        const workId = $(this).data('work-id');
        editWork(workId);
    });
    
    $(document).on('click.finance', '.delete-work', function(e) {
        e.preventDefault();
        const workId = $(this).data('work-id');
        deleteWork(workId);
    });
    
    $(document).on('click.finance', '.edit-material', function(e) {
        e.preventDefault();
        const materialId = $(this).data('material-id');
        editMaterial(materialId);
    });
    
    $(document).on('click.finance', '.delete-material', function(e) {
        e.preventDefault();
        const materialId = $(this).data('material-id');
        deleteMaterial(materialId);
    });
    
    $(document).on('click.finance', '.edit-transport', function(e) {
        e.preventDefault();
        const transportId = $(this).data('transport-id');
        editTransport(transportId);
    });
    
    $(document).on('click.finance', '.delete-transport', function(e) {
        e.preventDefault();
        const transportId = $(this).data('transport-id');
        deleteTransport(transportId);
    });
    
    console.log('Обработчики событий инициализированы');
}

// Загрузка финансовых данных
function loadFinanceData() {
    if (!window.projectId) {
        console.error('Project ID не определен');
        showMessage('Ошибка: ID проекта не найден', 'danger');
        return;
    }
    
    console.log('Загрузка финансовых данных для проекта:', window.projectId);
    
    // Счетчик завершенных запросов
    let completedRequests = 0;
    const totalRequests = 4; // estimates-summary, works, materials, transports
    
    function checkAllRequestsCompleted() {
        completedRequests++;
        console.log('Запрос завершен:', completedRequests, '/', totalRequests);
    }
    
    // Загружаем сводку финансов из смет (основной источник)
    $.ajax({
        url: `/partner/projects/${window.projectId}/finance/summary`,
        method: 'GET',
        success: function(response) {
            console.log('Сводка финансов из смет загружена:', response);
            if (response.success) {
                renderFinanceSummary(response.summary);
            }
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при загрузке сводки финансов из смет:', error);
            // Показываем заглушку, если нет смет
            renderFinanceSummary({
                estimates: {
                    total: 0,
                    paid: 0,
                    remaining: 0,
                    count: 0,
                    payment_progress: 0
                }
            });
        },
        complete: function() {
            checkAllRequestsCompleted();
        }
    });
    
    // Загружаем данные работ (для планирования и детализации)
    loadWorksWithCallback(checkAllRequestsCompleted);
    
    // Загружаем данные материалов
    loadMaterialsWithCallback(checkAllRequestsCompleted);
    
    // Загружаем данные транспорта
    loadTransportsWithCallback(checkAllRequestsCompleted);
    
    // Загружаем счетчики
    loadFinanceCounts();
}

// Загрузка работ
function loadWorks() {
    loadWorksWithCallback(null);
}

function loadWorksWithCallback(callback) {
    $.ajax({
        url: `/partner/projects/${window.projectId}/finance/works-partial`,
        method: 'GET',
        success: function(response) {
            console.log('Работы загружены:', response);
            renderWorks(response.works || []);
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при загрузке работ:', error);
            showMessage('Ошибка при загрузке работ', 'danger');
            $('#worksContainer').html('<div class="alert alert-danger">Ошибка при загрузке работ</div>');
        },
        complete: function() {
            if (callback && typeof callback === 'function') {
                callback();
            }
        }
    });
}

// Загрузка материалов
function loadMaterials() {
    loadMaterialsWithCallback(null);
}

function loadMaterialsWithCallback(callback) {
    $.ajax({
        url: `/partner/projects/${window.projectId}/finance/materials-partial`,
        method: 'GET',
        success: function(response) {
            console.log('Материалы загружены:', response);
            renderMaterials(response.materials || []);
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при загрузке материалов:', error);
            showMessage('Ошибка при загрузке материалов', 'danger');
            $('#materialsContainer').html('<div class="alert alert-danger">Ошибка при загрузке материалов</div>');
        },
        complete: function() {
            if (callback && typeof callback === 'function') {
                callback();
            }
        }
    });
}

// Загрузка транспорта
function loadTransports() {
    loadTransportsWithCallback(null);
}

function loadTransportsWithCallback(callback) {
    $.ajax({
        url: `/partner/projects/${window.projectId}/finance/transports-partial`,
        method: 'GET',
        success: function(response) {
            console.log('Транспорт загружен:', response);
            renderTransports(response.transports || []);
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при загрузке транспорта:', error);
            showMessage('Ошибка при загрузке транспорта', 'danger');
            $('#transportContainer').html('<div class="alert alert-danger">Ошибка при загрузке транспорта</div>');
        },
        complete: function() {
            if (callback && typeof callback === 'function') {
                callback();
            }
        }
    });
}

// Загрузка счетчиков
function loadFinanceCounts() {
    $.ajax({
        url: `/partner/projects/${window.projectId}/finance/counts`,
        method: 'GET',
        success: function(response) {
            console.log('Счетчики загружены:', response);
            if (response.success) {
                updateFinanceCounts(response.counts);
            }
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при загрузке счетчиков:', error);
        }
    });
}

// Отображение сводки финансов (данные из смет)
function renderFinanceSummary(summary) {
    const financial = summary.financial || {};
    const planning = summary.planning || {};
    
    const html = `
        <div class="row">
            <div class="col-md-4">
                <div class="finance-metric">
                    <h3 id="estimatesTotal">${formatMoney(financial.total?.amount || 0)} ₽</h3>
                    <p class="mb-1">Общая стоимость по сметам</p>
                    <small class="text-muted">${financial.total?.estimates_count || 0} смет</small>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="finance-metric">
                    <h3 id="mainWorksTotal">${formatMoney(financial.main_works?.total || 0)} ₽</h3>
                    <p class="mb-1">Основные работы</p>
                    <small class="text-muted">${financial.main_works?.estimates_count || 0} смет</small>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: ${financial.total?.amount > 0 ? (financial.main_works?.total / financial.total?.amount * 100) : 0}%"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="finance-metric">
                    <h3 id="materialsTotal">${formatMoney(financial.materials?.total || 0)} ₽</h3>
                    <p class="mb-1">Материалы</p>
                    <small class="text-muted">${financial.materials?.estimates_count || 0} смет</small>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-info" style="width: ${financial.total?.amount > 0 ? (financial.materials?.total / financial.total?.amount * 100) : 0}%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="finance-metric">
                    <h3 id="additionalWorksTotal">${formatMoney(financial.additional_works?.total || 0)} ₽</h3>
                    <p class="mb-1">Дополнительные работы</p>
                    <small class="text-muted">${financial.additional_works?.estimates_count || 0} смет</small>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: ${financial.total?.amount > 0 ? (financial.additional_works?.total / financial.total?.amount * 100) : 0}%"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Информация:</strong> Финансовые показатели формируются на основе смет проекта. 
                    Данные работ (${planning.works?.count || 0}), материалов (${planning.materials?.count || 0}) и транспорта (${planning.transport?.count || 0}) служат для детализации и планирования.
                </div>
            </div>
        </div>
    `;
    
    $('#financeSummary').html(html);
}

// Отображение работ
function renderWorks(works) {
    if (works.length === 0) {
        // Проверяем роль пользователя для показа кнопки добавления
        const canManage = @json(\App\Helpers\UserRoleHelper::canManageProjects());
        const addButton = canManage ? `
            <button class="btn btn-primary" type="button" id="addFirstWorkBtn">
                Добавить первую работу
            </button>
        ` : '';
        
        $('#worksContainer').html(`
            <div class="text-center py-4">
                <i class="bi bi-tools" style="font-size: 3rem; color: #dee2e6;"></i>
                <p class="mt-3 mb-3">Работы не добавлены</p>
                ${addButton}
            </div>
        `);
        return;
    }
    
    let html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Тип</th>
                        <th>Количество</th>
                        <th>Цена</th>
                        <th>Сумма</th>
                        <th>Оплачено</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    works.forEach(work => {
        const paymentStatus = getPaymentStatus(work.amount, work.paid_amount);
        
        // Проверяем роль пользователя для показа кнопок действий
        const canManage = @json(\App\Helpers\UserRoleHelper::canSeeActionButtons());
        const actionButtons = canManage ? `
            <button class="btn btn-sm btn-outline-primary edit-work" data-work-id="${work.id}" title="Редактировать">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger delete-work" data-work-id="${work.id}" title="Удалить">
                <i class="bi bi-trash"></i>
            </button>
        ` : '';
        
        html += `
            <tr data-work-id="${work.id}">
                <td>${work.name}</td>
                <td>
                    <span class="badge ${work.type === 'basic' ? 'bg-primary' : 'bg-secondary'}">
                        ${work.type === 'basic' ? 'Основные' : 'Дополнительные'}
                    </span>
                </td>
                <td>${work.quantity} ${work.unit || ''}</td>
                <td>${formatMoney(work.price)} ₽</td>
                <td>${formatMoney(work.amount)} ₽</td>
                <td>
                    <span class="badge ${paymentStatus.class}">
                        ${formatMoney(work.paid_amount)} ₽
                    </span>
                </td>
                <td>
                    ${actionButtons}
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    $('#worksContainer').html(html);
}

// Отображение материалов
function renderMaterials(materials) {
    if (materials.length === 0) {
        // Проверяем роль пользователя для показа кнопки добавления
        const canManage = @json(\App\Helpers\UserRoleHelper::canManageProjects());
        const addButton = canManage ? `
            <button class="btn btn-success" type="button" id="addFirstMaterialBtn">
                Добавить первый материал
            </button>
        ` : '';
        
        $('#materialsContainer').html(`
            <div class="text-center py-4">
                <i class="bi bi-box-seam" style="font-size: 3rem; color: #dee2e6;"></i>
                <p class="mt-3 mb-3">Материалы не добавлены</p>
                ${addButton}
            </div>
        `);
        return;
    }
    
    let html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Тип</th>
                        <th>Количество</th>
                        <th>Цена</th>
                        <th>Сумма</th>
                        <th>Оплачено</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    materials.forEach(material => {
        const paymentStatus = getPaymentStatus(material.amount, material.paid_amount || 0);
        
        // Проверяем роль пользователя для показа кнопок действий
        const canManage = @json(\App\Helpers\UserRoleHelper::canSeeActionButtons());
        const actionButtons = canManage ? `
            <button class="btn btn-sm btn-outline-primary edit-material" data-material-id="${material.id}" title="Редактировать">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger delete-material" data-material-id="${material.id}" title="Удалить">
                <i class="bi bi-trash"></i>
            </button>
        ` : '';
        
        html += `
            <tr data-material-id="${material.id}">
                <td>${material.name}</td>
                <td>
                    <span class="badge ${(material.type === 'basic' || !material.type) ? 'bg-primary' : 'bg-secondary'}">
                        ${(material.type === 'basic' || !material.type) ? 'Основные' : 'Дополнительные'}
                    </span>
                </td>
                <td>${material.quantity} ${material.unit || ''}</td>
                <td>${formatMoney(material.unit_price || material.price)} ₽</td>
                <td>${formatMoney(material.amount)} ₽</td>
                <td>
                    <span class="badge ${paymentStatus.class}">
                        ${formatMoney(material.paid_amount || 0)} ₽
                    </span>
                </td>
                <td>
                    ${actionButtons}
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    $('#materialsContainer').html(html);
}

// Отображение транспорта
function renderTransports(transports) {
    if (transports.length === 0) {
        // Проверяем роль пользователя для показа кнопки добавления
        const canManage = @json(\App\Helpers\UserRoleHelper::canManageProjects());
        const addButton = canManage ? `
            <button class="btn btn-warning" type="button" id="addFirstTransportBtn">
                Добавить первый транспорт
            </button>
        ` : '';
        
        $('#transportContainer').html(`
            <div class="text-center py-4">
                <i class="bi bi-truck" style="font-size: 3rem; color: #dee2e6;"></i>
                <p class="mt-3 mb-3">Транспорт не добавлен</p>
                ${addButton}
            </div>
        `);
        return;
    }
    
    let html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Количество</th>
                        <th>Цена</th>
                        <th>Сумма</th>
                        <th>Оплачено</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    transports.forEach(transport => {
        const paymentStatus = getPaymentStatus(transport.amount, transport.paid_amount);
        
        // Проверяем роль пользователя для показа кнопок действий
        const canManage = @json(\App\Helpers\UserRoleHelper::canSeeActionButtons());
        const actionButtons = canManage ? `
            <button class="btn btn-sm btn-outline-primary edit-transport" data-transport-id="${transport.id}" title="Редактировать">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger delete-transport" data-transport-id="${transport.id}" title="Удалить">
                <i class="bi bi-trash"></i>
            </button>
        ` : '';
        
        html += `
            <tr data-transport-id="${transport.id}">
                <td>${transport.name}</td>
                <td>${transport.quantity} ${transport.unit || ''}</td>
                <td>${formatMoney(transport.price)} ₽</td>
                <td>${formatMoney(transport.amount)} ₽</td>
                <td>
                    <span class="badge ${paymentStatus.class}">
                        ${formatMoney(transport.paid_amount)} ₽
                    </span>
                </td>
                <td>
                    ${actionButtons}
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    $('#transportContainer').html(html);
}

// Обновление счетчиков
function updateFinanceCounts(counts) {
    $('#worksCount').text(counts.works || 0);
    $('#materialsCount').text(counts.materials || 0);
    $('#transportCount').text(counts.transports || 0);
}

// Функции для работы с модальными окнами
function openAddWorkModal() {
    console.log('Открытие модального окна добавления работы');
    console.log('window.modalManager существует:', !!window.modalManager);
    console.log('window.modalManager.loadModal существует:', !!(window.modalManager && window.modalManager.loadModal));
    
    if (window.modalManager && typeof window.modalManager.loadModal === 'function') {
        window.modalManager.loadModal('work-add');
    } else {
        console.warn('modalManager не инициализирован, используем прямое открытие модального окна');
        openWorkModalDirect();
    }
}

function openAddMaterialModal() {
    console.log('Открытие модального окна добавления материала');
    console.log('window.modalManager существует:', !!window.modalManager);
    console.log('window.modalManager.loadModal существует:', !!(window.modalManager && window.modalManager.loadModal));
    
    if (window.modalManager && typeof window.modalManager.loadModal === 'function') {
        window.modalManager.loadModal('material-add');
    } else {
        console.warn('modalManager не инициализирован, используем прямое открытие модального окна');
        openMaterialModalDirect();
    }
}

function openAddTransportModal() {
    console.log('Открытие модального окна добавления транспорта');
    console.log('window.modalManager существует:', !!window.modalManager);
    console.log('window.modalManager.loadModal существует:', !!(window.modalManager && window.modalManager.loadModal));
    
    if (window.modalManager && typeof window.modalManager.loadModal === 'function') {
        window.modalManager.loadModal('transport-add');
    } else {
        console.warn('modalManager не инициализирован, используем прямое открытие модального окна');
        openTransportModalDirect();
    }
}

// Прямые функции открытия модальных окон (резерв на случай отсутствия modalManager)
function openWorkModalDirect() {
    if (!window.projectId) {
        showMessage('Project ID не найден', 'error');
        return;
    }
    
    $.get(`/partner/projects/${window.projectId}/modals/work-add`)
        .done(function(data) {
            if (data.html) {
                $('#modalContainer').html(data.html);
                const modal = new bootstrap.Modal(document.getElementById('addWorkModal'));
                modal.show();
            }
        })
        .fail(function() {
            showMessage('Ошибка загрузки модального окна', 'error');
        });
}

function openMaterialModalDirect() {
    if (!window.projectId) {
        showMessage('Project ID не найден', 'error');
        return;
    }
    
    $.get(`/partner/projects/${window.projectId}/modals/material-add`)
        .done(function(data) {
            if (data.html) {
                $('#modalContainer').html(data.html);
                const modal = new bootstrap.Modal(document.getElementById('addMaterialModal'));
                modal.show();
            }
        })
        .fail(function() {
            showMessage('Ошибка загрузки модального окна', 'error');
        });
}

function openTransportModalDirect() {
    if (!window.projectId) {
        showMessage('Project ID не найден', 'error');
        return;
    }
    
    $.get(`/partner/projects/${window.projectId}/modals/transport-add`)
        .done(function(data) {
            if (data.html) {
                $('#modalContainer').html(data.html);
                const modal = new bootstrap.Modal(document.getElementById('addTransportModal'));
                modal.show();
            }
        })
        .fail(function() {
            showMessage('Ошибка загрузки модального окна', 'error');
        });
}

// Функции для открытия модальных окон редактирования
function openEditWorkModal(work) {
    console.log('Открытие модального окна редактирования работы', work);
    
    if (window.modalManager && typeof window.modalManager.loadModal === 'function') {
        // Сохраняем данные работы для последующего заполнения формы
        window.editWorkData = work;
        window.modalManager.loadModal('work-edit', { work: work });
    } else {
        console.error('modalManager не инициализирован или loadModal не найден');
        showMessage('Система модальных окон не готова', 'error');
    }
}

function openEditMaterialModal(material) {
    console.log('Открытие модального окна редактирования материала', material);
    
    if (window.modalManager && typeof window.modalManager.loadModal === 'function') {
        // Сохраняем данные материала для последующего заполнения формы
        window.editMaterialData = material;
        window.modalManager.loadModal('material-edit', { material: material });
    } else {
        console.error('modalManager не инициализирован или loadModal не найден');
        showMessage('Система модальных окон не готова', 'error');
    }
}

function openEditTransportModal(transport) {
    console.log('Открытие модального окна редактирования транспорта', transport);
    
    if (window.modalManager && typeof window.modalManager.loadModal === 'function') {
        // Сохраняем данные транспорта для последующего заполнения формы
        window.editTransportData = transport;
        window.modalManager.loadModal('transport-edit', { transport: transport });
    } else {
        console.error('modalManager не инициализирован или loadModal не найден');
        showMessage('Система модальных окон не готова', 'error');
    }
}

// Обработчики форм
function handleWorkForm(form) {
    console.log('Обработка формы работы');
    
    const formData = new FormData(form);
    const url = form.action || `/partner/projects/${window.projectId}/works`;
    
    // Добавляем CSRF токен
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    }
    
    console.log('Отправка работы по URL:', url);
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Определяем, добавление это или редактирование
            const isEdit = form.action.includes('works/') && form.querySelector('input[name="_method"]');
            const message = isEdit ? 'Работа успешно обновлена' : 'Работа успешно добавлена';
            
            showMessage(message, 'success');
            
            // Закрываем модальное окно через modalManager
            if (window.modalManager && window.modalManager.activeModal) {
                window.modalManager.activeModal.hide();
            }
            
            // Перезагружаем данные
            loadWorks();
            loadFinanceData();
        } else {
            const isEdit = form.action.includes('works/') && form.querySelector('input[name="_method"]');
            const errorMessage = isEdit ? 'Ошибка при обновлении работы' : 'Ошибка при добавлении работы';
            showMessage(data.message || errorMessage, 'error');
        }
    })
    .catch(error => {
        console.error('Ошибка AJAX:', error);
        showMessage('Ошибка при отправке данных: ' + error.message, 'error');
    });
}

function handleMaterialForm(form) {
    console.log('Обработка формы материала');
    
    const formData = new FormData(form);
    const url = form.action || `/partner/projects/${window.projectId}/materials`;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    }
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Определяем, добавление это или редактирование
            const isEdit = form.action.includes('materials/') && form.querySelector('input[name="_method"]');
            const message = isEdit ? 'Материал успешно обновлен' : 'Материал успешно добавлен';
            
            showMessage(message, 'success');
            
            // Закрываем модальное окно через modalManager
            if (window.modalManager && window.modalManager.activeModal) {
                window.modalManager.activeModal.hide();
            }
            
            // Перезагружаем данные
            loadMaterials();
            loadFinanceData();
        } else {
            const isEdit = form.action.includes('materials/') && form.querySelector('input[name="_method"]');
            const errorMessage = isEdit ? 'Ошибка при обновлении материала' : 'Ошибка при добавлении материала';
            showMessage(data.message || errorMessage, 'error');
        }
    })
    .catch(error => {
        console.error('Ошибка AJAX:', error);
        showMessage('Ошибка при отправке данных: ' + error.message, 'error');
    });
}

function handleTransportForm(form) {
    console.log('Обработка формы транспорта');
    
    const formData = new FormData(form);
    const url = form.action || `/partner/projects/${window.projectId}/transports`;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    }
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Определяем, добавление это или редактирование
            const isEdit = form.action.includes('transports/') && form.querySelector('input[name="_method"]');
            const message = isEdit ? 'Транспорт успешно обновлен' : 'Транспорт успешно добавлен';
            
            showMessage(message, 'success');
            
            // Закрываем модальное окно через modalManager
            if (window.modalManager && window.modalManager.activeModal) {
                window.modalManager.activeModal.hide();
            }
            
            // Перезагружаем данные
            loadTransports();
            loadFinanceData();
        } else {
            const isEdit = form.action.includes('transports/') && form.querySelector('input[name="_method"]');
            const errorMessage = isEdit ? 'Ошибка при обновлении транспорта' : 'Ошибка при добавлении транспорта';
            showMessage(data.message || errorMessage, 'error');
        }
    })
    .catch(error => {
        console.error('Ошибка AJAX:', error);
        showMessage('Ошибка при отправке данных: ' + error.message, 'error');
    });
}

// Функции редактирования
function editWork(workId) {
    console.log('Редактирование работы ID:', workId);
    
    // Получаем данные работы с сервера
    $.ajax({
        url: `/partner/projects/${window.projectId}/works/${workId}`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success && response.work) {
                openEditWorkModal(response.work);
            } else {
                showMessage('Ошибка при загрузке данных работы', 'error');
            }
        },
        error: function(xhr) {
            console.error('Ошибка при загрузке работы:', xhr);
            showMessage('Ошибка при загрузке данных работы', 'error');
        }
    });
}

function editMaterial(materialId) {
    console.log('Редактирование материала ID:', materialId);
    
    // Получаем данные материала с сервера
    $.ajax({
        url: `/partner/projects/${window.projectId}/materials/${materialId}`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success && response.material) {
                openEditMaterialModal(response.material);
            } else {
                showMessage('Ошибка при загрузке данных материала', 'error');
            }
        },
        error: function(xhr) {
            console.error('Ошибка при загрузке материала:', xhr);
            showMessage('Ошибка при загрузке данных материала', 'error');
        }
    });
}

function editTransport(transportId) {
    console.log('Редактирование транспорта ID:', transportId);
    
    // Получаем данные транспорта с сервера
    $.ajax({
        url: `/partner/projects/${window.projectId}/transports/${transportId}`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success && response.transport) {
                openEditTransportModal(response.transport);
            } else {
                showMessage('Ошибка при загрузке данных транспорта', 'error');
            }
        },
        error: function(xhr) {
            console.error('Ошибка при загрузке транспорта:', xhr);
            showMessage('Ошибка при загрузке данных транспорта', 'error');
        }
    });
}


function deleteWork(workId) {
    console.log('Удаление работы ID:', workId);
    $.ajax({
        url: `/partner/projects/${window.projectId}/works/${workId}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showMessage('Работа успешно удалена', 'success');
                loadWorks();
                loadFinanceData();
            } else {
                showMessage(response.message || 'Ошибка при удалении работы', 'error');
            }
        },
        error: function(xhr) {
            console.error('Ошибка при удалении работы:', xhr);
            showMessage('Ошибка при удалении работы', 'error');
        }
    });
}

function deleteMaterial(materialId) {
    console.log('Удаление материала ID:', materialId);
    $.ajax({
        url: `/partner/projects/${window.projectId}/materials/${materialId}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showMessage('Материал успешно удален', 'success');
                loadMaterials();
                loadFinanceData();
            } else {
                showMessage(response.message || 'Ошибка при удалении материала', 'error');
            }
        },
        error: function(xhr) {
            console.error('Ошибка при удалении материала:', xhr);
            showMessage('Ошибка при удалении материала', 'error');
        }
    });
}

function deleteTransport(transportId) {
    console.log('Удаление транспорта ID:', transportId);
    $.ajax({
        url: `/partner/projects/${window.projectId}/transports/${transportId}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showMessage('Транспорт успешно удален', 'success');
                loadTransports();
                loadFinanceData();
            } else {
                showMessage(response.message || 'Ошибка при удалении транспорта', 'error');
            }
        },
        error: function(xhr) {
            console.error('Ошибка при удалении транспорта:', xhr);
            showMessage('Ошибка при удалении транспорта', 'error');
        }
    });
}

// Вспомогательные функции
function resetForm(form) {
    if (form) {
        form.reset();
        // Очищаем классы валидации Bootstrap
        $(form).find('.is-invalid').removeClass('is-invalid');
        $(form).find('.is-valid').removeClass('is-valid');
    }
}

function formatMoney(amount) {
    return new Intl.NumberFormat('ru-RU', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount || 0);
}

function getPaymentStatus(total, paid) {
    if (paid <= 0) {
        return { class: 'bg-danger', text: 'Не оплачено' };
    } else if (paid >= total) {
        return { class: 'bg-success', text: 'Оплачено' };
    } else {
        return { class: 'bg-warning', text: 'Частично' };
    }
}

// Делаем функции глобальными для совместимости
window.initFinanceTabContent = initFinanceTabContent;
window.initFinanceHandlers = initFinanceHandlers;
// Экспорт функций в глобальную область видимости
window.initFinanceTabContent = initFinanceTabContent;
window.openAddWorkModal = openAddWorkModal;
window.openAddMaterialModal = openAddMaterialModal;
window.openAddTransportModal = openAddTransportModal;
window.loadFinanceData = loadFinanceData;
window.loadWorks = loadWorks;
window.loadMaterials = loadMaterials;
window.loadTransports = loadTransports;

// Инициализация при загрузке
$(document).ready(function() {
    console.log('Finance tab DOM ready');
    console.log('Проверка состояния modalManager:', {
        exists: !!window.modalManager,
        initialized: window.modalManagerInitialized,
        loadModal: !!(window.modalManager && window.modalManager.loadModal)
    });
    
    // Проверяем, если финансовая вкладка активна сразу при загрузке
    if ($('#finance-tab').hasClass('active')) {
        console.log('Финансовая вкладка активна при загрузке, инициализируем...');
        setTimeout(function() {
            initFinanceTabContent();
        }, 100);
    }
    
    // Обработчик для переключения на финансовую вкладку
    $(document).on('shown.bs.tab', '#finance-tab', function() {
        console.log('Переключение на финансовую вкладку');
        console.log('Состояние modalManager при переключении:', {
            exists: !!window.modalManager,
            initialized: window.modalManagerInitialized,
            loadModal: !!(window.modalManager && window.modalManager.loadModal)
        });
        
        if (!window.financeInitialized) {
            initFinanceTabContent();
        } else {
            console.log('Финансовая вкладка уже инициализирована');
        }
    });
});
</script>
@endpush