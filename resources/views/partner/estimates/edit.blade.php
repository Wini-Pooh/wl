@extends('layouts.app')

@section('head')
    <!-- Подключаем автосохранение -->
    <script src="{{ asset('js/estimate-autosave.js') }}"></script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-pencil-square me-2"></i>
                    Редактирование сметы #EST-{{ str_pad($estimate->id, 4, '0', STR_PAD_LEFT) }}
                </h2>
                <div class="btn-group">
                    <!-- Выпадающий список экспорта -->
                    <div class="dropdown me-2">
                        <button class="btn btn-success dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-download me-1"></i> Экспорт
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                            <li><h6 class="dropdown-header">PDF</h6></li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="exportToPDF('full')">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>
                                    Полная версия
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="exportToPDF('master')">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>
                                    Для мастера
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="exportToPDF('client')">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>
                                    Для клиента
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Excel</h6></li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="exportToExcel('full')">
                                    <i class="bi bi-file-earmark-excel me-2"></i>
                                    Полная версия
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="exportToExcel('master')">
                                    <i class="bi bi-file-earmark-excel me-2"></i>
                                    Для мастера
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="exportToExcel('client')">
                                    <i class="bi bi-file-earmark-excel me-2"></i>
                                    Для клиента
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Кнопка сохранения как шаблон -->
                    <button type="button" class="btn btn-info me-2" onclick="openSaveTemplateModal()">
                        <i class="bi bi-bookmark me-1"></i> Сохранить как шаблон
                    </button>
                    
                    <!-- Кнопка загрузки для клиента -->
                    <button type="button" class="btn btn-primary me-2" onclick="downloadForClient()">
                        <i class="bi bi-cloud-download me-1"></i> Загрузить клиенту
                    </button>
                    
                    <a href="{{ route('partner.estimates.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> К списку
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Форма редактирования сметы -->
            <form id="estimateForm" action="{{ route('partner.estimates.update', $estimate->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $estimate->id }}">

                <!-- Скрытые поля для сохранения важных параметров -->
                <input type="hidden" name="project_id" value="{{ $estimate->project_id }}">
                <input type="hidden" name="type" value="{{ $estimate->type }}">
                <input type="hidden" name="name" value="{{ $estimate->name }}">
                <input type="hidden" name="description" value="{{ $estimate->description }}">
                <input type="hidden" name="status" value="{{ $estimate->status }}">

                <!-- Редактор сметы -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Редактор сметы</h5>
                            <div>
                                <button type="button" class="btn btn-outline-dark btn-sm me-2" id="toggleFullscreen" title="Полноэкранный режим">
                                    <i class="bi bi-arrows-fullscreen me-1"></i> Полный экран
                                </button>
                                <button type="button" class="btn btn-success btn-sm" id="addPosition">
                                    <i class="bi bi-plus-lg me-1"></i> Добавить строку
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm ms-2" id="addSection">
                                    <i class="bi bi-collection me-1"></i> Добавить раздел
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="estimate-editor" class="estimate-editor">
                            <!-- Здесь будут загружены секции и строки сметы -->
                        </div>
                        
                        <div class="p-3 bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="markup_percent" class="form-label">Общая наценка, %</label>
                                        <div class="input-group">
                                            <input type="number" id="markup_percent" name="data[totals][markup_percent]" 
                                                   class="form-control" step="0.01" min="0" max="100"
                                                   value="{{ is_array($estimateData['totals']['markup_percent'] ?? null) ? 20 : ($estimateData['totals']['markup_percent'] ?? 20) }}">
                                            <button type="button" class="btn btn-outline-secondary" id="applyGlobalMarkup">
                                                <i class="bi bi-check-lg"></i> Применить ко всем
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="discount_percent" class="form-label">Общая скидка, %</label>
                                        <div class="input-group">
                                            <input type="number" id="discount_percent" name="data[totals][discount_percent]" 
                                                   class="form-control" step="0.01" min="0" max="100"
                                                   value="{{ is_array($estimateData['totals']['discount_percent'] ?? null) ? 0 : ($estimateData['totals']['discount_percent'] ?? 0) }}">
                                            <button type="button" class="btn btn-outline-secondary" id="applyGlobalDiscount">
                                                <i class="bi bi-check-lg"></i> Применить ко всем
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <div class="card bg-light border">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between">
                                                <span>Стоимость работ:</span>
                                                <span class="fw-bold" id="work_cost_display">0.00 ₽</span>
                                            </div>
                                            <input type="hidden" name="data[totals][work_cost]" id="work_cost" value="{{ is_array($estimateData['totals']['work_cost'] ?? null) ? 0 : ($estimateData['totals']['work_cost'] ?? 0) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light border">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between">
                                                <span>Стоимость материалов:</span>
                                                <span class="fw-bold" id="materials_cost_display">0.00 ₽</span>
                                            </div>
                                            <input type="hidden" name="data[totals][materials_cost]" id="materials_cost" value="{{ is_array($estimateData['totals']['materials_cost'] ?? null) ? 0 : ($estimateData['totals']['materials_cost'] ?? 0) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success ">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between">
                                                <span>Выгода:</span>
                                                <span class="fw-bold" id="profit_display">0.00 ₽</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <small class="opacity-75">Наценка:</small>
                                                <small class="opacity-75" id="profit_percent_display">0%</small>
                                            </div>
                                            <input type="hidden" name="data[totals][profit_amount]" id="profit_amount" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between">
                                                <span>ИТОГО для клиента:</span>
                                                <span class="fw-bold" id="client_total_display">0.00 ₽</span>
                                            </div>
                                            <input type="hidden" name="data[totals][client_total]" id="client_total" value="{{ is_array($estimateData['totals']['client_total'] ?? null) ? 0 : ($estimateData['totals']['client_total'] ?? 0) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Шаблон для новой строки в смете -->
<template id="estimate-row-template">
    <tr class="estimate-row" data-row-id="{row_id}" draggable="true">
        <td class="position-number text-center">{position}</td>
        <td><input type="text" class="form-control" name="data[sections][{section_id}][items][{row_id}][name]" value="{name}"></td>
        <td>
            <select class="form-select" name="data[sections][{section_id}][items][{row_id}][unit]">
                <option value="м²" {unit_m2}>м²</option>
                <option value="м.п." {unit_m_p}>м.п.</option>
                <option value="шт" {unit_sht}>шт</option>
                <option value="компл." {unit_compl}>компл.</option>
                <option value="кг" {unit_kg}>кг</option>
                <option value="л" {unit_l}>л</option>
                <option value="м³" {unit_m3}>м³</option>
                <option value="м" {unit_m}>м</option>
                <option value="упак" {unit_upak}>упак</option>
            </select>
        </td>
        <td><input type="number" class="form-control quantity" name="data[sections][{section_id}][items][{row_id}][quantity]" step="0.01" min="0" value="{quantity}"></td>
        <td><input type="number" class="form-control price" name="data[sections][{section_id}][items][{row_id}][price]" step="0.01" min="0" value="{price}"></td>
        <td><span class="form-control-plaintext amount">{amount}</span>
            <input type="hidden" name="data[sections][{section_id}][items][{row_id}][amount]" value="{amount}">
        </td>
        <td><input type="number" class="form-control markup" name="data[sections][{section_id}][items][{row_id}][markup]" step="0.01" min="0" value="{markup}"></td>
        <td><input type="number" class="form-control discount" name="data[sections][{section_id}][items][{row_id}][discount]" step="0.01" min="0" value="{discount}"></td>
        <td>
            <span class="form-control-plaintext client-price">{client_price}</span>
            <input type="hidden" name="data[sections][{section_id}][items][{row_id}][client_price]" value="{client_price}">
        </td>
        <td>
            <span class="form-control-plaintext client-amount">{client_amount}</span>
            <input type="hidden" name="data[sections][{section_id}][items][{row_id}][client_amount]" value="{client_amount}">
        </td>
        <td class="text-center">
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary drag-handle" title="Перетащить строку">
                    <i class="bi bi-grip-vertical"></i>
                </button>
                <button type="button" class="btn btn-outline-danger remove-row" title="Удалить">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </td>
    </tr>
