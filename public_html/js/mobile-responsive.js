/**
 * Адаптивные функции для мобильных и планшетных устройств
 * Версия: 1.1 - добавлена поддержка проектов
 */

document.addEventListener('DOMContentLoaded', function() {
    initMobileResponsive();
    initProjectsMobileFeatures();
});

/**
 * Инициализация мобильных функций для страницы проектов
 */
function initProjectsMobileFeatures() {
    if (!isProjectsPage()) return;
    
    initProjectFiltersCollapse();
    initProjectCardTouchEvents();
    initProjectMobileNavigation();
    initProjectSearchEnhancements();
}

/**
 * Проверка, находимся ли мы на странице проектов
 */
function isProjectsPage() {
    return window.location.pathname.includes('/projects') || 
           document.querySelector('.mobile-project-card') !== null;
}

/**
 * Инициализация коллапса фильтров для проектов
 */
function initProjectFiltersCollapse() {
    const filtersToggle = document.querySelector('[data-bs-target="#filtersCollapse"]');
    const filtersCollapse = document.getElementById('filtersCollapse');
    
    if (!filtersToggle || !filtersCollapse) return;
    
    // Автоматическое скрытие фильтров после применения на мобильных
    const filterForm = document.getElementById('filterForm');
    if (filterForm && window.innerWidth <= 768) {
        filterForm.addEventListener('submit', function() {
            setTimeout(() => {
                const bsCollapse = bootstrap.Collapse.getInstance(filtersCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            }, 300);
        });
    }
    
    // Анимация иконки chevron
    filtersToggle.addEventListener('click', function() {
        const chevron = this.querySelector('.bi-chevron-down');
        if (chevron) {
            chevron.style.transform = this.classList.contains('collapsed') ? 
                'rotate(0deg)' : 'rotate(180deg)';
        }
    });
}

/**
 * Touch события для карточек проектов
 */
function initProjectCardTouchEvents() {
    const projectCards = document.querySelectorAll('.mobile-project-card, .project-card');
    
    projectCards.forEach(card => {
        let touchStartY = 0;
        let touchStartTime = 0;
        
        card.addEventListener('touchstart', function(e) {
            touchStartY = e.touches[0].clientY;
            touchStartTime = Date.now();
            this.style.transform = 'scale(0.98)';
            this.style.transition = 'transform 0.1s ease';
        });
        
        card.addEventListener('touchend', function(e) {
            this.style.transform = 'scale(1)';
            
            // Проверяем на quick tap
            const touchEndTime = Date.now();
            const touchDuration = touchEndTime - touchStartTime;
            
            if (touchDuration < 200) {
                // Быстрый тап - имитируем клик
                const viewButton = this.querySelector('a[href*="show"]');
                if (viewButton && !e.target.closest('.btn')) {
                    window.location.href = viewButton.href;
                }
            }
        });
        
        card.addEventListener('touchcancel', function() {
            this.style.transform = 'scale(1)';
        });
    });
}

/**
 * Улучшенная навигация для мобильных проектов
 */
function initProjectMobileNavigation() {
    // Плавная прокрутка к карточкам
    const viewModeButtons = document.querySelectorAll('input[name="viewMode"]');
    viewModeButtons.forEach(button => {
        button.addEventListener('change', function() {
            setTimeout(() => {
                const activeContent = document.querySelector('.view-content[style*="block"]');
                if (activeContent) {
                    activeContent.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                }
            }, 300);
        });
    });
    
    // Кнопка "Наверх" для длинных списков
    createScrollToTopButton();
}

/**
 * Создание кнопки "Наверх"
 */
function createScrollToTopButton() {
    if (document.querySelector('.scroll-to-top')) return;
    
    const button = document.createElement('button');
    button.className = 'btn btn-primary scroll-to-top position-fixed';
    button.innerHTML = '<i class="bi bi-arrow-up"></i>';
    button.style.cssText = `
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    `;
    
    document.body.appendChild(button);
    
    // Показ/скрытие кнопки при прокрутке
    window.addEventListener('scroll', debounce(function() {
        if (window.pageYOffset > 300) {
            button.style.display = 'block';
        } else {
            button.style.display = 'none';
        }
    }, 100));
    
    // Прокрутка наверх
    button.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/**
 * Улучшения поиска для мобильных
 */
function initProjectSearchEnhancements() {
    const searchInputs = document.querySelectorAll('#search, #phone');
    
    searchInputs.forEach(input => {
        // Очистка поля по двойному тапу
        let lastTap = 0;
        input.addEventListener('touchend', function(e) {
            const currentTime = new Date().getTime();
            const tapLength = currentTime - lastTap;
            
            if (tapLength < 500 && tapLength > 0) {
                this.value = '';
                this.focus();
            }
            lastTap = currentTime;
        });
        
        // Автоматическая отправка формы при вводе (с задержкой)
        input.addEventListener('input', debounce(function() {
            if (this.value.length >= 3 || this.value.length === 0) {
                // Автоматическая отправка формы для быстрого поиска
                // this.form.submit();
            }
        }, 1000));
    });
}

/**
 * Debounce функция для оптимизации производительности
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Инициализация адаптивных функций
 */
function initMobileResponsive() {
    initMobileSidebar();
    initMobileTabs();
    initMobileModals();
    initTouchGestures();
    initMobileSearch();
    initViewportFixes();
    initMobileFilters();
    
    // Переинициализация при изменении ориентации
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            reinitMobileElements();
        }, 100);
    });
    
    // Переинициализация при изменении размера окна
    window.addEventListener('resize', debounce(function() {
        reinitMobileElements();
    }, 250));
}

