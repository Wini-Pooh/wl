@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid fade-in px-2 px-md-3">
    <!-- Заголовок с мобильной адаптацией -->
    <div class="row align-items-center mb-3 mb-md-4">
        <div class="col-12 col-md-8 mb-2 mb-md-0">
            <h2 class="mb-0 gradient-text fs-4 fs-md-2">
                <i class="bi bi-graph-up me-2"></i>
                <span class="d-none d-sm-inline">Аналитика и отчеты</span>
                <span class="d-sm-none">Аналитика</span>
            </h2>
        </div>
        <div class="col-12 col-md-4">
            <select id="periodSelect" class="form-select form-select-sm form-select-md-regular w-100">
                <option value="7" {{ $period == 7 ? 'selected' : '' }}>7 дней</option>
                <option value="30" {{ $period == 30 ? 'selected' : '' }}>30 дней</option>
                <option value="90" {{ $period == 90 ? 'selected' : '' }}>90 дней</option>
                <option value="365" {{ $period == 365 ? 'selected' : '' }}>Год</option>
            </select>
        </div>
    </div>

    <!-- Навигация по вкладкам с мобильной адаптацией -->
    <div class="card mb-3 mb-md-4 shadow-sm">
        <div class="card-header p-1 p-md-2">
            <ul class="nav nav-tabs card-header-tabs nav-justified" id="analyticsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-2 py-2 px-md-3 py-md-3" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab">
                        <i class="bi bi-cash-stack d-block d-md-inline me-md-1 fs-5 fs-md-6"></i>
                        <span class="d-none d-md-inline">Финансы</span>
                        <small class="d-block d-md-none mt-1">Финансы</small>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-2 py-2 px-md-3 py-md-3" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab">
                        <i class="bi bi-building d-block d-md-inline me-md-1 fs-5 fs-md-6"></i>
                        <span class="d-none d-md-inline">Проекты</span>
                        <small class="d-block d-md-none mt-1">Проекты</small>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-2 py-2 px-md-3 py-md-3" id="employees-tab" data-bs-toggle="tab" data-bs-target="#employees" type="button" role="tab">
                        <i class="bi bi-people d-block d-md-inline me-md-1 fs-5 fs-md-6"></i>
                        <span class="d-none d-md-inline">Сотрудники</span>
                        <small class="d-block d-md-none mt-1">Сотрудники</small>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-2 py-2 px-md-3 py-md-3" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                        <i class="bi bi-speedometer2 d-block d-md-inline me-md-1 fs-5 fs-md-6"></i>
                        <span class="d-none d-md-inline">Общая</span>
                        <small class="d-block d-md-none mt-1">Общее</small>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Контент вкладок -->
    <div class="tab-content" id="analyticsTabContent">
        <!-- Финансовая аналитика -->
        <div class="tab-pane fade show active" id="financial" role="tabpanel">
            @include('partner.analytics.tabs.financial', ['data' => $financialData])
        </div>

        <!-- Аналитика проектов -->
        <div class="tab-pane fade" id="projects" role="tabpanel">
            @include('partner.analytics.tabs.projects', ['data' => $projectData])
        </div>

        <!-- Аналитика сотрудников -->
        <div class="tab-pane fade" id="employees" role="tabpanel">
            @include('partner.analytics.tabs.employees', ['data' => $employeeData])
        </div>

        <!-- Общая аналитика -->
        <div class="tab-pane fade" id="general" role="tabpanel">
            @include('partner.analytics.tabs.general', ['data' => $generalData])
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<style>
/* Специальные стили для аналитики с синими градиентами */
.analytics-card {
    background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.15);
    transition: all 0.3s ease;
    border-left: 4px solid var(--primary-blue);
}

.analytics-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.25);
    border-left-color: var(--primary-blue-dark);
}

.chart-container {
    position: relative;
    height: 400px;
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.15);
}

.metric-card {
    text-align: center;
    padding: 1.5rem;
    border-radius: 12px;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    margin-bottom: 1rem;
    border: none;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.25);
    transition: all 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.35);
}

.metric-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.metric-label {
    font-size: 1rem;
    opacity: 0.95;
    font-weight: 500;
}

.chart-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.15);
    border-radius: 12px;
    overflow: hidden;
    background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
}

