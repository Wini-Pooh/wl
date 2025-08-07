<!-- Левая боковая панель с возможностью сворачивания -->
<div class="sidebar-container" id="sidebarContainer">
    <div class="sidebar-header">
        <button class="btn sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-layout-sidebar-inset"></i>
        </button>
    </div>

    <div class="sidebar-content">
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <!-- Меню для всех пользователей -->
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('home')); ?>">
                        <i class="bi bi-house-heart-fill"></i> 
                        <span class="menu-text"><?php echo e(__('Главная')); ?></span>
                    </a>
                </li>
                
                <!-- Меню для администраторов -->
                <?php if(Auth::check() && Auth::user()->isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('admin.users.index')); ?>">
                        <i class="bi bi-people-fill"></i> 
                        <span class="menu-text"><?php echo e(__('Управление пол')); ?></span>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Меню для партнеров и админов -->
                <?php if(Auth::check() && (Auth::user()->isPartner() || Auth::user()->isAdmin())): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('partner.dashboard')); ?>">
                        <i class="bi bi-speedometer2"></i> 
                        <span class="menu-text"><?php echo e(__('Панель партнера')); ?></span>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Меню проектов/объектов (для всех кроме сметчиков) -->
                <?php if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isPartner() || Auth::user()->isEmployee() || Auth::user()->isForeman() || Auth::user()->isClient())): ?>
                <li class="nav-item">
                    <?php if(Auth::user()->isAdmin() || Auth::user()->isPartner() || Auth::user()->isForeman()): ?>
                        <a class="nav-link" href="<?php echo e(route('partner.projects.index')); ?>">
                    <?php elseif(Auth::user()->isEmployee()): ?>
                        <a class="nav-link" href="<?php echo e(route('employee.projects.index')); ?>">
                    <?php elseif(Auth::user()->isClient()): ?>
                        <a class="nav-link" href="<?php echo e(route('partner.projects.index')); ?>">
                    <?php endif; ?>
                        <i class="bi bi-buildings-fill"></i> 
                        <span class="menu-text"><?php echo e(__('Объекты')); ?></span>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Меню смет (для всех кроме клиентов) -->
                <?php if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isPartner() || Auth::user()->isEmployee() || Auth::user()->isEstimator() || Auth::user()->isForeman())): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('partner.estimates.index')); ?>">
                        <i class="bi bi-calculator-fill"></i> 
                        <span class="menu-text"><?php echo e(__('Сметы')); ?></span>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Меню документов (для всех авторизованных пользователей) -->
                <?php if(Auth::check()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('documents.index')); ?>">
                        <i class="bi bi-file-earmark-text"></i> 
                        <span class="menu-text"><?php echo e(__('Документы')); ?></span>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Меню тарифов (для всех авторизованных пользователей) -->
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('subscriptions.index')); ?>">
                        <i class="bi bi-credit-card"></i> 
                        <span class="menu-text"><?php echo e(__('Тарифы')); ?></span>
                    </a>
                </li>
                
                <!-- Меню сотрудников (только для партнеров, сотрудников и админов - НЕ прорабов) -->
                <?php if(Auth::check() && (Auth::user()->isPartner() || Auth::user()->isEmployee() || Auth::user()->isAdmin())): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('partner.employees.index')); ?>">
                        <i class="bi bi-people"></i> 
                        <span class="menu-text"><?php echo e(__('Сотрудники')); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('partner.employees.dashboard')); ?>">
                        <i class="bi bi-graph-up"></i> 
                        <span class="menu-text"><?php echo e(__('Финансы сотрудников')); ?></span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Меню для сотрудников, прорабов и сметчиков -->
                <?php if(Auth::check() && (Auth::user()->isEmployee() || Auth::user()->isEstimator() || Auth::user()->isForeman())): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('employee.dashboard')); ?>">
                        <i class="bi bi-speedometer2"></i> 
                        <span class="menu-text"><?php echo e(__('Рабочий стол')); ?></span>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Дополнительная информация о роли пользователя -->
                <?php if(Auth::check()): ?>
                <li class="nav-item mt-auto">
                    <div class="nav-link text-muted small">
                        <i class="bi bi-person-badge"></i>
                        <span class="menu-text">
                            <?php if(Auth::user()->isAdmin()): ?>
                                Администратор
                            <?php elseif(Auth::user()->isPartner()): ?>
                                Партнер
                            <?php elseif(Auth::user()->isEmployee()): ?>
                                Сотрудник
                            <?php elseif(Auth::user()->isForeman()): ?>
                                Прораб
                            <?php elseif(Auth::user()->isEstimator()): ?>
                                Сметчик
                            <?php elseif(Auth::user()->isClient()): ?>
                                Клиент
                            <?php else: ?>
                                Пользователь
                            <?php endif; ?>
                        </span>
                    </div>
                </li>
                <?php endif; ?>
            </ul>
        </div>
        
        <!-- Компонент лимитов подписки -->
        <?php echo $__env->make('components.subscription-limits', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div>

<!-- Overlay для мобильных устройств -->
<div class="mobile-overlay" id="mobileOverlay"></div>

<!-- JavaScript для управления боковой панелью -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarContainer = document.getElementById('sidebarContainer');
    const body = document.body;
    const mobileOverlay = document.getElementById('mobileOverlay');
    
    // Проверяем ширину экрана для определения мобильного устройства
    function isMobile() {
        return window.innerWidth <= 768;
    }
    
    // Функция для обновления иконки
    function updateToggleIcon() {
        const toggleIcon = sidebarToggle.querySelector('i.bi');
        
        if (isMobile()) {
            if (body.classList.contains('sidebar-mobile-open')) {
                toggleIcon.className = 'bi bi-x-lg';
            } else {
                toggleIcon.className = 'bi bi-list';
            }
        } else {
            if (body.classList.contains('sidebar-collapsed')) {
                toggleIcon.className = 'bi bi-chevron-double-right';
            } else {
                toggleIcon.className = 'bi bi-chevron-double-left';
            }
        }
    }
    
    // Функция для инициализации состояния sidebar
    function initializeSidebar() {
        if (isMobile()) {
            // На мобильных устройствах sidebar всегда скрыт по умолчанию
            body.classList.remove('sidebar-collapsed');
            body.classList.remove('sidebar-mobile-open');
        } else {
            // На десктопе проверяем сохраненное состояние
            const sidebarState = localStorage.getItem('sidebarCollapsed');
            if (sidebarState === 'true') {
                body.classList.add('sidebar-collapsed');
            } else {
                body.classList.remove('sidebar-collapsed');
            }
        }
        updateToggleIcon();
    }
    
    // Обработчик события для кнопки переключения
    sidebarToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (isMobile()) {
            // На мобильных устройствах переключаем видимость
            body.classList.toggle('sidebar-mobile-open');
        } else {
            // На десктопе переключаем сворачивание
            body.classList.toggle('sidebar-collapsed');
            
            // Сохраняем состояние в localStorage
            const isCollapsed = body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }
        
        updateToggleIcon();
    });
    
    // Обработчик клика по overlay для закрытия sidebar на мобильных
    mobileOverlay.addEventListener('click', function() {
        if (isMobile()) {
            body.classList.remove('sidebar-mobile-open');
            updateToggleIcon();
        }
    });
    
    // Обработчик изменения размера окна
    window.addEventListener('resize', function() {
        initializeSidebar();
    });
    
    // Инициализация при загрузке страницы
    initializeSidebar();
    
    // Функция для установки активного пункта меню
    function setActiveMenuItem() {
        const currentPath = window.location.pathname;
        const menuLinks = document.querySelectorAll('.sidebar-menu .nav-link');
        let activeLink = null;
        let maxMatchLength = 0;
        
        menuLinks.forEach(link => {
            link.classList.remove('active');
            
            try {
                // Получаем путь ссылки
                let linkPath;
                if (link.href && link.href.startsWith('http')) {
                    linkPath = new URL(link.href).pathname;
                } else {
                    linkPath = link.getAttribute('href');
                }
                
                // Пропускаем якорные ссылки и некорректные пути
                if (!linkPath || linkPath.startsWith('#') || linkPath === 'javascript:void(0)') {
                    return;
                }
                
                // Точное совпадение имеет приоритет
                if (currentPath === linkPath) {
                    activeLink = link;
                    maxMatchLength = linkPath.length;
                } 
                // Частичное совпадение (но не для корневого пути)
                else if (linkPath !== '/' && currentPath.startsWith(linkPath) && linkPath.length > maxMatchLength) {
                    activeLink = link;
                    maxMatchLength = linkPath.length;
                }
            } catch (error) {
                // Игнорируем ошибки для некорректных URL
                console.warn('Invalid URL in menu link:', link.href || link.getAttribute('href'));
            }
        });
        
        // Устанавливаем активный класс для наиболее подходящей ссылки
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }
    
    setActiveMenuItem();
});
</script>
        
   <?php /**PATH C:\OSPanel\domains\rem\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>