/**
 * Инициализация мобильного сайдбара
 */
function initMobileSidebar() {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.getElementById('sidebarContainer');
    
    if (!mobileToggle || !sidebar) return;
    
    // Создаем overlay для мобильного меню
    let overlay = document.querySelector('.mobile-sidebar-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'mobile-sidebar-overlay';
        document.body.appendChild(overlay);
    }
    
    // Обработчик кнопки меню
    mobileToggle.addEventListener('click', function(e) {
        e.preventDefault();
        toggleMobileSidebar();
    });
    
    // Закрытие по клику на overlay
    overlay.addEventListener('click', function() {
        closeMobileSidebar();
    });
    
    // Закрытие по ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('show')) {
            closeMobileSidebar();
        }
    });
    
    // Закрытие при клике на ссылку в меню
    const sidebarLinks = sidebar.querySelectorAll('.nav-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                setTimeout(() => closeMobileSidebar(), 150);
            }
        });
    });
}

/**
 * Переключение мобильного сайдбара
 */
function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebarContainer');
    const overlay = document.querySelector('.mobile-sidebar-overlay');
    const body = document.body;
    
    if (sidebar.classList.contains('show')) {
        closeMobileSidebar();
    } else {
        sidebar.classList.add('show');
        overlay.classList.add('show');
        body.style.overflow = 'hidden';
    }
}

/**
 * Закрытие мобильного сайдбара
 */
function closeMobileSidebar() {
    const sidebar = document.getElementById('sidebarContainer');
    const overlay = document.querySelector('.mobile-sidebar-overlay');
    const body = document.body;
    
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
    body.style.overflow = '';
}

/**
 * Инициализация мобильных вкладок
 */
function initMobileTabs() {
    const tabContainers = document.querySelectorAll('.nav-tabs');
    
    tabContainers.forEach(container => {
        if (window.innerWidth <= 768) {
            makeMobileFriendlyTabs(container);
        }
    });
}

/**
 * Адаптация вкладок для мобильных устройств
 */
function makeMobileFriendlyTabs(tabContainer) {
    const tabs = tabContainer.querySelectorAll('.nav-link');
    
    tabs.forEach(tab => {
        // Добавляем touch-события
        tab.addEventListener('touchstart', function() {
            tab.style.backgroundColor = 'var(--brand-primary)';
            tab.style.color = 'white';
        });
        
        tab.addEventListener('touchend', function() {
            setTimeout(() => {
                if (!tab.classList.contains('active')) {
                    tab.style.backgroundColor = '';
                    tab.style.color = '';
                }
            }, 150);
        });
    });
    
    // Горизонтальная прокрутка для вкладок
    if (tabContainer.scrollWidth > tabContainer.clientWidth) {
        tabContainer.style.overflowX = 'auto';
        tabContainer.style.whiteSpace = 'nowrap';
        tabContainer.style.scrollbarWidth = 'thin';
    }
}

/**
 * Инициализация мобильных модальных окон
 */