</template>

<!-- Шаблон для секции сметы -->
<template id="estimate-section-template">
    <div class="section mb-4" data-section-id="{section_id}">
        <div class="section-header d-flex justify-content-between align-items-center p-2 bg-light border">
            <div class="d-flex align-items-center" style="width:100%; max-width: 500px;">
                <button type="button" class="btn btn-outline-secondary btn-sm me-2 toggle-section" title="Свернуть/развернуть">
                    <i class="bi bi-chevron-down"></i>
                </button>
                <input type="text" class="form-control form-control-sm section-title" 
                       name="data[sections][{section_id}][title]" 
                       value="{section_title}" >
                <input type="hidden" name="data[sections][{section_id}][id]" value="{section_id}">
                <input type="hidden" name="data[sections][{section_id}][type]" value="section">
            </div>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-success btn-sm add-row-to-section" title="Добавить строку">
                    <i class="bi bi-plus-lg"></i>
                </button>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Действия с разделом">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item move-section-up" href="#"><i class="bi bi-arrow-up me-2"></i>Переместить вверх</a></li>
                        <li><a class="dropdown-item move-section-down" href="#"><i class="bi bi-arrow-down me-2"></i>Переместить вниз</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item remove-section text-danger" href="#"><i class="bi bi-trash me-2"></i>Удалить раздел</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="3%" class="text-center">№</th>
                            <th width="25%">Наименование работ/материалов</th>
                            <th width="5%">Ед.изм.</th>
                            <th width="8%">Кол-во</th>
                            <th width="8%">Цена</th>
                            <th width="9%">Стоимость</th>
                            <th width="6%">Наценка, %</th>
                            <th width="6%">Скидка, %</th>
                            <th width="9%">Цена клиента</th>
                            <th width="9%">Сумма клиента</th>
                            <th width="5%">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="section-items">
                        <!-- Здесь будут строки сметы -->
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="5" class="text-end fw-bold">Итого по разделу:</td>
                            <td class="section-total fw-bold">{section_total}</td>
                            <td colspan="3" class="text-end fw-bold">Итого для клиента:</td>
                            <td class="section-client-total fw-bold">{section_client_total}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</template>

<!-- Модальное окно для создания нового раздела -->
<div class="modal fade" id="newSectionModal" tabindex="-1" aria-labelledby="newSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newSectionModalLabel">Добавить новый раздел</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="newSectionTitle" class="form-label">Название раздела</label>
                    <input type="text" class="form-control" id="newSectionTitle" placeholder="Введите название раздела">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="createSectionBtn">Создать раздел</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для сохранения шаблона -->
