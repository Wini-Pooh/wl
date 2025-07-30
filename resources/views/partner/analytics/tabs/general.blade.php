<!-- Общая аналитика -->
<div class="row mb-4">
    <!-- Основные KPI -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card analytics-card bg-gradient-primary ">
            <div class="card-body text-center">
                <i class="bi bi-graph-up" style="font-size: 2rem;"></i>
                <div class="h4 mt-2">{{ number_format($data['conversion_rate'], 1) }}%</div>
                <p class="card-text">Конверсия</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card analytics-card bg-gradient-success ">
            <div class="card-body text-center">
                <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
                <div class="h4 mt-2">{{ number_format($data['avg_project_duration'], 0) }}</div>
                <p class="card-text">Дней на проект</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card analytics-card bg-gradient-info ">
            <div class="card-body text-center">
                <i class="bi bi-emoji-smile" style="font-size: 2rem;"></i>
                <div class="h4 mt-2">{{ number_format($data['client_satisfaction'], 1) }}%</div>
                <p class="card-text">Удовлетворенность</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card analytics-card bg-gradient-warning ">
            <div class="card-body text-center">
                <i class="bi bi-percent" style="font-size: 2rem;"></i>
                <div class="h4 mt-2">{{ number_format($data['profitability_ratio'], 1) }}%</div>
                <p class="card-text">Рентабельность</p>
            </div>
        </div>
    </div>
</div>

<!-- Интерактивные графики -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        Интерактивная аналитика
                    </h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="chartType" id="revenue" autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="revenue">Доходы</label>

                        <input type="radio" class="btn-check" name="chartType" id="expenses" autocomplete="off">
                        <label class="btn btn-outline-primary" for="expenses">Расходы</label>

                        <input type="radio" class="btn-check" name="chartType" id="projects" autocomplete="off">
                        <label class="btn btn-outline-primary" for="projects">Проекты</label>

                        <input type="radio" class="btn-check" name="chartType" id="employees" autocomplete="off">
                        <label class="btn btn-outline-primary" for="employees">Сотрудники</label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="interactiveChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Детализированная статистика -->
<div class="row">
    <!-- Общая эффективность -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Общая эффективность
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Выполнение планов</span>
                        <span><strong>85%</strong></span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Качество работ</span>
                        <span><strong>92%</strong></span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-primary" style="width: 92%"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Соблюдение сроков</span>
                        <span><strong>78%</strong></span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width: 78%"></div>
                    </div>
                </div>
                
                <div class="mb-0">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Рентабельность</span>
                        <span><strong>{{ number_format($data['profitability_ratio'], 1) }}%</strong></span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-info" style="width: {{ min($data['profitability_ratio'], 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Быстрая статистика -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Быстрая статистика
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end pb-3">
                            <h4 class="text-primary">15</h4>
                            <p class="text-muted small mb-0">Проектов в этом месяце</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="pb-3">
                            <h4 class="text-success">8</h4>
                            <p class="text-muted small mb-0">Завершенных проектов</p>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end pb-3">
                            <h4 class="text-warning">{{ number_format(2500000, 0, ',', ' ') }}</h4>
                            <p class="text-muted small mb-0">Средний доход (₽)</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="pb-3">
                            <h4 class="text-info">45</h4>
                            <p class="text-muted small mb-0">Дней средний срок</p>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-danger">3</h4>
                            <p class="text-muted small mb-0">Просроченных проектов</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-secondary">95%</h4>
                        <p class="text-muted small mb-0">Общая загруженность</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Прогнозы и рекомендации -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-crystal-ball me-2"></i>
                    Прогнозы и рекомендации
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card border-success">
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="bi bi-trending-up me-2"></i>
                                    Положительные тенденции
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        Рост количества проектов на 15%
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        Улучшение рентабельности
                                    </li>
                                    <li class="mb-0">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        Снижение просроченных платежей
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card border-warning">
                            <div class="card-body">
                                <h6 class="card-title text-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Требуют внимания
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-dash-circle text-warning me-2"></i>
                                        Соблюдение сроков проектов
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-dash-circle text-warning me-2"></i>
                                        Контроль расходов на материалы
                                    </li>
                                    <li class="mb-0">
                                        <i class="bi bi-dash-circle text-warning me-2"></i>
                                        Планирование загрузки сотрудников
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card border-info">
                            <div class="card-body">
                                <h6 class="card-title text-info">
                                    <i class="bi bi-lightbulb me-2"></i>
                                    Рекомендации
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-arrow-right text-info me-2"></i>
                                        Автоматизировать уведомления о платежах
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-arrow-right text-info me-2"></i>
                                        Внедрить KPI для сотрудников
                                    </li>
                                    <li class="mb-0">
                                        <i class="bi bi-arrow-right text-info me-2"></i>
                                        Оптимизировать закупки материалов
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let interactiveChart = null;

document.addEventListener('DOMContentLoaded', function() {
    // Инициализируем интерактивный график
    initInteractiveChart();
    
    // Обработчики переключения типов графиков
    document.querySelectorAll('input[name="chartType"]').forEach(input => {
        input.addEventListener('change', function() {
            updateInteractiveChart(this.id);
        });
    });
});

function initInteractiveChart() {
    const ctx = document.getElementById('interactiveChart').getContext('2d');
    updateInteractiveChart('revenue');
}

function updateInteractiveChart(chartType) {
    const ctx = document.getElementById('interactiveChart').getContext('2d');
    
    // Уничтожаем предыдущий график
    if (interactiveChart) {
        interactiveChart.destroy();
    }
    
    // Показываем индикатор загрузки
    showChartLoading();
    
    // Запрашиваем данные для выбранного типа графика
    fetch(`/partner/analytics/chart-data?type=${chartType}&period={{ $period }}`)
        .then(response => response.json())
        .then(data => {
            hideChartLoading();
            
            switch(chartType) {
                case 'revenue':
                    interactiveChart = createLineChart(ctx, 'Доходы', data, '#28a745');
                    break;
                case 'expenses':
                    interactiveChart = createLineChart(ctx, 'Расходы', data, '#dc3545');
                    break;
                case 'projects':
                    interactiveChart = createLineChart(ctx, 'Проекты', data, '#007bff');
                    break;
                case 'employees':
                    // Для сотрудников создаем круговую диаграмму
                    interactiveChart = createPieChart(ctx, data);
                    break;
            }
        })
        .catch(error => {
            hideChartLoading();
            console.error('Ошибка загрузки данных:', error);
        });
}

function showChartLoading() {
    const canvas = document.getElementById('interactiveChart');
    const container = canvas.parentElement;
    
    if (!container.querySelector('.chart-loading')) {
        const loading = document.createElement('div');
        loading.className = 'chart-loading position-absolute top-50 start-50 translate-middle';
        loading.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        `;
        container.appendChild(loading);
    }
}

function hideChartLoading() {
    const loading = document.querySelector('.chart-loading');
    if (loading) {
        loading.remove();
    }
}
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

.chart-container {
    position: relative;
}
</style>