function initMobileModals() {
    const modals = document.querySelectorAll('.modal');
    
    modals.forEach(modal => {
        // Автофокус для мобильных устройств
        modal.addEventListener('shown.bs.modal', function() {
            if (window.innerWidth <= 768) {
                const firstInput = modal.querySelector('input:not([type="hidden"]), select, textarea');
                if (firstInput) {
                    // Задержка для избежания проблем с клавиатурой
                    setTimeout(() => {
                        firstInput.focus();
                    }, 300);
                }
            }
        });
        
        // Предотвращение закрытия модала при touch вне области
        modal.addEventListener('touchstart', function(e) {
            if (e.target === modal) {
                e.stopPropagation();
            }
        });
    });
}

/**
 * Инициализация touch-жестов
 */
function initTouchGestures() {
    // Свайп для закрытия сайдбара
    let startX = 0;
    let startY = 0;
    
    document.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    });
    
    document.addEventListener('touchmove', function(e) {
        if (!startX || !startY) return;
        
        const currentX = e.touches[0].clientX;
        const currentY = e.touches[0].clientY;
        
        const diffX = startX - currentX;
        const diffY = startY - currentY;
        
        // Свайп влево для закрытия сайдбара
        if (Math.abs(diffX) > Math.abs(diffY) && diffX > 50) {
            const sidebar = document.getElementById('sidebarContainer');
            if (sidebar && sidebar.classList.contains('show')) {
                closeMobileSidebar();
            }
        }
        
        startX = 0;
        startY = 0;
    });
}

/**
 * Инициализация мобильных фильтров
 */
function initMobileFilters() {
    if (window.innerWidth > 768) return;
    
    // Находим все формы с фильтрами
    const filterForms = document.querySelectorAll('.card .card-body form');
    
    filterForms.forEach(form => {
        const cardBody = form.closest('.card-body');
        if (!cardBody) return;
        
        // Проверяем, есть ли поля фильтрации
        const hasFilters = form.querySelectorAll('input[type="text"], select, input[type="tel"]').length > 0;
        if (!hasFilters) return;
        
        // Создаем мобильную версию
        createMobileFilterToggle(cardBody, form);
    });
}

/**
 * Создание переключателя для мобильных фильтров
 */
function createMobileFilterToggle(cardBody, form) {
    // Подсчитываем активные фильтры
    const activeFilters = countActiveFilters(form);
    
    // Создаем кнопку переключения
    const toggleButton = document.createElement('button');
    toggleButton.className = 'mobile-filter-toggle';
    toggleButton.type = 'button';
    toggleButton.innerHTML = `
        <span>
            <i class="bi bi-funnel me-2"></i>
            Фильтры ${activeFilters > 0 ? `(${activeFilters})` : ''}
        </span>
        <i class="bi bi-chevron-down"></i>
    `;
    
    // Добавляем индикатор активных фильтров
    if (activeFilters > 0) {
        const badge = document.createElement('span');
        badge.className = 'mobile-filter-badge';
        badge.textContent = activeFilters;
        toggleButton.style.position = 'relative';
        toggleButton.appendChild(badge);
    }
    
    // Оборачиваем форму в контейнер
    const filtersContent = document.createElement('div');
    filtersContent.className = 'mobile-filters-content';
    
    // Перемещаем форму в контейнер
    form.parentNode.insertBefore(filtersContent, form);
    filtersContent.appendChild(form);
    
    // Добавляем кнопку перед контейнером
    cardBody.insertBefore(toggleButton, filtersContent);
    
    // Добавляем класс для стилизации
    cardBody.classList.add('mobile-collapsible-filters');
    
    // Обработчик клика
    toggleButton.addEventListener('click', function() {
        toggleMobileFilters(toggleButton, filtersContent);
    });
    
    // Автоматически раскрываем если есть активные фильтры
    if (activeFilters > 0) {
        filtersContent.classList.add('show');
        toggleButton.classList.add('expanded');
    }
}

/**
 * Переключение видимости мобильных фильтров
 */
function toggleMobileFilters(button, content) {
    const isExpanded = content.classList.contains('show');
    
    if (isExpanded) {
        // Сворачиваем
        content.classList.remove('show');
        button.classList.remove('expanded');
        button.style.background = '';
        button.style.color = '';
    } else {
        // Разворачиваем
        content.classList.add('show');
        button.classList.add('expanded');
    }
}

/**
 * Подсчет активных фильтров
 */