<div class="modal fade" id="saveTemplateModal" tabindex="-1" aria-labelledby="saveTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saveTemplateModalLabel">Сохранить как шаблон</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="templateName" class="form-label">Название шаблона <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="templateName" placeholder="Введите название шаблона" required>
                </div>
                <div class="mb-3">
                    <label for="templateDescription" class="form-label">Описание</label>
                    <textarea class="form-control" id="templateDescription" rows="3" placeholder="Краткое описание шаблона (необязательно)"></textarea>
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Шаблон будет сохранен с текущими данными сметы и будет доступен при создании новых смет типа "{{ $estimate->type_name }}".
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="saveTemplateBtn">
                    <i class="bi bi-bookmark me-1"></i> Сохранить шаблон
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Страница загружена, начинаем загрузку данных сметы');
    
    // Проверка наличия контейнера редактора
    const editorContainer = document.getElementById('estimate-editor');
    if (!editorContainer) {
        console.error('ОШИБКА: Контейнер редактора сметы не найден!');
    } else {
        console.log('Контейнер редактора найден');
    }
    
    // Проверка наличия всех шаблонов
    const rowTemplate = document.getElementById('estimate-row-template');
    const sectionTemplate = document.getElementById('estimate-section-template');
    
    if (!rowTemplate) {
        console.error('ОШИБКА: Шаблон строки сметы не найден!');
    } else {
        console.log('Шаблон строки найден');
    }
    
    if (!sectionTemplate) {
        console.error('ОШИБКА: Шаблон секции сметы не найден!');
    } else {
        console.log('Шаблон секции найден');
    }
    
    // Для страницы редактирования - загрузка данных сметы
    try {
        const estimateData = {!! json_encode($estimateData) !!};
        console.log('Данные сметы получены:', estimateData);
        loadEstimateData(estimateData);
    } catch (error) {
        console.error('ОШИБКА при загрузке данных сметы:', error);
    }
    
    // Принудительный пересчет итогов после загрузки данных
    setTimeout(() => {
        calculateAllTotals();
        console.log('Итоги пересчитаны после загрузки данных');
    }, 100);
    
    // Обработка добавления новой позиции
    document.getElementById('addPosition').addEventListener('click', function() {
        // Добавляем новую строку в последний раздел
        const sections = document.querySelectorAll('.section');
        if (sections.length > 0) {
            const lastSection = sections[sections.length - 1];
            const sectionId = lastSection.getAttribute('data-section-id');
            addEstimateRow(sectionId);
            markForAutosave();
        } else {
            // Если нет секций, создаем новую
            showNewSectionModal();
        }
    });

    // Обработка добавления нового раздела
    document.getElementById('addSection').addEventListener('click', function() {
        showNewSectionModal();
    });

    // Обработчик для кнопки создания раздела в модальном окне
    document.getElementById('createSectionBtn').addEventListener('click', function() {
        const title = document.getElementById('newSectionTitle').value.trim();
        if (title) {
            addEstimateSection(title);
            const modal = bootstrap.Modal.getInstance(document.getElementById('newSectionModal'));
            modal.hide();
            document.getElementById('newSectionTitle').value = '';
            markForAutosave();
        }
    });

    // Глобальное применение наценки
    document.getElementById('applyGlobalMarkup').addEventListener('click', function() {
        const markup = parseFloat(document.getElementById('markup_percent').value) || 0;
        document.querySelectorAll('.markup').forEach(input => {
            input.value = markup;
            input.dispatchEvent(new Event('input'));
        });
        calculateAllTotals();
        markForAutosave();
    });

    // Глобальное применение скидки
    document.getElementById('applyGlobalDiscount').addEventListener('click', function() {
        const discount = parseFloat(document.getElementById('discount_percent').value) || 0;
        document.querySelectorAll('.discount').forEach(input => {
            input.value = discount;
            input.dispatchEvent(new Event('input'));
        });
        calculateAllTotals();
        markForAutosave();
    });

    // Обработчик изменения наценки для пересчета выгоды
    document.getElementById('markup_percent').addEventListener('input', function() {
        calculateAllTotals();
        markForAutosave();
    });

    // Обработчик изменения скидки для пересчета итогов
    document.getElementById('discount_percent').addEventListener('input', function() {
        calculateAllTotals();
        markForAutosave();
    });

    // Обработчик полноэкранного режима
    document.getElementById('toggleFullscreen').addEventListener('click', function() {
        toggleFullscreenMode();
    });

    // Делегирование событий для динамически добавляемых элементов
    document.addEventListener('click', function(event) {
        // Удаление строки
        if (event.target.closest('.remove-row')) {
            const row = event.target.closest('.estimate-row');
            if (row) {
                row.remove();
                updatePositionNumbers();
                calculateAllTotals();
                markForAutosave();
            }
        }

        // Свернуть/развернуть секцию
        if (event.target.closest('.toggle-section')) {
            const section = event.target.closest('.section');
            const sectionBody = section.querySelector('.section-body');
            const icon = event.target.closest('.toggle-section').querySelector('i');
            
            if (sectionBody.style.display === 'none') {
                sectionBody.style.display = '';
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-down');
            } else {
                sectionBody.style.display = 'none';
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-right');
            }
        }

        // Добавление строки в секцию
        if (event.target.closest('.add-row-to-section')) {
            const section = event.target.closest('.section');
            const sectionId = section.getAttribute('data-section-id');
            addEstimateRow(sectionId);
            markForAutosave();
        }

        // Удаление секции
        if (event.target.closest('.remove-section')) {
            if (confirm('Вы уверены, что хотите удалить этот раздел со всеми позициями?')) {
                const section = event.target.closest('.section');
                section.remove();
                calculateAllTotals();
                markForAutosave();
            }
        }

        // Перемещение секции вверх
        if (event.target.closest('.move-section-up')) {
            event.preventDefault();
            const section = event.target.closest('.section');
            const prevSection = section.previousElementSibling;
            if (prevSection && prevSection.classList.contains('section')) {
                section.parentNode.insertBefore(section, prevSection);
                markForAutosave();
            }
        }

        // Перемещение секции вниз
        if (event.target.closest('.move-section-down')) {
            event.preventDefault();
            const section = event.target.closest('.section');
            const nextSection = section.nextElementSibling;
            if (nextSection && nextSection.classList.contains('section')) {
                section.parentNode.insertBefore(nextSection, section);
                markForAutosave();
            }
        }
    });

    // Расчет значений при изменении числовых полей
    document.addEventListener('input', function(event) {
        if (event.target.closest('.estimate-row')) {
            const row = event.target.closest('.estimate-row');
            calculateRowTotals(row);
            calculateSectionTotals(row.closest('.section'));
            calculateAllTotals();
            // Помечаем, что есть изменения для автосохранения
            markForAutosave();
        }
    });

    // Система автосохранения
    let autosaveTimer = null;
    let hasUnsavedChanges = false;
    let isAutosaving = false;
    
    // Создаем индикатор сохранения
    const saveIndicator = document.createElement('div');
    saveIndicator.id = 'autosave-indicator';
    saveIndicator.className = 'position-fixed top-0 end-0 m-3 p-2 bg-light border rounded shadow-sm';
    saveIndicator.style.zIndex = '9999';
    saveIndicator.innerHTML = '<small class="text-muted"><i class="bi bi-shield-check"></i> Автосохранение включено</small>';
    document.body.appendChild(saveIndicator);

    // Drag & Drop для строк
    let draggedRow = null;
    let draggedSection = null;

    // Обработчики drag & drop
    document.addEventListener('dragstart', function(event) {
        if (event.target.classList.contains('estimate-row')) {
            draggedRow = event.target;
            draggedSection = event.target.closest('.section');
            event.target.style.opacity = '0.5';
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/html', event.target.outerHTML);
        }
    });

    document.addEventListener('dragend', function(event) {
        if (event.target.classList.contains('estimate-row')) {
            event.target.style.opacity = '';
            draggedRow = null;
            draggedSection = null;
        }
    });

    document.addEventListener('dragover', function(event) {
        if (draggedRow && event.target.closest('.estimate-row')) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
            
            const targetRow = event.target.closest('.estimate-row');
            const targetSection = targetRow.closest('.section');
            
            // Подсвечиваем только если это та же секция
            if (targetSection === draggedSection) {
                const rect = targetRow.getBoundingClientRect();
                const midY = rect.top + rect.height / 2;
                
                // Убираем предыдущие классы
                document.querySelectorAll('.drag-over-top, .drag-over-bottom').forEach(el => {
                    el.classList.remove('drag-over-top', 'drag-over-bottom');
                });
                
                if (event.clientY < midY) {
                    targetRow.classList.add('drag-over-top');
                } else {
                    targetRow.classList.add('drag-over-bottom');
                }
            }
        }
    });

    document.addEventListener('dragleave', function(event) {
        if (event.target.closest('.estimate-row')) {
            event.target.closest('.estimate-row').classList.remove('drag-over-top', 'drag-over-bottom');
        }
    });

    document.addEventListener('drop', function(event) {
        if (draggedRow && event.target.closest('.estimate-row')) {
            event.preventDefault();
            
            const targetRow = event.target.closest('.estimate-row');
            const targetSection = targetRow.closest('.section');
            
            // Перемещаем только внутри одной секции
            if (targetSection === draggedSection && targetRow !== draggedRow) {
                const rect = targetRow.getBoundingClientRect();
                const midY = rect.top + rect.height / 2;
                
                if (event.clientY < midY) {
                    targetRow.parentNode.insertBefore(draggedRow, targetRow);
                } else {
                    targetRow.parentNode.insertBefore(draggedRow, targetRow.nextSibling);
                }
                
                // Обновляем номера позиций и итоги
                updatePositionNumbers();
                calculateSectionTotals(targetSection);
                calculateAllTotals();
                markForAutosave();
            }
            
            // Убираем классы подсветки
            document.querySelectorAll('.drag-over-top, .drag-over-bottom').forEach(el => {
                el.classList.remove('drag-over-top', 'drag-over-bottom');
            });
        }
    });
    
    // Функция отметки изменений для автосохранения
    function markForAutosave() {
        hasUnsavedChanges = true;
        if (!isAutosaving) {
            saveIndicator.innerHTML = '<small class="text-warning"><i class="bi bi-pencil-fill"></i> Есть изменения</small>';
        }
    }
    
    // Функция сбора данных сметы для автосохранения
    function collectEstimateData() {
        const data = {
            type: 'main',
            version: '1.0',
            meta: {
                template_name: 'Основная смета работ',
                is_template: false,
                updated_at: new Date().toISOString(),
                description: 'Смета строительных и отделочных работ'
            },
            sections: {},
            totals: {},
            footer: {
                items: []
            }
        };
        
        // Собираем данные секций
        document.querySelectorAll('.section').forEach(section => {
            const sectionId = section.dataset.sectionId;
            const sectionTitleElement = section.querySelector('.section-title');
            const sectionTitle = sectionTitleElement ? sectionTitleElement.value : 'Раздел';
            
            data.sections[sectionId] = {
                id: sectionId,
                title: sectionTitle,
                type: 'section',
                items: {}
            };
            
            // Собираем данные строк секции
            section.querySelectorAll('.estimate-row').forEach(row => {
                const rowId = row.dataset.rowId;
                const nameInput = row.querySelector('input[name*="[name]"]');
                const unitSelect = row.querySelector('select[name*="[unit]"]');
                const quantityInput = row.querySelector('input[name*="[quantity]"]');
                const priceInput = row.querySelector('input[name*="[price]"]');
                const markupInput = row.querySelector('input[name*="[markup]"]');
                const discountInput = row.querySelector('input[name*="[discount]"]');
                const amountInput = row.querySelector('input[name*="[amount]"]');
                const clientPriceInput = row.querySelector('input[name*="[client_price]"]');
                const clientAmountInput = row.querySelector('input[name*="[client_amount]"]');
                
                const rowData = {
                    name: nameInput ? nameInput.value : '',
                    unit: unitSelect ? unitSelect.value : 'шт',
                    quantity: quantityInput ? parseFloat(quantityInput.value) || 0 : 0,
                    price: priceInput ? parseFloat(priceInput.value) || 0 : 0,
                    markup: markupInput ? parseFloat(markupInput.value) || 0 : 0,
                    discount: discountInput ? parseFloat(discountInput.value) || 0 : 0,
                    amount: amountInput ? parseFloat(amountInput.value) || 0 : 0,
                    client_price: clientPriceInput ? parseFloat(clientPriceInput.value) || 0 : 0,
                    client_amount: clientAmountInput ? parseFloat(clientAmountInput.value) || 0 : 0
                };
                
                data.sections[sectionId].items[rowId] = rowData;
            });
        });
        
        // Собираем итоги
        const workCost = document.getElementById('work_cost');
        const materialsCost = document.getElementById('materials_cost');
        const clientTotal = document.getElementById('client_total');
        const markupPercent = document.getElementById('markup_percent');
        const discountPercent = document.getElementById('discount_percent');
        const profitAmount = document.getElementById('profit_amount');
        
        data.totals = {
            work_cost: workCost ? parseFloat(workCost.value) || 0 : 0,
            materials_cost: materialsCost ? parseFloat(materialsCost.value) || 0 : 0,
            client_total: clientTotal ? parseFloat(clientTotal.value) || 0 : 0,
            markup_percent: markupPercent ? parseFloat(markupPercent.value) || 20 : 20,
            discount_percent: discountPercent ? parseFloat(discountPercent.value) || 0 : 0,
            profit_amount: profitAmount ? parseFloat(profitAmount.value) || 0 : 0,
            // Добавляем старые поля для совместимости
            work_total: workCost ? parseFloat(workCost.value) || 0 : 0,
            materials_total: materialsCost ? parseFloat(materialsCost.value) || 0 : 0,
            grand_total: clientTotal ? parseFloat(clientTotal.value) || 0 : 0
        };
        
        return data;
    }
    
    // Функция автосохранения
    function autoSaveEstimate() {
        if (!hasUnsavedChanges || isAutosaving) return;
        
        isAutosaving = true;
        saveIndicator.innerHTML = '<small class="text-info"><i class="bi bi-hourglass-split"></i> Сохранение...</small>';
        
        try {
            const data = collectEstimateData();
            
            fetch(`/partner/estimates/{{ $estimate->id }}/autosave`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ data: data })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    saveIndicator.innerHTML = '<small class="text-success"><i class="bi bi-check-circle-fill"></i> Сохранено ' + data.timestamp + '</small>';
                    hasUnsavedChanges = false;
                    
                    // Через 3 секунды показываем статус готовности
                    setTimeout(() => {
                        if (!hasUnsavedChanges) {
                            saveIndicator.innerHTML = '<small class="text-muted"><i class="bi bi-shield-check"></i> Автосохранение включено</small>';
                        }
                    }, 3000);
                } else {
                    saveIndicator.innerHTML = '<small class="text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Ошибка сохранения</small>';
                    console.error('Ошибка автосохранения:', data.message);
                }
            })
            .catch(error => {
                saveIndicator.innerHTML = '<small class="text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Ошибка сохранения</small>';
                console.error('Ошибка автосохранения:', error);
            })
            .finally(() => {
                isAutosaving = false;
            });
        } catch (error) {
            console.error('Ошибка при сборе данных для автосохранения:', error);
            saveIndicator.innerHTML = '<small class="text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Ошибка сохранения</small>';
            isAutosaving = false;
        }
    }
    
    // Запуск автосохранения каждые 5 секунд
    autosaveTimer = setInterval(autoSaveEstimate, 5000);
    
    // Отслеживаем изменения в форме
    document.getElementById('estimateForm').addEventListener('input', markForAutosave);
    document.getElementById('estimateForm').addEventListener('change', markForAutosave);
    
    // Отслеживаем изменения в динамически добавляемых элементах
    document.addEventListener('input', function(e) {
        if (e.target.closest('.estimate-row') || e.target.closest('.section')) {
            markForAutosave();
        }
    });
    
    document.addEventListener('change', function(e) {
        if (e.target.closest('.estimate-row') || e.target.closest('.section')) {
            markForAutosave();
        }
    });
    
    // Сохранение при потере фокуса страницы
    document.addEventListener('visibilitychange', function() {
        if (document.hidden && hasUnsavedChanges) {
            autoSaveEstimate();
        }
    });
    
    // Сохранение при закрытии страницы
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            autoSaveEstimate();
        }
    });
    
    // Очищаем интервал при закрытии страницы
    window.addEventListener('unload', function() {
        if (autosaveTimer) {
            clearInterval(autosaveTimer);
        }
    });

    // Функция для загрузки данных сметы при редактировании
    function loadEstimateData(data) {
        console.log("=== НАЧАЛО ЗАГРУЗКИ ДАННЫХ СМЕТЫ ===");
        console.log("Исходные данные:", data);
        
        const editorContainer = document.getElementById('estimate-editor');
        if (!editorContainer) {
            console.error("ОШИБКА: Не найден контейнер редактора");
            return;
        }
        
        if (!data || !data.sections) {
            console.error("ОШИБКА: Нет данных или секций в данных сметы");
            return;
        }

        // Очищаем контейнер
        editorContainer.innerHTML = '';

        console.log("Тип data.sections:", typeof data.sections);
        console.log("Является ли data.sections массивом:", Array.isArray(data.sections));
        console.log("Ключи data.sections:", Object.keys(data.sections));

        // Проверяем, является ли data.sections объектом или массивом
        if (Array.isArray(data.sections)) {
            console.log("Обрабатываем sections как массив");
            // Если массив, обрабатываем как было раньше
            data.sections.forEach((section, index) => {
                // Проверяем наличие обязательных полей в секции
                if (!section) {
                    console.warn(`Пропуск пустой секции ${index}`);
                    return;
                }
                
                // Проверяем и устанавливаем ID и заголовок секции
                const sectionId = section.id || `section_${index}_${Date.now()}`;
                const sectionTitle = section.title || `Раздел ${index + 1}`;
                
                const sectionElement = addEstimateSection(sectionTitle, sectionId, false);
                
                // Добавляем все строки в секцию
                if (section.items && Array.isArray(section.items) && section.items.length > 0) {
                    section.items.forEach(item => {
                        if (item) {
                            addEstimateRow(sectionId, item, false);
                        }
                    });
                }
            });
        } else {
            console.log("Обрабатываем sections как объект");
            // Если объект, обрабатываем как объект с ключами
            let index = 0;
            
            // Проверяем, что data.sections - это объект
            if (typeof data.sections === 'object' && data.sections !== null) {
                for (const sectionId in data.sections) {
                    const section = data.sections[sectionId];
                    
                    console.log(`=== ОБРАБОТКА СЕКЦИИ ${sectionId} ===`);
                    console.log("Данные секции:", section);
                    
                    // Проверяем наличие обязательных полей в секции
                    if (!section) {
                        console.warn(`Пропуск пустой секции ${sectionId}`);
                        continue;
                    }
                    
                    // Используем sectionId из ключа, а если у секции есть своё поле id и оно не массив и не пустое, то берем его
                    const actualSectionId = (section.id && typeof section.id === 'string' && section.id.length > 0) ? section.id : sectionId;
                    
                    // Логируем для отладки
                    console.log(`Секция ${sectionId} - ID из секции:`, section.id, 'используем:', actualSectionId);
                    
                    // Используем заголовок секции или генерируем по индексу/ID
                    let sectionTitle;
                    
                    // Проверяем, является ли title строкой и не пустой
                    if (section.title && typeof section.title === 'string' && section.title.trim().length > 0) {
                        sectionTitle = section.title;
                        console.log(`Используем title из данных: ${sectionTitle}`);
                    } 
                    // Иначе формируем заголовок на основе sectionId
                    else {
                        // Преобразуем ID в читабельное название (demo_1 -> Demo 1)
                        if (sectionId) {
                            // Удаляем цифры из префикса ID и форматируем строку
                            const sectionName = sectionId.split('_')[0] || '';
                            const sectionNumber = sectionId.split('_')[1] || '';
                            
                            // Форматируем название секции
                            if (sectionName) {
                                const formattedName = sectionName.charAt(0).toUpperCase() + sectionName.slice(1);
                                sectionTitle = formattedName + (sectionNumber ? ' ' + sectionNumber : '');
                            } else {
                                sectionTitle = `Раздел ${index + 1}`;
                            }
                        } else {
                            sectionTitle = `Раздел ${index + 1}`;
                        }
                        console.log(`Сгенерировали title: ${sectionTitle} на основе ID: ${sectionId}`);
                    }
                    
                    const sectionElement = addEstimateSection(sectionTitle, actualSectionId, false);
                    
                    // Добавляем все строки в секцию
                    if (section.items) {
                        console.log("Тип section.items:", typeof section.items);
                        console.log("section.items:", section.items);
                        
                        if (Array.isArray(section.items)) {
                            console.log("Обрабатываем items как массив");
                            section.items.forEach(item => {
                                if (item) {
                                    addEstimateRow(actualSectionId, item, false);
                                }
                            });
                        } else if (typeof section.items === 'object') {
                            console.log("Обрабатываем items как объект");
                            // Если items - это объект, перебираем его ключи
                            for (const rowId in section.items) {
                                try {
                                    console.log(`=== ОБРАБОТКА СТРОКИ ${rowId} ===`);
                                    console.log("Данные строки:", section.items[rowId]);
                                    
                                    let itemData = section.items[rowId];
                                    
                                    // Проверяем, является ли itemData объектом с данными строки
                                    if (typeof itemData === 'object' && itemData !== null) {
                                        console.log("Данные строки уже в правильном формате");
                                        // Данные уже в правильном формате
                                        const rowData = {
                                            id: rowId,
                                            name: itemData.name || '',
                                            unit: itemData.unit || 'шт',
                                            quantity: parseFloat(itemData.quantity) || 0,
                                            price: parseFloat(itemData.price) || 0,
                                            markup: parseFloat(itemData.markup) || 20,
                                            discount: parseFloat(itemData.discount) || 0,
                                            amount: parseFloat(itemData.amount) || 0,
                                            client_price: parseFloat(itemData.client_price) || 0,
                                            client_amount: parseFloat(itemData.client_amount) || 0
                                        };
                                        
                                        console.log("Создаем строку с данными:", rowData);
                                        addEstimateRow(actualSectionId, rowData, false);
                                    } else {
                                        console.log("Обрабатываем данные строки в старом формате");
                                        // Обработка для старого формата или числовых значений
                                        let parsedData = null;
                                        
                                        if (typeof itemData === 'string') {
                                            try {
                                                // Пробуем распарсить как JSON
                                                parsedData = JSON.parse(itemData);
                                                console.log(`Успешно распарсили JSON для строки ${rowId}:`, parsedData);
                                            } catch (e) {
                                                // Если не получилось, используем как есть
                                                console.log(`Не удалось распарсить JSON для строки ${rowId}, используем как цену:`, itemData);
                                                parsedData = null;
                                            }
                                        }
                                        
                                        // Пытаемся определить название товара/работы по ID строки
                                        let itemName = '';
                                        // Убираем технический префикс row_TIMESTAMP_INDEX_RANDOM
                                        const nameMatch = rowId.match(/^row_\d+_\d+_\d+$/);
                                        if (nameMatch) {
                                            // Если ID имеет только технический формат, используем пустое название
                                            itemName = '';
                                        } else {
                                            // Иначе пытаемся извлечь название из ID
                                            itemName = rowId.replace(/^row_\d+_\d+_\d+_/, '').replace(/_/g, ' ').trim();
                                            if (!itemName) {
                                                itemName = rowId.replace(/_/g, ' ').replace(/^\w/, c => c.toUpperCase());
                                            }
                                        }
                                        
                                        // Определяем цену из значения в JSON
                                        let price = 0;
                                        if (typeof itemData === 'string') {
                                            price = parseFloat(itemData) || 0;
                                        } else if (typeof itemData === 'number') {
                                            price = itemData;
                                        }
                                        
                                        console.log(`Строка ${rowId}: название="${itemName}", цена=${price}`);
                                        
                                        // Создаем объект с данными строки
                                        const rowData = {
                                            id: rowId,
                                            name: parsedData?.name || itemName,
                                            unit: parsedData?.unit || 'шт',
                                            quantity: parsedData?.quantity || 0,
                                            price: parsedData?.price || price,
                                            markup: parsedData?.markup || 20,
                                            discount: parsedData?.discount || 0
                                        };
                                        
                                        addEstimateRow(actualSectionId, rowData, false);
                                    }
                                } catch (error) {
                                    console.error(`Ошибка при обработке строки ${rowId}:`, error);
                                }
                            }
                        }
                    }
                    
                    index++;
                }
            } else {
                console.error("data.sections не является объектом или массивом:", data.sections);
            }
        }

        // Обновляем номера позиций и рассчитываем итоги
        updatePositionNumbers();
        calculateAllTotals();
        
        console.log("=== КОНЕЦ ЗАГРУЗКИ ДАННЫХ СМЕТЫ ===");
        // Отладочная информация
        console.log("Смета загружена, секций:", document.querySelectorAll('.section').length);
        
        // Прокручиваем к верху страницы после загрузки
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Функция добавления новой секции сметы
    function addEstimateSection(title, sectionId = null, focus = true) {
        const editorContainer = document.getElementById('estimate-editor');
        if (!editorContainer) return;

        // Генерируем ID секции если не предоставлен
        if (!sectionId) {
            sectionId = 'section_' + Date.now();
        }
        
        console.log(`Добавление секции: ${sectionId}, заголовок: ${title}`);

        // Создаем секцию по шаблону
        const template = document.getElementById('estimate-section-template').innerHTML;
        let sectionHtml = template
            .replace(/{section_id}/g, sectionId)
            .replace(/{section_title}/g, title || sectionId.replace(/_/g, ' ').replace(/^\w/, c => c.toUpperCase()))
            .replace(/{section_total}/g, '0.00')
            .replace(/{section_client_total}/g, '0.00');

        // Добавляем в контейнер
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = sectionHtml;
        const sectionElement = tempDiv.firstElementChild;
        editorContainer.appendChild(sectionElement);

        // Фокусируемся на заголовке если нужно
        if (focus) {
            const titleInput = sectionElement.querySelector('.section-title');
            titleInput.focus();
        }

        return sectionElement;
    }

    // Функция добавления новой строки в смету
    function addEstimateRow(sectionId, data = null, focus = true) {
        const section = document.querySelector(`.section[data-section-id="${sectionId}"]`);
        if (!section) return;

        const sectionItems = section.querySelector('.section-items');
        const rowCount = sectionItems.querySelectorAll('.estimate-row').length;
        // Используем rowId из переданных данных или генерируем новый
        const rowId = (data && data.id) ? data.id : 'row_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
        
        // Данные по умолчанию
        const defaultData = {
            name: '',
            unit: 'шт',
            quantity: 0,
            price: 0,
            markup: 20,
            discount: 0
        };
        
        // Объединяем с переданными данными
        const rowData = {...defaultData, ...data};
        
        // Рассчитываем значения
        const amount = (parseFloat(rowData.quantity) || 0) * (parseFloat(rowData.price) || 0);
        const clientPrice = (parseFloat(rowData.price) || 0) * 
            (1 + (parseFloat(rowData.markup) || 0) / 100) * 
            (1 - (parseFloat(rowData.discount) || 0) / 100);
        const clientAmount = (parseFloat(rowData.quantity) || 0) * clientPrice;

        // Создаем строку по шаблону
        const template = document.getElementById('estimate-row-template').innerHTML;
        let rowHtml = template
            .replace(/{row_id}/g, rowId)
            .replace(/{section_id}/g, sectionId)
            .replace(/{position}/g, rowCount + 1)
            .replace(/{name}/g, rowData.name || '')
            .replace(/{quantity}/g, rowData.quantity || 0)
            .replace(/{price}/g, rowData.price || 0)
            .replace(/{amount}/g, amount.toFixed(2))
            .replace(/{markup}/g, rowData.markup || 20)
            .replace(/{discount}/g, rowData.discount || 0)
            .replace(/{client_price}/g, clientPrice.toFixed(2))
            .replace(/{client_amount}/g, clientAmount.toFixed(2))
            .replace(/{unit_m2}/g, rowData.unit === 'м²' ? 'selected' : '')
            .replace(/{unit_m_p}/g, rowData.unit === 'м.п.' ? 'selected' : '')
            .replace(/{unit_sht}/g, rowData.unit === 'шт' ? 'selected' : '')
            .replace(/{unit_compl}/g, rowData.unit === 'компл.' ? 'selected' : '')
            .replace(/{unit_kg}/g, rowData.unit === 'кг' ? 'selected' : '')
            .replace(/{unit_l}/g, rowData.unit === 'л' ? 'selected' : '')
            .replace(/{unit_m3}/g, rowData.unit === 'м³' ? 'selected' : '')
            .replace(/{unit_m}/g, rowData.unit === 'м' ? 'selected' : '')
            .replace(/{unit_upak}/g, rowData.unit === 'упак' ? 'selected' : '');
            
        // Устанавливаем выбранную единицу измерения
        const unitOptions = {
            'м²': 'unit_m2',
            'м.п.': 'unit_m_p',
            'шт': 'unit_sht',
            'компл.': 'unit_compl',
            'кг': 'unit_kg',
            'л': 'unit_l',
            'м³': 'unit_m3',
            'м': 'unit_m',
            'упак': 'unit_upak'
        };
        
        // Заменяем параметры для выбора единицы измерения
        Object.keys(unitOptions).forEach(unit => {
            const placeholder = `{${unitOptions[unit]}}`;
            rowHtml = rowHtml.replace(placeholder, unit === rowData.unit ? 'selected' : '');
        });
            
        // Добавляем строку в секцию
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = `<table><tbody>${rowHtml}</tbody></table>`;
        const rowElement = tempDiv.querySelector('tr');
        sectionItems.appendChild(rowElement);
        
        // Добавляем обработчики drag & drop к новой строке
        rowElement.addEventListener('dragstart', function(event) {
            draggedRow = event.target;
            draggedSection = event.target.closest('.section');
            event.target.style.opacity = '0.5';
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/html', event.target.outerHTML);
        });
        
        rowElement.addEventListener('dragend', function(event) {
            event.target.style.opacity = '';
            draggedRow = null;
            draggedSection = null;
        });
        
        // Фокусируемся на названии только если это необходимо
        if (focus) {
            rowElement.querySelector('input[type="text"]').focus();
        }
        
        // Обновляем итоги
        calculateSectionTotals(section);
        
        return rowElement;
    }

    // Функция для расчета итогов по строке
    function calculateRowTotals(row) {
        try {
            // Проверяем наличие всех элементов
            const quantityInput = row.querySelector('.quantity');
            const priceInput = row.querySelector('.price');
            const markupInput = row.querySelector('.markup');
            const discountInput = row.querySelector('.discount');
            
            if (!quantityInput || !priceInput || !markupInput || !discountInput) {
                console.error('Ошибка: Не найдены все необходимые элементы в строке', row);
                return;
            }
            
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const markup = parseFloat(markupInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            
            // Расчет сумм
            const amount = quantity * price;
            const clientPrice = price * (1 + markup/100) * (1 - discount/100);
            const clientAmount = quantity * clientPrice;
            
            // Проверяем и обновляем поля
            const amountSpan = row.querySelector('.amount');
            const amountInput = row.querySelector('input[name*="[amount]"]');
            const clientPriceSpan = row.querySelector('.client-price');
            const clientPriceInput = row.querySelector('input[name*="[client_price]"]');
            const clientAmountSpan = row.querySelector('.client-amount');
            const clientAmountInput = row.querySelector('input[name*="[client_amount]"]');
            
            if (!amountSpan || !amountInput || !clientPriceSpan || !clientPriceInput || !clientAmountSpan || !clientAmountInput) {
                console.error('Ошибка: Не найдены поля для вывода результатов в строке', row);
                return;
            }
            
            // Обновление полей
            amountSpan.textContent = amount.toFixed(2);
            amountInput.value = amount.toFixed(2);
            
            clientPriceSpan.textContent = clientPrice.toFixed(2);
            clientPriceInput.value = clientPrice.toFixed(2);
            
            clientAmountSpan.textContent = clientAmount.toFixed(2);
            clientAmountInput.value = clientAmount.toFixed(2);
        } catch (error) {
            console.error('Ошибка при расчете итогов по строке:', error, row);
        }
    }

    // Функция для расчета итогов по секции
    function calculateSectionTotals(section) {
        if (!section) return;
        
        let sectionTotal = 0;
        let sectionClientTotal = 0;
        
        // Суммируем все строки в секции
        section.querySelectorAll('.estimate-row').forEach(row => {
            sectionTotal += parseFloat(row.querySelector('input[name*="[amount]"]').value) || 0;
            sectionClientTotal += parseFloat(row.querySelector('input[name*="[client_amount]"]').value) || 0;
        });
        
        // Обновляем итоги в подвале секции
        section.querySelector('.section-total').textContent = sectionTotal.toFixed(2);
        section.querySelector('.section-client-total').textContent = sectionClientTotal.toFixed(2);
        
        return {
            total: sectionTotal,
            clientTotal: sectionClientTotal
        };
    }

    // Функция для расчета общих итогов по всем секциям
    function calculateAllTotals() {
        let workCost = 0;      // Стоимость работ (цена × количество)
        let materialsCost = 0; // Стоимость материалов (цена × количество)
        let workClientTotal = 0;      // Итого для клиента по работам
        let materialsClientTotal = 0; // Итого для клиента по материалам
        
        // Считаем итоги по всем секциям
        document.querySelectorAll('.section').forEach(section => {
            const sectionTotals = calculateSectionTotals(section);
            const sectionId = section.getAttribute('data-section-id');
            
            // Определяем тип секции (работы или материалы)
            if (sectionId && sectionId.includes('materials')) {
                materialsCost += sectionTotals.total;        // Стоимость материалов
                materialsClientTotal += sectionTotals.clientTotal; // Итого для клиента по материалам
            } else {
                workCost += sectionTotals.total;             // Стоимость работ
                workClientTotal += sectionTotals.clientTotal; // Итого для клиента по работам
            }
        });
        
        // Общий итог для клиента
        const clientTotal = workClientTotal + materialsClientTotal;
        
        // Рассчитываем выгоду (разница между клиентской ценой и себестоимостью)
        const baseCost = workCost + materialsCost;
        const profitAmount = clientTotal - baseCost;
        const profitPercent = baseCost > 0 ? (profitAmount / baseCost) * 100 : 0;
        
        // Обновляем итоговые значения
        document.getElementById('work_cost').value = workCost.toFixed(2);
        document.getElementById('work_cost_display').textContent = formatCurrency(workCost);
        
        document.getElementById('materials_cost').value = materialsCost.toFixed(2);
        document.getElementById('materials_cost_display').textContent = formatCurrency(materialsCost);
        
        // Обновляем выгоду
        document.getElementById('profit_amount').value = profitAmount.toFixed(2);
        document.getElementById('profit_display').textContent = formatCurrency(profitAmount);
        document.getElementById('profit_percent_display').textContent = profitPercent.toFixed(1) + '%';
        
        document.getElementById('client_total').value = clientTotal.toFixed(2);
        document.getElementById('client_total_display').textContent = formatCurrency(clientTotal);
    }

    // Функция для обновления номеров позиций
    function updatePositionNumbers() {
        document.querySelectorAll('.section').forEach(section => {
            const rows = section.querySelectorAll('.estimate-row');
            rows.forEach((row, index) => {
                row.querySelector('.position-number').textContent = index + 1;
            });
        });
    }

    // Функция для отображения модального окна создания раздела
    function showNewSectionModal() {
        const modal = new bootstrap.Modal(document.getElementById('newSectionModal'));
        modal.show();
    }

    // Форматирование валюты
    function formatCurrency(value) {
        return new Intl.NumberFormat('ru-RU', { 
            style: 'currency', 
            currency: 'RUB',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    }

    // Валидация формы перед отправкой
    document.getElementById('estimateForm').addEventListener('submit', function(e) {
        // Убедимся, что все расчеты выполнены
        calculateAllTotals();
        
        // Проверим наличие секций с позициями
        const sections = document.querySelectorAll('.section');
        let hasItems = false;
        
        sections.forEach(section => {
            if (section.querySelectorAll('.estimate-row').length > 0) {
                hasItems = true;
            }
        });
        
        if (sections.length === 0 || !hasItems) {
            e.preventDefault();
            alert('Смета должна содержать хотя бы один раздел с позициями');
        }
    });
    
    console.log('Автосохранение инициализировано для сметы {{ $estimate->id }}');
    
    // Функции экспорта
    window.exportToPDF = function(type = 'master') {
        const estimateId = {{ $estimate->id }};
        const estimateData = collectEstimateData();
        
        // Показываем индикатор загрузки
        const exportBtn = document.getElementById('exportDropdown');
        const originalText = exportBtn.innerHTML;
        exportBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Экспорт...';
        exportBtn.disabled = true;
        
        fetch(`/partner/estimates/${estimateId}/export-pdf`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ data: estimateData, type: type })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Ошибка при экспорте PDF');
            }
            return response.blob();
        })
        .then(blob => {
            // Создаем ссылку для скачивания
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `smeta_${estimateId}_${type}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        })
        .catch(error => {
            console.error('Ошибка экспорта PDF:', error);
            alert('Ошибка при экспорте в PDF');
        })
        .finally(() => {
            exportBtn.innerHTML = originalText;
            exportBtn.disabled = false;
        });
    };
    
    window.exportToExcel = function(type = 'master') {
        const estimateId = {{ $estimate->id }};
        const estimateData = collectEstimateData();
        
        // Показываем индикатор загрузки
        const exportBtn = document.getElementById('exportDropdown');
        const originalText = exportBtn.innerHTML;
        exportBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Экспорт...';
        exportBtn.disabled = true;
        
        fetch(`/partner/estimates/${estimateId}/export-excel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ data: estimateData, type: type })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Ошибка при экспорте Excel');
            }
            return response.blob();
        })
        .then(blob => {
            // Создаем ссылку для скачивания
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `smeta_${estimateId}_${type}.xlsx`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        })
        .catch(error => {
            console.error('Ошибка экспорта Excel:', error);
            alert('Ошибка при экспорте в Excel');
        })
        .finally(() => {
            exportBtn.innerHTML = originalText;
            exportBtn.disabled = false;
        });
    };
});

