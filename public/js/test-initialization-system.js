/**
 * Тестовый скрипт для проверки оптимизированной системы инициализации
 * Разместить в консоли браузера для проверки работоспособности
 */

console.log('🧪 Начинаем тестирование системы инициализации...');

// Проверка наличия основных компонентов
const tests = {
    projectManager: !!window.projectManager,
    projectId: !!window.projectId,
    financeUnifiedManager: !!window.financeUnifiedManager,
    inputMaskManager: !!window.inputMaskManager
};

console.log('📋 Результаты проверки компонентов:', tests);

// Тестирование ProjectInitializationManager
if (window.projectManager) {
    console.log('✅ ProjectManager найден, проводим детальное тестирование...');
    
    // Получаем диагностику
    const diagnostics = window.projectManager.getDiagnostics();
    console.log('🔍 Диагностика системы:', diagnostics);
    
    // Проверяем состояние инициализации
    const status = window.projectManager.getInitializationStatus();
    console.log('📊 Статус инициализации:', status);
    
    // Тестируем готовность критических компонентов
    const criticalComponents = ['csrf_token', 'modal_basics', 'notifications'];
    criticalComponents.forEach(component => {
        const isReady = window.projectManager.isReady(component);
        console.log(`${isReady ? '✅' : '❌'} ${component}: ${isReady ? 'готов' : 'не готов'}`);
    });
    
    // Тестируем инициализацию тестового модального окна
    console.log('🧪 Тестируем инициализацию модального окна...');
    window.projectManager.initModal('testModal', 'test', function() {
        console.log('✅ Тестовое модальное окно успешно инициализировано');
    });
    
} else {
    console.error('❌ ProjectManager не найден! Проверьте подключение скриптов.');
}

// Проверка финансовых компонентов
if (window.financeUnifiedManager) {
    console.log('✅ FinanceUnifiedManager найден');
} else {
    console.warn('⚠️ FinanceUnifiedManager не найден');
}

// Проверка масок ввода
if (window.inputMaskManager) {
    console.log('✅ InputMaskManager найден');
} else {
    console.warn('⚠️ InputMaskManager не найден');
}

// Проверка модальных окон
const modalIds = ['workModal', 'materialModal', 'transportModal'];
modalIds.forEach(modalId => {
    const modal = document.getElementById(modalId);
    if (modal) {
        console.log(`✅ Модальное окно ${modalId} найдено в DOM`);
    } else {
        console.log(`ℹ️ Модальное окно ${modalId} не найдено (может быть нормально)`);
    }
});

// Финальный отчет
console.log('🎯 Тестирование завершено!');
console.log('💡 Для получения полной диагностики выполните: window.projectManager.getDiagnostics()');
console.log('💡 Для проверки готовности компонента: window.projectManager.isReady("component_name")');
console.log('💡 Для просмотра статуса: window.projectManager.getInitializationStatus()');