function countActiveFilters(form) {
    let count = 0;
    
    // Проверяем текстовые поля
    const textInputs = form.querySelectorAll('input[type="text"], input[type="tel"]');
    textInputs.forEach(input => {
        if (input.value.trim() !== '') count++;
    });
    
    // Проверяем селекты
    const selects = form.querySelectorAll('select');
    selects.forEach(select => {
        if (select.value !== '') count++;
    });
    
    return count;
}

/**
 * Обновление счетчика активных фильтров
 */
function updateFilterBadge(form) {
    const cardBody = form.closest('.card-body');
    if (!cardBody) return;
    
    const toggleButton = cardBody.querySelector('.mobile-filter-toggle');
    if (!toggleButton) return;
    
    const activeFilters = countActiveFilters(form);
    const badge = toggleButton.querySelector('.mobile-filter-badge');
    const span = toggleButton.querySelector('span');
    
    // Обновляем текст
    span.innerHTML = `
        <i class="bi bi-funnel me-2"></i>
        Фильтры ${activeFilters > 0 ? `(${activeFilters})` : ''}
    `;
    
    // Обновляем или удаляем badge
    if (activeFilters > 0) {
        if (!badge) {
            const newBadge = document.createElement('span');
            newBadge.className = 'mobile-filter-badge';
            newBadge.textContent = activeFilters;
            toggleButton.appendChild(newBadge);
            toggleButton.style.position = 'relative';
        } else {
            badge.textContent = activeFilters;
        }
    } else {
        if (badge) {
            badge.remove();
        }
    }
}

/**
 * Инициализация отслеживания изменений в фильтрах
 */