// Функции экспорта
function exportToPDF() {
    const estimateId = {{ $estimate->id }};
    
    // Собираем данные только заполненных строк
    const filledRows = collectFilledRows();
    
    if (filledRows.length === 0) {
        alert('Нет заполненных строк для экспорта');
        return;
    }
    
    // Создаем форму для отправки данных
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/partner/estimates/' + estimateId + '/export/pdf';
    form.target = '_blank';
    
    // Добавляем CSRF токен
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    // Добавляем данные заполненных строк
    const dataInput = document.createElement('input');
    dataInput.type = 'hidden';
    dataInput.name = 'filled_rows';
    dataInput.value = JSON.stringify(filledRows);
    form.appendChild(dataInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function exportToExcel() {
    const estimateId = {{ $estimate->id }};
    
    // Собираем данные только заполненных строк
    const filledRows = collectFilledRows();
    
    if (filledRows.length === 0) {
        alert('Нет заполненных строк для экспорта');
        return;
    }
    
    // Создаем форму для отправки данных
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/partner/estimates/' + estimateId + '/export/excel';
    form.target = '_blank';
    
    // Добавляем CSRF токен
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    // Добавляем данные заполненных строк
    const dataInput = document.createElement('input');
    dataInput.type = 'hidden';
    dataInput.name = 'filled_rows';
    dataInput.value = JSON.stringify(filledRows);
    form.appendChild(dataInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function collectFilledRows() {
    const filledRows = [];
    const sections = document.querySelectorAll('.section');
    
    sections.forEach(section => {
        const sectionName = section.querySelector('.section-name').textContent.trim();
        const sectionRows = [];
        
        const rows = section.querySelectorAll('.estimate-row');
        rows.forEach(row => {
            const nameInput = row.querySelector('.row-name');
            const unitInput = row.querySelector('.row-unit');
            const quantityInput = row.querySelector('.row-quantity');
            const priceInput = row.querySelector('.row-price');
            
            // Проверяем, заполнена ли строка
            if (nameInput && nameInput.value.trim() !== '') {
                sectionRows.push({
                    name: nameInput.value.trim(),
                    unit: unitInput ? unitInput.value.trim() : '',
                    quantity: quantityInput ? parseFloat(quantityInput.value) || 0 : 0,
                    price: priceInput ? parseFloat(priceInput.value) || 0 : 0,
                    total: (quantityInput && priceInput) ? 
                        (parseFloat(quantityInput.value) || 0) * (parseFloat(priceInput.value) || 0) : 0
                });
            }
        });
        
        if (sectionRows.length > 0) {
            filledRows.push({
                section: sectionName,
                rows: sectionRows
            });
        }
    });
    
    return filledRows;
}

// Функции для экспорта в PDF
function exportToPDF(type) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("partner.estimates.export-pdf", $estimate->id) }}';
    form.style.display = 'none';
    
    // Добавляем CSRF токен
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    // Добавляем тип экспорта
    const typeInput = document.createElement('input');
    typeInput.type = 'hidden';
    typeInput.name = 'type';
    typeInput.value = type;
    form.appendChild(typeInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Функции для экспорта в Excel
function exportToExcel(type) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("partner.estimates.export-excel", $estimate->id) }}';
    form.style.display = 'none';
    
    // Добавляем CSRF токен
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    // Добавляем тип экспорта
    const typeInput = document.createElement('input');
    typeInput.type = 'hidden';
    typeInput.name = 'type';
    typeInput.value = type;
    form.appendChild(typeInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Функция для загрузки PDF для клиента
function downloadForClient() {
    // Показываем индикатор загрузки
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Генерация PDF...';
    btn.disabled = true;
    
    // Создаем скрытую форму для отправки POST запроса
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("partner.estimates.export-pdf", $estimate->id) }}';
    form.style.display = 'none';
    
    // Добавляем CSRF токен
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    // Добавляем тип экспорта для клиента
    const typeInput = document.createElement('input');
    typeInput.type = 'hidden';
    typeInput.name = 'type';
    typeInput.value = 'client';
    form.appendChild(typeInput);
    
    // Добавляем флаг для загрузки клиенту
    const clientDownloadInput = document.createElement('input');
    clientDownloadInput.type = 'hidden';
    clientDownloadInput.name = 'client_download';
    clientDownloadInput.value = '1';
    form.appendChild(clientDownloadInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Возвращаем исходный вид кнопки через 2 секунды
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 2000);
}

