<!-- Финансовая информация проекта -->

<!-- Финансовая сводка -->
<div class="finance-summary mb-4">
    <?php echo $__env->make('partner.projects.finance.partials.finance-summary', [
        'summary' => [
            'works_total' => $project->works()->sum('amount') ?? 0,
            'works_paid' => $project->works()->sum('paid_amount') ?? 0,
            'materials_total' => $project->materials()->sum('amount') ?? 0,
            'materials_paid' => $project->materials()->sum('paid_amount') ?? 0,
            'transport_total' => $project->transports()->sum('amount') ?? 0,
            'transport_paid' => $project->transports()->sum('paid_amount') ?? 0,
            'grand_total' => ($project->works()->sum('amount') ?? 0) + ($project->materials()->sum('amount') ?? 0) + ($project->transports()->sum('amount') ?? 0),
            'total_paid' => ($project->works()->sum('paid_amount') ?? 0) + ($project->materials()->sum('paid_amount') ?? 0) + ($project->transports()->sum('paid_amount') ?? 0)
        ],
        'counts' => [
            'works' => $project->works()->count(),
            'materials' => $project->materials()->count(),
            'transports' => $project->transports()->count()
        ]
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Финансовое планирование</h5>
        
        </div>
        <ul class="nav nav-tabs card-header-tabs" id="finance-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="works-tab" data-bs-toggle="tab" data-bs-target="#works-pane" type="button" role="tab" aria-controls="works-pane" aria-selected="true">
                    <i class="bi bi-tools me-1 text-primary"></i>
                    <span class="d-none d-md-inline">Планирование работ</span>
                    <span class="d-md-none">Работы</span>
                    <span class="badge bg-primary ms-2" data-counter="works"><?php echo e($project->works()->count()); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials-pane" type="button" role="tab" aria-controls="materials-pane" aria-selected="false">
                    <i class="bi bi-box-seam me-1 text-success"></i>
                    <span class="d-none d-md-inline">Планирование материалов</span>
                    <span class="d-md-none">Материалы</span>
                    <span class="badge bg-success ms-2" data-counter="materials"><?php echo e($project->materials()->count()); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="transport-tab" data-bs-toggle="tab" data-bs-target="#transport-pane" type="button" role="tab" aria-controls="transport-pane" aria-selected="false">
                    <i class="bi bi-truck me-1 text-warning"></i>
                    <span class="d-none d-md-inline">Планирование транспорта</span>
                    <span class="d-md-none">Транспорт</span>
                    <span class="badge bg-warning ms-2" data-counter="transport"><?php echo e($project->transports()->count()); ?></span>
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
                        <h6 class="mb-0">
                            <span class="d-none d-md-inline">Планирование работ</span>
                            <span class="d-md-none">Работы</span>
                        </h6>
                        <small class="text-muted d-none d-md-block">Детализация работ для планирования (не влияет на финансовые показатели)</small>
                    </div>
                    <?php if(\App\Helpers\UserRoleHelper::canManageProjects()): ?>
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#workModal">
                        <i class="bi bi-plus-circle"></i>
                        <span class="d-none d-md-inline ms-1">Добавить работу</span>
                    </button>
                    <?php endif; ?>
                </div>
                <div id="worksContainer">
                    <?php echo $__env->make('partner.projects.finance.partials.works-partial', [
                        'works' => $project->works()->orderBy('created_at', 'desc')->get()
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>

            <!-- Вкладка Материалы -->
            <div class="tab-pane fade" id="materials-pane" role="tabpanel" aria-labelledby="materials-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0">
                            <span class="d-none d-md-inline">Планирование материалов</span>
                            <span class="d-md-none">Материалы</span>
                        </h6>
                        <small class="text-muted d-none d-md-block">Детализация материалов для планирования (не влияет на финансовые показатели)</small>
                    </div>
                    <?php if(\App\Helpers\UserRoleHelper::canManageProjects()): ?>
                    <button class="btn btn-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#materialModal">
                        <i class="bi bi-plus-circle"></i>
                        <span class="d-none d-md-inline ms-1">Добавить материал</span>
                    </button>
                    <?php endif; ?>
                </div>
                <div id="materialsContainer">
                    <?php echo $__env->make('partner.projects.finance.partials.materials-partial', [
                        'materials' => $project->materials()->orderBy('created_at', 'desc')->get()
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>

            <!-- Вкладка Транспорт -->
            <div class="tab-pane fade" id="transport-pane" role="tabpanel" aria-labelledby="transport-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0">Планирование транспорта</h6>
                        <small class="text-muted">Детализация транспорта для планирования (не влияет на финансовые показатели)</small>
                    </div>
                    <?php if(\App\Helpers\UserRoleHelper::canManageProjects()): ?>
                    <button class="btn btn-warning btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#transportModal">
                        <i class="bi bi-plus-circle me-1"></i>Добавить транспорт
                    </button>
                    <?php endif; ?>
                </div>
                <div id="transportContainer">
                    <?php echo $__env->make('partner.projects.finance.partials.transports-partial', [
                        'transports' => $project->transports()->orderBy('created_at', 'desc')->get()
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальные окна -->
<?php echo $__env->make('partner.projects.tabs.modals.work-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('partner.projects.tabs.modals.material-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('partner.projects.tabs.modals.transport-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script>
// Унифицированная инициализация финансовой вкладки
$(document).ready(function() {
    console.log('💰 Инициализация финансовой вкладки...');
    
    if (window.projectManager) {
        // Используем новую систему инициализации
        window.projectManager.initPage('finance_tab', function() {
            console.log('📊 Специфическая инициализация для финансовой вкладки...');
            
            // Основная инициализация финансовых компонентов
            window.projectManager.initFinance();
            
            // Инициализация расчетов для inline форм
            setupInlineCalculations();
        });
    } else {
        console.warn('⚠️ ProjectManager не найден в финансовой вкладке');
        setupInlineCalculations();
    }
});

// Функция для настройки расчетов в inline формах
function setupInlineCalculations() {
    // Инициализация расчета для всех форм
    setupCalculation('workPrice', 'workQuantity', 'workTotalCost');
    setupCalculation('materialPrice', 'materialQuantity', 'materialTotalCost');
    setupCalculation('transportPrice', 'transportQuantity', 'transportTotalCost');
}

// Универсальная функция для расчета общей стоимости
function setupCalculation(priceId, quantityId, totalId) {
    const priceInput = document.getElementById(priceId);
    const quantityInput = document.getElementById(quantityId);
    const totalSpan = document.getElementById(totalId);
    
    if (priceInput && quantityInput && totalSpan) {
        function updateTotal() {
            const price = parseFloat(priceInput.value) || 0;
            const quantity = parseFloat(quantityInput.value) || 0;
            const total = price * quantity;
            totalSpan.textContent = total.toLocaleString('ru-RU', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' ₽';
        }
        
        priceInput.addEventListener('input', updateTotal);
        quantityInput.addEventListener('input', updateTotal);
        updateTotal(); // Первоначальный расчет
    }
}

console.log('✅ Финансовая вкладка загружена');
</script>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/finance.blade.php ENDPATH**/ ?>