@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-calculator-fill text-primary me-2"></i>
                    Создать смету
                </h1>
                
                <a href="{{ route('employee.estimates.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад к сметам
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('employee.estimates.store') }}">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="project_id" class="form-label">Проект <span class="text-danger">*</span></label>
                                <select class="form-select @error('project_id') is-invalid @enderror" 
                                        id="project_id" name="project_id" required>
                                    <option value="">Выберите проект</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->client_name ?? 'Проект #' . $project->id }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="type" class="form-label">Тип сметы <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Выберите тип</option>
                                    <option value="work" {{ old('type') === 'work' ? 'selected' : '' }}>Работы</option>
                                    <option value="material" {{ old('type') === 'material' ? 'selected' : '' }}>Материалы</option>
                                    <option value="transport" {{ old('type') === 'transport' ? 'selected' : '' }}>Транспорт</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Название сметы <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Описание</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Позиции сметы -->
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Позиции сметы</h5>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">
                                        <i class="bi bi-plus"></i> Добавить позицию
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="items-container">
                                    <!-- Шаблон позиции будет добавлен через JavaScript -->
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-6 offset-md-6">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td class="fw-bold">Итого:</td>
                                                    <td class="text-end fw-bold" id="total-amount">0 ₽</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg"></i> Создать смету
                                    </button>
                                    <a href="{{ route('employee.estimates.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg"></i> Отменить
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Шаблон позиции -->
<template id="item-template">
    <div class="item-row border rounded p-3 mb-3">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Наименование <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="items[{index}][name]" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Количество <span class="text-danger">*</span></label>
                <input type="number" class="form-control quantity-input" step="0.01" min="0" 
                       name="items[{index}][quantity]" required onchange="calculateItemTotal(this)">
            </div>
            <div class="col-md-2">
                <label class="form-label">Единица</label>
                <input type="text" class="form-control" name="items[{index}][unit]" placeholder="шт, м², кг">
            </div>
            <div class="col-md-2">
                <label class="form-label">Цена <span class="text-danger">*</span></label>
                <input type="number" class="form-control price-input" step="0.01" min="0" 
                       name="items[{index}][price]" required onchange="calculateItemTotal(this)">
            </div>
            <div class="col-md-1">
                <label class="form-label">Сумма</label>
                <input type="text" class="form-control item-total" readonly>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-outline-danger w-100" onclick="removeItem(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
let itemIndex = 0;

function addItem() {
    const template = document.getElementById('item-template');
    const container = document.getElementById('items-container');
    
    const itemHtml = template.innerHTML.replace(/{index}/g, itemIndex);
    
    const itemDiv = document.createElement('div');
    itemDiv.innerHTML = itemHtml;
    
    container.appendChild(itemDiv.firstElementChild);
    
    itemIndex++;
}

function removeItem(button) {
    const itemRow = button.closest('.item-row');
    itemRow.remove();
    calculateTotal();
}

function calculateItemTotal(input) {
    const itemRow = input.closest('.item-row');
    const quantityInput = itemRow.querySelector('.quantity-input');
    const priceInput = itemRow.querySelector('.price-input');
    const totalInput = itemRow.querySelector('.item-total');
    
    const quantity = parseFloat(quantityInput.value) || 0;
    const price = parseFloat(priceInput.value) || 0;
    const total = quantity * price;
    
    totalInput.value = total.toLocaleString('ru-RU') + ' ₽';
    
    calculateTotal();
}

function calculateTotal() {
    const itemTotals = document.querySelectorAll('.item-total');
    let grandTotal = 0;
    
    itemTotals.forEach(function(input) {
        const value = input.value.replace(/[^\d.,]/g, '').replace(',', '.');
        grandTotal += parseFloat(value) || 0;
    });
    
    document.getElementById('total-amount').textContent = grandTotal.toLocaleString('ru-RU') + ' ₽';
}

// Добавляем первую позицию при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    addItem();
});
</script>
@endpush
