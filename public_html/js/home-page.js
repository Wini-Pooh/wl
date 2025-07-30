/**
 * JavaScript для адаптивной главной страницы
 * Версия: 1.0
 */

document.addEventListener('DOMContentLoaded', function() {
    initHomePage();
});

/**
 * Инициализация главной страницы
 */
function initHomePage() {
    initTouchFeatures();
    initAnimations();
    initCounters();
    initQuickAccess();
    initMobileOptimizations();
}

/**
 * Улучшения для touch-устройств
 */
function initTouchFeatures() {
    if ('ontouchstart' in window) {
        document.body.classList.add('touch-device');
        
        // Touch эффекты для интерактивных элементов
        const interactiveElements = document.querySelectorAll('.project-card, .quick-access-card, .stats-card, .news-item');
        
        interactiveElements.forEach(element => {
            let touchTimer;
            
            element.addEventListener('touchstart', function(e) {
                this.style.transform = 'scale(0.98)';
                this.style.transition = 'transform 0.1s ease';
                
                // Устанавливаем таймер для длинного нажатия
                touchTimer = setTimeout(() => {
                    this.style.transform = 'scale(1.02)';
                    // Легкая вибрация если поддерживается
                    if (navigator.vibrate) {
                        navigator.vibrate(50);
                    }
                }, 500);
            });
            
            element.addEventListener('touchend', function(e) {
                clearTimeout(touchTimer);
                this.style.transform = '';
                this.style.transition = 'transform 0.3s ease';
            });
            
            element.addEventListener('touchcancel', function(e) {
                clearTimeout(touchTimer);
                this.style.transform = '';
                this.style.transition = 'transform 0.3s ease';
            });
        });
    }
}

/**
 * Инициализация анимаций появления
 */
function initAnimations() {
    // Intersection Observer для анимаций при скролле
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        // Наблюдаем за элементами без класса fade-in-up
        document.querySelectorAll('.card:not(.fade-in-up)').forEach(card => {
            observer.observe(card);
        });
    }
}

/**
 * Анимация счетчиков
 */
function initCounters() {
    const counters = document.querySelectorAll('.display-6');
    
    counters.forEach(counter => {
        const target = parseInt(counter.textContent) || 0;
        if (target === 0) return;
        
        let current = 0;
        const increment = target / 50;
        const duration = 1000;
        const stepTime = duration / 50;
        
        // Сначала обнуляем
        counter.textContent = '0';
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current);
        }, stepTime);
    });
}

/**
 * Быстрый доступ к функциям
 */
function initQuickAccess() {
    // Быстрые клавиши
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + P - переход к проектам
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            const projectsLink = document.querySelector('a[href*="projects"]');
            if (projectsLink) {
                projectsLink.click();
            }
        }
        
        // Ctrl/Cmd + H - обновление страницы
        if ((e.ctrlKey || e.metaKey) && e.key === 'h') {
            e.preventDefault();
            window.location.reload();
        }
    });
    
    // Клики по карточкам проектов
    document.querySelectorAll('.project-card').forEach(card => {
        card.addEventListener('click', function() {
            // Если есть ссылка в карточке, переходим по ней
            const link = this.querySelector('a');
            if (link) {
                link.click();
            } else {
                // Иначе пытаемся найти ID проекта и перейти к нему
                const projectData = this.dataset.projectId;
                if (projectData) {
                    window.location.href = `/partner/projects/${projectData}`;
                }
            }
        });
    });
}

/**
 * Мобильные оптимизации
 */
function initMobileOptimizations() {
    // Проверяем размер экрана
    const isMobile = window.innerWidth <= 768;
    
    if (isMobile) {
        // Упрощаем анимации на мобильных для производительности
        document.documentElement.style.setProperty('--animation-duration', '0.2s');
        
        // Оптимизируем тач события
        document.body.style.touchAction = 'manipulation';
        
        // Отключаем hover эффекты на мобильных
        const style = document.createElement('style');
        style.textContent = `
            @media (hover: none) {
                .card:hover, .project-card:hover, .stats-card:hover {
                    transform: none !important;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Обработка изменения ориентации
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            // Пересчитываем layout после смены ориентации
            window.dispatchEvent(new Event('resize'));
        }, 100);
    });
    
    // Обработка изменения размера окна
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            adjustLayoutForScreen();
        }, 100);
    });
}

/**
 * Адаптация layout под размер экрана
 */
function adjustLayoutForScreen() {
    const width = window.innerWidth;
    
    // Очень маленькие экраны (до 375px)
    if (width <= 375) {
        document.body.classList.add('small-screen');
        // Уменьшаем отступы
        document.documentElement.style.setProperty('--card-padding', '0.75rem');
    } else {
        document.body.classList.remove('small-screen');
        document.documentElement.style.setProperty('--card-padding', '1rem');
    }
    
    // Планшеты в портретной ориентации
    if (width >= 768 && width <= 1024) {
        document.body.classList.add('tablet-portrait');
    } else {
        document.body.classList.remove('tablet-portrait');
    }
}

/**
 * Утилиты для статусов проектов
 */
function getStatusText(status) {
    const statusMap = {
        'new': 'Новый',
        'in_progress': 'В работе',
        'design': 'Проектирование',
        'materials_preparation': 'Подготовка материалов',
        'paused': 'Приостановлен',
        'completed': 'Завершен',
        'cancelled': 'Отменен'
    };
    return statusMap[status] || status;
}

/**
 * Функция для показа уведомлений
 */
function showNotification(message, type = 'info') {
    // Создаем уведомление
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease;
    `;
    
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Автоматически убираем через 5 секунд
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

/**
 * Обработка ошибок загрузки изображений
 */
document.addEventListener('error', function(e) {
    if (e.target.tagName === 'IMG') {
        e.target.src = '/images/placeholder.svg';
        e.target.alt = 'Изображение недоступно';
    }
}, true);

/**
 * Предзагрузка критических ресурсов
 */
function preloadCriticalResources() {
    const criticalUrls = [
        '/partner/projects',
        '/css/mobile-projects.css'
    ];
    
    criticalUrls.forEach(url => {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = url;
        document.head.appendChild(link);
    });
}

// Запускаем предзагрузку после загрузки страницы
window.addEventListener('load', preloadCriticalResources);

/**
 * Сервис-воркер для кэширования (если поддерживается)
 */
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}
