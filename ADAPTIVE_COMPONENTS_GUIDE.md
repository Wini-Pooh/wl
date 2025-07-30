# Документация по адаптивным компонентам

## Обзор улучшений

Добавлены специальные адаптивные компоненты для улучшения пользовательского опыта на мобильных устройствах:

1. **Адаптивные вкладки аналитики** - компактное отображение вкладок в дашборде
2. **Сворачиваемые фильтры** - экономия места на мобильных устройствах с возможностью сворачивания/разворачивания фильтров

## 1. Адаптивные вкладки аналитики

### Автоматическая адаптация
Вкладки в `#analyticsTab` автоматически адаптируются для мобильных устройств:

- **768px и меньше**: Вкладки располагаются в сетке 2x2
- **480px и меньше**: Показываются только иконки
- Плавные анимации при переключении
- Touch-friendly области касания

### CSS классы
```css
/* Основные стили применяются автоматически */
#analyticsTab .nav-link {
    /* Адаптивные стили */
}
```

### HTML структура
```html
<ul class="nav nav-tabs card-header-tabs" id="analyticsTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab">
            <i class="bi bi-cash-stack me-2"></i>
            <span class="d-none d-sm-inline">Финансы</span>
        </button>
    </li>
    <!-- Другие вкладки... -->
</ul>
```

## 2. Сворачиваемые фильтры

### Автоматическая инициализация
JavaScript автоматически обнаруживает формы с фильтрами и создает для них мобильные версии:

```javascript
// Автоматически инициализируется при загрузке
document.addEventListener('DOMContentLoaded', function() {
    initMobileFilters();
});
```

### Функциональность
- **Автоматическое обнаружение**: Находит формы с полями фильтрации
- **Счетчик активных фильтров**: Показывает количество заполненных фильтров
- **Анимированное сворачивание**: Плавные переходы
- **Индикатор активности**: Красный badge с количеством активных фильтров

### HTML структура (до обработки)
```html
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/partner/projects">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Поиск</label>
                    <input type="text" class="form-control" id="search" name="search">
                </div>
                <!-- Другие поля... -->
            </div>
        </form>
    </div>
</div>
```

### HTML структура (после обработки на мобильных)
```html
<div class="card mb-4">
    <div class="card-body mobile-collapsible-filters">
        <!-- Кнопка переключения (добавляется автоматически) -->
        <button class="mobile-filter-toggle" type="button">
            <span>
                <i class="bi bi-funnel me-2"></i>
                Фильтры (2)
            </span>
            <i class="bi bi-chevron-down"></i>
            <span class="mobile-filter-badge">2</span>
        </button>
        
        <!-- Содержимое фильтров (скрывается/показывается) -->
        <div class="mobile-filters-content show">
            <form method="GET" action="/partner/projects">
                <!-- Поля формы -->
            </form>
        </div>
    </div>
</div>
```

## JavaScript API

### Основные функции

```javascript
// Инициализация мобильных фильтров
window.MobileResponsive.initMobileFilters();

// Переключение видимости фильтров
window.MobileResponsive.toggleMobileFilters(button, content);

// Обновление счетчика активных фильтров
window.MobileResponsive.updateFilterBadge(form);

// Полная переинициализация всех мобильных элементов
window.MobileResponsive.reinit();
```

### Обработка событий

```javascript
// Отслеживание изменений в фильтрах
const form = document.querySelector('form');
form.addEventListener('input', function() {
    window.MobileResponsive.updateFilterBadge(form);
});
```

## CSS переменные и стили

### Основные переменные
```css
:root {
    --brand-primary: rgba(59, 130, 246, 0.9);
    --brand-light: rgba(248, 250, 252, 0.95);
    --brand-gray-200: rgba(229, 231, 235, 0.85);
    --brand-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06);
    --brand-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --brand-radius-md: 10px;
}
```

### Кастомизация стилей

#### Изменение цветов кнопки фильтров
```css
@media (max-width: 768px) {
    .mobile-filter-toggle:hover {
        background: #your-color !important;
        color: white !important;
    }
}
```

#### Изменение анимации
```css
@media (max-width: 768px) {
    .mobile-filters-content {
        transition: all 0.5s ease !important; /* Замедленная анимация */
    }
}
```

#### Кастомизация badge
```css
@media (max-width: 768px) {
    .mobile-filter-badge {
        background: #your-color !important;
        font-size: 0.8rem !important;
    }
}
```

## Поддерживаемые поля фильтров

Автоматически обнаруживаются следующие типы полей:
- `input[type="text"]` - текстовые поля
- `input[type="tel"]` - телефонные номера  
- `select` - выпадающие списки

### Добавление поддержки новых типов полей

```javascript
// В функции countActiveFilters добавьте проверку для новых типов
function countActiveFilters(form) {
    let count = 0;
    
    // Существующие проверки...
    
    // Добавьте новые типы
    const emailInputs = form.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        if (input.value.trim() !== '') count++;
    });
    
    return count;
}
```

## Брейкпоинты

```css
/* Мобильные устройства - основные стили */
@media (max-width: 768px) { }

/* Очень маленькие экраны - минимальный интерфейс */
@media (max-width: 480px) { }

/* Планшеты и десктопы - стандартное отображение */
@media (min-width: 769px) { }
```

## Лучшие практики

### 1. Тестирование
- Тестируйте на реальных устройствах
- Проверяйте работу в обеих ориентациях
- Убедитесь в корректной работе touch-событий

### 2. Производительность
- Функции автоматически используют debounce для оптимизации
- Анимации оптимизированы для 60 FPS
- Минимальное количество DOM-манипуляций

### 3. Доступность
- Все элементы имеют корректные ARIA-атрибуты
- Поддержка навигации с клавиатуры
- Достаточный контраст цветов

### 4. Кастомизация
- Используйте CSS переменные для изменения цветов
- Переопределяйте стили через медиа-запросы
- Сохраняйте консистентность с общим дизайном

## Отладка

### Проверка инициализации
```javascript
// В консоли браузера
console.log(window.MobileResponsive);
```

### Принудительная переинициализация
```javascript
// После динамической загрузки контента
window.MobileResponsive.reinit();
```

### Проверка активных фильтров
```javascript
// Для конкретной формы
const form = document.querySelector('form');
const count = window.MobileResponsive.countActiveFilters(form);
console.log('Активных фильтров:', count);
```

## Совместимость

- **iOS Safari**: 12+
- **Chrome Mobile**: 80+
- **Samsung Internet**: 10+
- **Firefox Mobile**: 68+

Все компоненты имеют fallback для старых браузеров - на неподдерживаемых устройствах отображается стандартная версия интерфейса.
