<?php
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SESSION['admin_role'] !== 'super_admin') {
    header("Location: admin_dashboard.php");
    exit;
}

// Загрузка текущих настроек
$settings = [
    'site_name' => 'Цифровая кафедра',
    'registration_enabled' => true,
    'testing_enabled' => true,
    'chat_enabled' => false
];

// Обработка сохранения настроек
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings['site_name'] = sanitize($_POST['site_name']);
    $settings['registration_enabled'] = isset($_POST['registration_enabled']);
    $settings['testing_enabled'] = isset($_POST['testing_enabled']);
    $settings['chat_enabled'] = isset($_POST['chat_enabled']);
    
    // Здесь должна быть логика сохранения в БД или файл конфигурации
    $_SESSION['message'] = "Настройки успешно сохранены";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Настройки системы</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>Настройки системы</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="site_name" class="form-label">Название сайта</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" 
                               value="<?= htmlspecialchars($settings['site_name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Модули системы</h5>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="registration_enabled" name="registration_enabled" 
                                   <?= $settings['registration_enabled'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="registration_enabled">Регистрация пользователей</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="testing_enabled" name="testing_enabled" 
                                   <?= $settings['testing_enabled'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="testing_enabled">Тестирование</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="chat_enabled" name="chat_enabled" 
                                   <?= $settings['chat_enabled'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="chat_enabled">Чат</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>