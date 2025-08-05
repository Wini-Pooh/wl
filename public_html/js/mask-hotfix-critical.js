/**
 * Критический хотфикс для масок ввода (минимальная версия)
 * Встраивается прямо в HTML для гарантированной работы
 */
(function() {
    // Функция очистки значений от некорректных символов
    function cleanValue(value) {
        if (typeof value !== 'string') return value;
        
        // Удаляем символы масок и оставляем только цифры и запятые
        let cleaned = value.replace(/[{}\[\]\\|]/g, '');
        
        // Конвертируем в числовое значение если нужно
        if (/^\d+,\d+$/.test(cleaned)) {
            return parseFloat(cleaned.replace(',', '.')).toString();
        }
        
        return cleaned;
    }
    
    // Обработчик для всех форм
    $(document).on('submit', 'form', function() {
        const form = this;
        const fields = form.querySelectorAll('input[name="quantity"], input[name="price"], input[name="unit_price"], input[name="amount"]');
        
        fields.forEach(field => {
            if (field.value && /[{}\[\]\\|]/.test(field.value)) {
                const cleaned = cleanValue(field.value);
                console.log('HOTFIX: Исправлено поле ' + field.name + ': "' + field.value + '" → "' + cleaned + '"');
                field.value = cleaned;
            }
        });
    });
    
    console.log('✅ Критический хотфикс масок загружен');
})();