function initFilterChangeTracking() {
    const filterForms = document.querySelectorAll('.mobile-collapsible-filters form');
    
    filterForms.forEach(form => {
        // Отслеживаем изменения в полях
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('input', debounce(() => {
                updateFilterBadge(form);
            }, 300));
            
            input.addEventListener('change', () => {
                updateFilterBadge(form);
            });
        });
    });
}
function initMobileSearch() {
    const searchInputs = document.querySelectorAll('input[type="search"], input[placeholder*="поиск"], input[placeholder*="Поиск"]');
    
    searchInputs.forEach(input => {
        // Автокомплит отключаем для мобильных
        if (window.innerWidth <= 768) {
            input.setAttribute('autocomplete', 'off');
            input.setAttribute('autocorrect', 'off');
            input.setAttribute('autocapitalize', 'off');
            input.setAttribute('spellcheck', 'false');
        }
        
        // Фокус и потеря фокуса
        input.addEventListener('focus', function() {
            if (window.innerWidth <= 768) {
                // Прокрутка к элементу
                setTimeout(() => {
                    input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        });
    });
}

/**
 * Исправления для viewport
 */
function initViewportFixes() {
    // Фикс высоты viewport для iOS
    function setViewportHeight() {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    
    setViewportHeight();
    window.addEventListener('resize', setViewportHeight);
    
    // Предотвращение зума при фокусе на input (iOS)
    if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (parseFloat(window.getComputedStyle(input).fontSize) < 16) {
                input.style.fontSize = '16px';
            }
        });
    }
}

/**
 * Переинициализация мобильных элементов
 */
function reinitMobileElements() {
    // Проверяем изменение ориентации
    const isMobile = window.innerWidth <= 768;
    
    if (isMobile) {
        // Закрываем сайдбар если открыт при повороте
        closeMobileSidebar();
        
        // Переинициализируем вкладки
        initMobileTabs();
        
        // Переинициализируем фильтры
        initMobileFilters();
    }
    
    // Обновляем высоту viewport
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
}

/**
 * Debounce функция для оптимизации событий
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Утилиты для работы с таблицами на мобильных
 */
function initMobileTables() {
    const tables = document.querySelectorAll('.table-responsive');
    
    tables.forEach(table => {
        if (window.innerWidth <= 768) {
            // Добавляем прокрутку для таблиц
            table.style.overflowX = 'auto';
            table.style.webkitOverflowScrolling = 'touch';
            
            // Создаем переключатель вида
            const toggle = createMobileTableToggle(table);
            
            // Индикатор прокрутки
            const scrollIndicator = document.createElement('div');
            scrollIndicator.className = 'table-scroll-indicator';
            scrollIndicator.innerHTML = '<i class="bi bi-arrow-left-right"></i> Прокрутите для просмотра';
            scrollIndicator.style.cssText = `
                text-align: center;
                padding: 5px;
                background: var(--brand-light);
                font-size: 0.8rem;
                color: var(--brand-gray-600);
                border-radius: 0 0 8px 8px;
                margin-bottom: 10px;
            `;
            
            table.parentNode.insertBefore(scrollIndicator, table.nextSibling);
            
            // Скрываем индикатор при прокрутке
            table.addEventListener('scroll', function() {
                if (table.scrollLeft > 0) {
                    scrollIndicator.style.display = 'none';
                } else {
                    scrollIndicator.style.display = 'block';
                }
            });
            
            // Автоматически создаем карточную версию
            convertTableToCards(table);
        }
    });
}

/**
 * Инициализация после загрузки DOM
 */
document.addEventListener('DOMContentLoaded', function() {
    initMobileTables();
    initFilterChangeTracking();
});

/**
 * Экспорт функций для использования в других скриптах
 */
window.MobileResponsive = {
    toggleSidebar: toggleMobileSidebar,
    closeSidebar: closeMobileSidebar,
    reinit: reinitMobileElements,
    initTables: initMobileTables,
    createMobileTableToggle: createMobileTableToggle,
    toggleTableView: toggleTableView,
    convertTableToCards: convertTableToCards,
    initMobileFilters: initMobileFilters,
    toggleMobileFilters: toggleMobileFilters,
    updateFilterBadge: updateFilterBadge
};

/**
 * Создание переключателя вида таблицы
 */
function createMobileTableToggle(tableContainer) {
    if (window.innerWidth > 768) return;
    
    const toggle = document.createElement('div');
    toggle.className = 'table-view-toggle btn-group';
    toggle.innerHTML = `
        <button type="button" class="btn active" data-view="table">
            <i class="bi bi-table me-1"></i>Таблица
        </button>
        <button type="button" class="btn" data-view="cards">
            <i class="bi bi-grid-3x3-gap me-1"></i>Карточки
        </button>
    `;
    
    // Вставляем переключатель перед таблицей
    tableContainer.parentNode.insertBefore(toggle, tableContainer);
    
    // Обработчики переключения
    const buttons = toggle.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            toggleTableView(tableContainer, view);
            
            // Обновляем активную кнопку
            buttons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    return toggle;
}

/**
 * Переключение вида таблицы
 */
function toggleTableView(tableContainer, view) {
    const mobileCards = tableContainer.parentNode.querySelector('.table-mobile-cards');
    
    if (view === 'cards') {
        tableContainer.style.display = 'none';
        if (mobileCards) {
            mobileCards.classList.add('active');
        } else {
            // Создаем карточный вид если его нет
            convertTableToCards(tableContainer);
        }
    } else {
        tableContainer.style.display = 'block';
        if (mobileCards) {
            mobileCards.classList.remove('active');
        }
    }
}

/**
 * Конвертация таблицы в карточки
 */
function convertTableToCards(tableContainer) {
    const table = tableContainer.querySelector('table');
    if (!table) return;
    
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
    const rows = table.querySelectorAll('tbody tr');
    
    const cardsContainer = document.createElement('div');
    cardsContainer.className = 'table-mobile-cards';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const card = document.createElement('div');
        card.className = 'mobile-card';
        
        let cardHTML = '<div class="mobile-card-header">';
        cardHTML += `<h6 class="mobile-card-title">${cells[0]?.textContent || 'Элемент'}</h6>`;
        cardHTML += '</div>';
        
        cardHTML += '<div class="mobile-card-body">';
        
        cells.forEach((cell, index) => {
            if (index === 0) return; // Пропускаем первую ячейку (она в заголовке)
            
            const header = headers[index] || `Поле ${index}`;
            cardHTML += `
                <div class="mobile-field">
                    <div class="mobile-field-label">${header}</div>
                    <div class="mobile-field-value">${cell.innerHTML}</div>
                </div>
            `;
        });
        
        cardHTML += '</div>';
        
        // Добавляем действия если есть кнопки в последней ячейке
        const lastCell = cells[cells.length - 1];
        const buttons = lastCell?.querySelectorAll('.btn');
        if (buttons && buttons.length > 0) {
            cardHTML += '<div class="mobile-card-actions">';
            buttons.forEach(btn => {
                cardHTML += btn.outerHTML;
            });
            cardHTML += '</div>';
        }
        
        card.innerHTML = cardHTML;
        cardsContainer.appendChild(card);
    });
    
    // Вставляем после таблицы
    tableContainer.parentNode.insertBefore(cardsContainer, tableContainer.nextSibling);
    
    return cardsContainer;
}