/* Цветовые градиенты для метрик */
.bg-revenue {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-expenses {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
}

.bg-profit {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-projects {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.loading-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
}

/* Улучшенные стили для мобильной адаптации */
.container-fluid {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
}

@media (min-width: 768px) {
    .container-fluid {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
}

/* Мобильные вкладки */
.nav-justified .nav-item {
    flex: 1;
}

.nav-justified .nav-link {
    text-align: center;
    border-radius: 0.375rem 0.375rem 0 0;
    transition: all 0.2s ease;
}

/* Адаптивные размеры шрифтов */
.fs-md-6 {
    font-size: 1rem;
}

@media (min-width: 768px) {
    .fs-md-6 {
        font-size: 1rem;
    }
}

.form-select-sm {
    padding: 0.25rem 1.75rem 0.25rem 0.5rem;
    font-size: 0.875rem;
}

@media (min-width: 768px) {
    .form-select-md-regular {
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
        font-size: 1rem;
    }
}

/* Стили для карточек вкладок */
.nav-tabs {
    border-bottom: none;
}

.nav-tabs .nav-link {
    border: none;
    background: transparent;
    color: #6c757d;
    font-weight: 500;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border-color: transparent;
    color: #007bff;
    background: rgba(0, 123, 255, 0.1);
}

.nav-tabs .nav-link.active {
    color: #007bff;
    background: rgba(0, 123, 255, 0.1);
    border-color: transparent;
    font-weight: 600;
}

/* Мобильная адаптация для аналитики */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .chart-container {
        height: 280px;
        padding: 12px;
    }
    
    .metric-card {
        padding: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .metric-value {
        font-size: 1.8rem;
    }
    
    .metric-label {
        font-size: 0.85rem;
    }
    
    /* Улучшенные адаптивные вкладки */
    .nav-tabs .nav-link {
        padding: 0.5rem 0.25rem;
        font-size: 0.8rem;
        min-height: 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    
    .nav-tabs .nav-link i {
        font-size: 1.2rem;
        margin-bottom: 0.25rem;
    }
    
    .nav-tabs .nav-link small {
        font-size: 0.7rem;
        line-height: 1;
    }
    
    /* Карточки на мобильных */
    .card {
        border-radius: 0.5rem;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
    }
    
    .chart-container {
        height: 220px;
        padding: 8px;
    }
    
    .metric-card {
        padding: 0.75rem;
        margin-bottom: 0.5rem;
    }
    
    .metric-value {
        font-size: 1.5rem;
    }
    
    .metric-label {
        font-size: 0.75rem;
    }
    
    .nav-tabs .nav-link {
        padding: 0.4rem 0.2rem;
        min-height: 55px;
        font-size: 0.75rem;
    }
    
    .nav-tabs .nav-link i {
        font-size: 1.1rem;
    }
    
    .nav-tabs .nav-link small {
        font-size: 0.65rem;
    }
    
    /* Более компактные карточки */
    .card-header {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    /* Уменьшаем отступы */
    .mb-3, .mb-md-4 {
        margin-bottom: 0.75rem !important;
    }
    
    .row {
        margin-left: -0.375rem;
        margin-right: -0.375rem;
    }
    
    .row > * {
        padding-left: 0.375rem;
        padding-right: 0.375rem;
    }
}

/* Дополнительные стили для улучшения внешнего вида */
.card-header-tabs {
    border-bottom: none;
}

.card-header-tabs .nav-link {
    background: transparent;
    border: none;
    color: #6c757d;
    margin: 0 2px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.card-header-tabs .nav-link:hover {
    color: #007bff;
    background: rgba(0, 123, 255, 0.1);
}

.card-header-tabs .nav-link.active {
    background: rgba(0, 123, 255, 0.15);
    color: #007bff;
    font-weight: 600;
    border: 1px solid rgba(0, 123, 255, 0.2);
}

/* Анимация загрузки */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 12px;
    z-index: 10;
}

.loading-overlay .spinner-border {
    color: var(--primary-blue);
    width: 3rem;
    height: 3rem;
}

/* Дополнительные утилитарные классы для мобильной адаптации */
.text-nowrap-mobile {
    white-space: nowrap;
}

@media (max-width: 576px) {
    .text-nowrap-mobile {
        white-space: normal;
        word-break: break-word;
    }
}

/* Улучшения для кнопок и форм на мобильных */
@media (max-width: 768px) {
    .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .form-select {
        font-size: 0.875rem;
    }
}

/* Адаптивные отступы */
.spacing-mobile {
    margin-bottom: 1rem;
}

@media (min-width: 768px) {
    .spacing-mobile {
        margin-bottom: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
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
@endpush
