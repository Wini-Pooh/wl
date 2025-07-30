<?php
// Тест подключения к базе данных
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Тест подключения к базе данных</h1>";

try {
    // Проверяем драйверы PHP
    echo "<h3>Установленные PDO драйверы:</h3>";
    $drivers = PDO::getAvailableDrivers();
    echo "<ul>";
    foreach ($drivers as $driver) {
        echo "<li>{$driver}</li>";
    }
    echo "</ul>";
    
    // Пытаемся подключиться к MySQL
    $host = '127.0.0.1';
    $port = '3306';
    $dbname = 'rem';
    $username = 'root';
    $password = '';
    
    echo "<h3>Попытка подключения к MySQL:</h3>";
    echo "<p>Host: {$host}:{$port}</p>";
    echo "<p>Database: {$dbname}</p>";
    echo "<p>Username: {$username}</p>";
    
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname}";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<div style='color: green;'><h3>✅ Подключение к MySQL успешно!</h3></div>";
    
    // Проверяем таблицы
    echo "<h3>Проверка таблиц:</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array('signature_requests', $tables)) {
        echo "<p style='color: green;'>✅ Таблица signature_requests существует</p>";
        
        // Проверяем записи
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM signature_requests");
        $count = $stmt->fetch()['count'];
        echo "<p>Записей в signature_requests: {$count}</p>";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT sr.id, sr.status, pd.original_name, pd.file_path 
                                FROM signature_requests sr 
                                LEFT JOIN project_documents pd ON sr.document_id = pd.id 
                                LIMIT 5");
            $requests = $stmt->fetchAll();
            
            echo "<h4>Последние запросы на подпись:</h4>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Status</th><th>Document Name</th><th>File Path</th></tr>";
            foreach ($requests as $req) {
                echo "<tr>";
                echo "<td>{$req['id']}</td>";
                echo "<td>{$req['status']}</td>";
                echo "<td>" . ($req['original_name'] ?? 'N/A') . "</td>";
                echo "<td>" . ($req['file_path'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>❌ Таблица signature_requests не найдена</p>";
    }
    
    if (in_array('project_documents', $tables)) {
        echo "<p style='color: green;'>✅ Таблица project_documents существует</p>";
        
        // Проверяем записи
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM project_documents");
        $count = $stmt->fetch()['count'];
        echo "<p>Записей в project_documents: {$count}</p>";
    } else {
        echo "<p style='color: red;'>❌ Таблица project_documents не найдена</p>";
    }
    
} catch (PDOException $e) {
    echo "<div style='color: red;'>";
    echo "<h3>❌ Ошибка подключения к MySQL:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
    
    echo "<h3>Возможные решения:</h3>";
    echo "<ul>";
    echo "<li>Убедитесь, что MySQL запущен в OSPanel</li>";
    echo "<li>Проверьте настройки в файле .env</li>";
    echo "<li>Убедитесь, что база данных 'rem' существует</li>";
    echo "<li>Проверьте права доступа</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<div style='color: red;'>";
    echo "<h3>❌ Общая ошибка:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
