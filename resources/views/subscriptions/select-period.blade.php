@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Выбор периода подписки</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h5 class="text-primary">{{ $plan->name }}</h5>
                        <p class="text-muted">{{ $plan->description }}</p>
                    </div>

                    <form method="POST" action="{{ route('subscriptions.subscribe', $plan) }}">
                        @csrf

                        <!-- Выбор периода -->
                        <div class="mb-4">
                            <label class="form-label">Период подписки</label>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="billing_period" id="monthly" value="monthly" checked>
                                <label class="form-check-label" for="monthly">
                                    <strong>Месячная подписка</strong><br>
                                    <span class="text-muted">{{ number_format($plan->monthly_price, 0, ',', ' ') }} ₽ в месяц</span>
                                </label>
                            </div>

                            @if($plan->yearly_price)
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="billing_period" id="yearly" value="yearly">
                                    <label class="form-check-label" for="yearly">
                                        <strong>Годовая подписка</strong>
                                        <span class="badge bg-success ms-2">Скидка {{ $plan->yearly_discount_percent }}%</span><br>
                                        <span class="text-muted">{{ number_format($plan->yearly_price, 0, ',', ' ') }} ₽ в год</span>
                                        <small class="text-success d-block">
                                            Экономия: {{ number_format($plan->getYearlySavings(), 0, ',', ' ') }} ₽
                                        </small>
                                    </label>
                                </div>
                            @endif
                        </div>

                        <!-- Способ оплаты -->
                        <div class="mb-4">
                            <label class="form-label">Способ оплаты</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">Выберите способ оплаты</option>
                                <option value="bank_card">Банковская карта</option>
                                <option value="bank_transfer">Банковский перевод</option>
                                <option value="qr_code">QR-код</option>
                            </select>
                        </div>

                        <!-- Автопродление -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="auto_renewal" id="auto_renewal" checked>
                            <label class="form-check-label" for="auto_renewal">
                                Включить автоматическое продление подписки
                            </label>
                        </div>

                        <!-- Сводка заказа -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">Сводка заказа</h6>
                                <div class="d-flex justify-content-between">
                                    <span>Тариф:</span>
                                    <span>{{ $plan->name }}</span>
                                </div>
                                <div class="d-flex justify-content-between" id="period-summary">
                                    <span>Период:</span>
                                    <span id="period-text">Месячная</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>К оплате:</span>
                                    <span id="total-price">{{ number_format($plan->monthly_price, 0, ',', ' ') }} ₽</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-credit-card"></i> Оформить подписку
                            </button>
                            <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-secondary">
                                Назад к тарифам
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyRadio = document.getElementById('monthly');
    const yearlyRadio = document.getElementById('yearly');
    const periodText = document.getElementById('period-text');
    const totalPrice = document.getElementById('total-price');

    function updateSummary() {
        if (monthlyRadio.checked) {
            periodText.textContent = 'Месячная';
            totalPrice.textContent = '{{ number_format($plan->monthly_price, 0, ',', ' ') }} ₽';
        } else if (yearlyRadio.checked) {
            periodText.textContent = 'Годовая';
            totalPrice.textContent = '{{ number_format($plan->yearly_price, 0, ',', ' ') }} ₽';
        }
    }

    monthlyRadio.addEventListener('change', updateSummary);
    if (yearlyRadio) {
        yearlyRadio.addEventListener('change', updateSummary);
    }
});
</script>
@endsection
