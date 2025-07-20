<?php
require_once 'config.php';

if (!isset($_SESSION['mfa_required']) || !$_SESSION['mfa_required']) {
    header("Location: admin_login.php");
    exit;
}

// Получаем секрет MFA для администратора
$stmt = $pdo->prepare("SELECT mfa_secret FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

// Обработка ввода кода
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
    $code = sanitize($_POST['code']);
    
    // Проверяем код (здесь должна быть реализация проверки TOTP)
    // В реальном проекте используйте библиотеку для работы с TOTP, например:
    // https://github.com/robthree/twofactorauth
    
    // Временная заглушка для демонстрации
    if (strlen($code) === 6 && is_numeric($code)) {
        unset($_SESSION['mfa_required']);
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "Неверный код подтверждения";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Двухфакторная аутентификация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Двухфакторная аутентификация</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>
                        
                        <p>Введите 6-значный код из приложения аутентификации</p>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="code" class="form-label">Код подтверждения</label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       pattern="\d{6}" maxlength="6" required>
                            </div>
                            <button type="submit" name="verify_code" class="btn btn-primary">Подтвердить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>