// Функция для открытия модального окна сохранения шаблона
function openSaveTemplateModal() {
    const modal = new bootstrap.Modal(document.getElementById('saveTemplateModal'));
    modal.show();
}

// Обработчик сохранения шаблона
document.getElementById('saveTemplateBtn').addEventListener('click', function() {
    const templateName = document.getElementById('templateName').value.trim();
    const templateDescription = document.getElementById('templateDescription').value.trim();
    
    if (!templateName) {
        alert('Пожалуйста, введите название шаблона');
        return;
    }
    
    // Показываем индикатор загрузки
    const btn = this;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Сохранение...';
    btn.disabled = true;
    
    // Отправляем запрос на сохранение шаблона
    fetch(`/partner/estimates/{{ $estimate->id }}/save-template`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            template_name: templateName,
            template_description: templateDescription
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Закрываем модальное окно
            const modal = bootstrap.Modal.getInstance(document.getElementById('saveTemplateModal'));
            modal.hide();
            
            // Очищаем поля
            document.getElementById('templateName').value = '';
            document.getElementById('templateDescription').value = '';
            
            // Показываем уведомление об успехе
            showAlert('success', 'Шаблон успешно сохранен!');
        } else {
            showAlert('error', data.message || 'Ошибка при сохранении шаблона');
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
        showAlert('error', 'Произошла ошибка при сохранении шаблона');
    })
    .finally(() => {
        // Возвращаем исходный вид кнопки
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
});

// Функция для показа уведомлений
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Добавляем уведомление в начало контейнера
    const container = document.querySelector('.container-fluid .row .col-md-12');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Автоматически скрываем через 5 секунд
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Функция переключения полноэкранного режима
function toggleFullscreenMode() {
    const estimateCard = document.querySelector('.card.mb-4');
    const toggleBtn = document.getElementById('toggleFullscreen');
    const toggleIcon = toggleBtn.querySelector('i');
    const navbar = document.querySelector('.navbar');
    const sidebar = document.querySelector('.sidebar-wrapper');
    const contentWrapper = document.querySelector('.content-wrapper');
    
    if (estimateCard.classList.contains('fullscreen-mode')) {
        // Выход из полноэкранного режима
        estimateCard.classList.remove('fullscreen-mode');
        document.body.classList.remove('fullscreen-estimate');
        
        // Показываем элементы интерфейса
        if (navbar) navbar.style.display = '';
        if (sidebar) sidebar.style.display = '';
        if (contentWrapper) {
            contentWrapper.style.paddingLeft = '';
            contentWrapper.style.paddingTop = '';
        }
        
        // Меняем иконку и текст кнопки
        toggleIcon.className = 'bi bi-arrows-fullscreen me-1';
        toggleBtn.innerHTML = '<i class="bi bi-arrows-fullscreen me-1"></i> Полный экран';
        toggleBtn.title = 'Полноэкранный режим';
        
        // Восстанавливаем прокрутку документа
        document.body.style.overflow = '';
        
    } else {
        // Включение полноэкранного режима
        estimateCard.classList.add('fullscreen-mode');
        document.body.classList.add('fullscreen-estimate');
        
        // Скрываем элементы интерфейса
        if (navbar) navbar.style.display = 'none';
        if (sidebar) sidebar.style.display = 'none';
        if (contentWrapper) {
            contentWrapper.style.paddingLeft = '0';
            contentWrapper.style.paddingTop = '0';
        }
        
        // Меняем иконку и текст кнопки
        toggleIcon.className = 'bi bi-arrows-angle-contract me-1';
        toggleBtn.innerHTML = '<i class="bi bi-arrows-angle-contract me-1"></i> Выйти';
        toggleBtn.title = 'Выйти из полноэкранного режима';
        
        // Отключаем прокрутку документа (будет прокручиваться только содержимое)
        document.body.style.overflow = 'hidden';
    }
}

// Обработчик клавиши Escape для выхода из полноэкранного режима
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const estimateCard = document.querySelector('.card.mb-4');
        if (estimateCard && estimateCard.classList.contains('fullscreen-mode')) {
            toggleFullscreenMode();
        }
    }
});
</script>

