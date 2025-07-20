<?php
require_once 'config.php';

// Инициализируем переменную ошибки
$error = null;

// Проверяем метод запроса только если есть данные
if (!empty($_POST)) {
    $email = sanitize($_POST['email'] ?? '');
    $password = sanitize($_POST['password'] ?? '');
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        // Убрали password_verify и сравниваем пароли напрямую
        if ($admin && $password === $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_name'] = $admin['name'];
            
            if (!empty($admin['mfa_secret'])) {
                $_SESSION['mfa_required'] = true;
                header("Location: admin_mfa.php");
                exit;
            }
            
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Неверный email или пароль";
        }
    } catch (PDOException $e) {
        $error = "Ошибка базы данных: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-card {
            margin-top: 5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .admin-btn {
            display: block;
            text-align: center;
            margin-top: 10px;
            padding: 8px;
            background-color: #6c757d;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }
        .admin-btn:hover {
            background-color: #5a6268;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-header text-center bg-primary text-white">
                        <h4><i class="bi bi-shield-lock"></i> Вход в админ-панель</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Пароль</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-box-arrow-in-right"></i> Войти
                                </button>
                            </div>
                        </form>
                          <a href="../verification/login.php" class="admin-link">Войти как пользователь</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Подключение Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Подключение Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>