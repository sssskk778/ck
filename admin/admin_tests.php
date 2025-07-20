<?php
// Должно быть ПЕРВЫМИ строками, без пробелов/пустых строк перед <?php
require_once 'config.php';

// Проверка авторизации
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SESSION['admin_role'] !== 'super_admin') {
    header("Location: admin_dashboard.php");
    exit;
}

// Обработка POST-запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_test'])) {
    // ... ваш существующий код обработки ...
    
    $_SESSION['message'] = "Тест успешно добавлен";
    header("Location: admin_tests.php");
    exit;
}

// Получение данных
$tests = $pdo->query("SELECT t.*, d.name as direction_name FROM tests t 
                     LEFT JOIN directions d ON t.direction_id = d.id 
                     ORDER BY t.created_at DESC")->fetchAll();

$directions = $pdo->query("SELECT * FROM directions")->fetchAll();

// Буферизация вывода
ob_start();
?>
<!DOCTYPE html>
<html lang="ru">
<!-- остальная часть HTML -->