<style>
.form-label.required::after {
    content: " *";
    color: #dc3545;
}

.position-number {
    background-color: #f8f9fa;
    font-weight: bold;
}

.section {
    background-color: #fff;
    border-radius: 0.25rem;
}

.section-header {
    border-radius: 0.25rem 0.25rem 0 0;
}

.estimate-row:hover {
    background-color: #f8f9fa;
}

.table th {
    font-weight: 600;
    border-top: none;
}

.estimate-editor input,
.estimate-editor select {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.section-total,
.section-client-total {
    font-weight: bold;
}

#estimate-editor .table {
    margin-bottom: 0;
}

#estimate-editor .table td {
    vertical-align: middle;
}

.form-control-plaintext {
    display: block;
    width: 100%;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    text-align: right;
}



/* Стили для drag & drop */
.estimate-row[draggable="true"] {
    cursor: move;
}

.estimate-row.drag-over-top {
    border-top: 3px solid #007bff;
}

.estimate-row.drag-over-bottom {
    border-bottom: 3px solid #007bff;
}

.estimate-row:hover .drag-handle {
    opacity: 1;
}

.drag-handle {
    opacity: 0.5;
    cursor: grab;
    transition: opacity 0.2s;
}

.drag-handle:hover {
    opacity: 1;
}

