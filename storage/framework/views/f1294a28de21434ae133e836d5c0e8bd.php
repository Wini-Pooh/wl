

<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<div class="container-fluid">
    <!-- Заголовок -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mb-2 mb-md-0">
            <i class="bi bi-graph-up me-2"></i>
            <span class="d-none d-sm-inline">Аналитика и отчеты</span>
            <span class="d-sm-none">Аналитика</span>
        </h2>
        <div class="btn-group">
            <select id="periodSelect" class="form-select" style="width: 200px;">
                <option value="7" <?php echo e($period == 7 ? 'selected' : ''); ?>>За 7 дней</option>
                <option value="30" <?php echo e($period == 30 ? 'selected' : ''); ?>>За 30 дней</option>
                <option value="90" <?php echo e($period == 90 ? 'selected' : ''); ?>>За 90 дней</option>
                <option value="365" <?php echo e($period == 365 ? 'selected' : ''); ?>>За год</option>
            </select>
        </div>
    </div>

    <!-- Навигация по вкладкам -->
    <div class="card mb-4">
        <div class="card-header p-0">
            <ul class="nav nav-tabs card-header-tabs" id="analyticsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab">
                        <i class="bi bi-cash-stack me-2"></i>
                        <span class="d-none d-sm-inline">Финансы</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab">
                        <i class="bi bi-building me-2"></i>
                        <span class="d-none d-sm-inline">Проекты</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="employees-tab" data-bs-toggle="tab" data-bs-target="#employees" type="button" role="tab">
                        <i class="bi bi-people me-2"></i>
                        <span class="d-none d-sm-inline">Сотрудники</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                        <i class="bi bi-speedometer2 me-2"></i>
                        <span class="d-none d-sm-inline">Общая статистика</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Контент вкладок -->
    <div class="tab-content" id="analyticsTabContent">
        <!-- Финансовая аналитика -->
        <div class="tab-pane fade show active" id="financial" role="tabpanel">
            <?php echo $__env->make('partner.analytics.tabs.financial', ['data' => $financialData], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        <!-- Аналитика проектов -->
        <div class="tab-pane fade" id="projects" role="tabpanel">
            <?php echo $__env->make('partner.analytics.tabs.projects', ['data' => $projectData], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        <!-- Аналитика сотрудников -->
        <div class="tab-pane fade" id="employees" role="tabpanel">
            <?php echo $__env->make('partner.analytics.tabs.employees', ['data' => $employeeData], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        <!-- Общая аналитика -->
        <div class="tab-pane fade" id="general" role="tabpanel">
            <?php echo $__env->make('partner.analytics.tabs.general', ['data' => $generalData], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<link rel="stylesheet" href="<?php echo e(asset('css/analytics.css')); ?>">
<style>
.analytics-card {
    transition: transform 0.2s;
}

.analytics-card:hover {
    transform: translateY(-2px);
}

.chart-container {
    position: relative;
    height: 400px;
}

.metric-card {
    text-align: center;
    padding: 1.5rem;
    border-radius: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin-bottom: 1rem;
}

.metric-value {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.metric-label {
    font-size: 1rem;
    opacity: 0.9;
}

.chart-card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 0.5rem;
    overflow: hidden;
}

.bg-revenue {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-expenses {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
}

.bg-profit {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
}

.bg-projects {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.loading-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@2.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработчик изменения периода
    document.getElementById('periodSelect').addEventListener('change', function() {
        const period = this.value;
        const url = new URL(window.location);
        url.searchParams.set('period', period);
        window.location.href = url.toString();
    });

    // Инициализация графиков
    initializeCharts();
});

function initializeCharts() {
    // Инициализация происходит в соответствующих вкладках
    console.log('Аналитика загружена');
}

// Универсальная функция для создания линейного графика
function createLineChart(ctx, label, data, color = '#007bff') {
    return new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [{
                label: label,
                data: data,
                borderColor: color,
                backgroundColor: color + '20',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day',
                        displayFormats: {
                            day: 'dd.MM'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('ru-RU').format(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + 
                                   new Intl.NumberFormat('ru-RU').format(context.parsed.y) + ' ₽';
                        }
                    }
                }
            }
        }
    });
}

// Универсальная функция для создания круговой диаграммы
function createPieChart(ctx, data, colors = null) {
    const defaultColors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
        '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
    ];
    
    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(data),
            datasets: [{
                data: Object.values(data),
                backgroundColor: colors || defaultColors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + 
                                   new Intl.NumberFormat('ru-RU').format(context.parsed) + 
                                   ' ₽ (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// Функция для создания столбчатой диаграммы
function createBarChart(ctx, labels, data, label, color = '#007bff') {
    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: color + '80',
                borderColor: color,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('ru-RU').format(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + 
                                   new Intl.NumberFormat('ru-RU').format(context.parsed.y);
                        }
                    }
                }
            }
        }
    });
}

// Функция для форматирования чисел
function formatNumber(num) {
    return new Intl.NumberFormat('ru-RU').format(num);
}

// Функция для форматирования валюты
function formatCurrency(num) {
    return new Intl.NumberFormat('ru-RU').format(num) + ' ₽';
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/analytics/dashboard.blade.php ENDPATH**/ ?>