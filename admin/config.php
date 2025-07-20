<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Режим отладки (поставьте false в production)
define('DEBUG_MODE', true);

$host = 'localhost';
$dbname = 'vh1u23185_ck';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    if (DEBUG_MODE) {
        error_log("Подключение к БД успешно установлено");
        // Для вывода в браузер (только для отладки!)
       
    }
    
} catch (PDOException $e) {
    $error_message = "Ошибка подключения к базе данных: " . $e->getMessage();
    error_log($error_message);
    die($error_message);
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

?>