.drag-handle:active {
    cursor: grabbing;
}

/* Стили для выпадающих меню */
.dropdown-menu {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.dropdown-item {
    padding: 0.375rem 1rem;
    font-size: 0.875rem;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item.text-danger:hover {
    background-color: #f5c6cb;
    color: #721c24;
}

/* Стили для кнопки загрузки для клиента */
.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}

.btn-primary:disabled {
    background-color: #6c757d;
    border-color: #6c757d;
    opacity: 0.65;
}

/* Анимация для drag and drop */
.drag-over-top {
    border-top: 2px solid #0d6efd;
}

.drag-over-bottom {
    border-bottom: 2px solid #0d6efd;
}

/* Стили для полноэкранного режима */
.fullscreen-mode {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 9999 !important;
    margin: 0 !important;
    border-radius: 0 !important;
    border: none !important;
    box-shadow: none !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
}

.fullscreen-mode .card-body {
    flex: 1 !important;
    overflow: auto !important;
    padding: 0 !important;
}

.fullscreen-mode .estimate-editor {
    height: calc(100vh - 200px) !important;
    overflow-y: auto !important;
}

.fullscreen-mode .card-header {
    flex-shrink: 0 !important;
    padding: 15px 20px !important;
    background-color: #f8f9fa !important;
    border-bottom: 2px solid #dee2e6 !important;
}

.fullscreen-mode .p-3.bg-light {
    flex-shrink: 0 !important;
    background-color: #f8f9fa !important;
    border-top: 1px solid #dee2e6 !important;
}

/* Для body в полноэкранном режиме */
body.fullscreen-estimate {
    overflow: hidden !important;
}

/* Анимация переходов */
.card.mb-4 {
    transition: all 0.3s ease-in-out;
}

/* Кнопка полноэкранного режима */
#toggleFullscreen {
    transition: all 0.2s ease;
}

#toggleFullscreen:hover {
    transform: scale(1.05);
}

/* Скрытие полос прокрутки в полноэкранном режиме для более чистого вида */
.fullscreen-mode .estimate-editor::-webkit-scrollbar {
    width: 8px;
}

.fullscreen-mode .estimate-editor::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.fullscreen-mode .estimate-editor::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.fullscreen-mode .estimate-editor::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Адаптивность для мобильных устройств в полноэкранном режиме */
@media (max-width: 768px) {
    .fullscreen-mode .card-header .d-flex {
        flex-direction: column !important;
        gap: 10px !important;
    }
    
    .fullscreen-mode .card-header .btn-group {
        justify-content: center !important;
    }
    
    .fullscreen-mode .estimate-editor {
        height: calc(100vh - 250px) !important;
    }
}
</style>
@endsection
                                    