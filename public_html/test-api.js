console.log("=== ТЕСТ AJAX ЗАПРОСОВ ===");
const projectId = window.projectId || 1;

// Функция для тестирования API
function testAPI(endpoint, description) {
    console.log(`Тестируем: ${description}`);
    
    fetch(`/partner/projects/${projectId}/${endpoint}`, {
        method: "GET",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json"
        }
    })
    .then(response => {
        console.log(`${description} - статус:`, response.status);
        return response.json();
    })
    .then(data => {
        console.log(`${description} - данные:`, data);
    })
    .catch(error => {
        console.error(`${description} - ошибка:`, error);
    });
}

// Тестируем все endpoints
testAPI("photos", "Фотографии");
testAPI("documents", "Документы");
testAPI("design", "Дизайн");
testAPI("schemes", "Схемы");
