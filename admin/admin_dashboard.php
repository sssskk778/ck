<?php
require_once 'config.php';

// Проверка авторизации
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Получаем информацию об администраторе
$admin_id = $_SESSION['admin_id'];
$admin_role = $_SESSION['admin_role'];
$admin_name = $_SESSION['admin_name'];

// Получаем статистику
$users_count = $pdo->query("SELECT COUNT(*) FROM listeners")->fetchColumn();
$directions_count = $pdo->query("SELECT COUNT(*) FROM directions")->fetchColumn();
$documents_count = $pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Цифровая кафедра</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_dashboard.php">Главная</a>
                    </li>
                    <?php if ($admin_role === 'super_admin' || $admin_role === 'user_admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_users.php">Пользователи</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($admin_role === 'super_admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_directions.php">Направления</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_documents.php">Документы</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create_tests.php">Тесты</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_settings.php">Настройки</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <?= htmlspecialchars($admin_name) ?>
                            (<?= $admin_role === 'super_admin' ? 'Главный админ' : ($admin_role === 'user_admin' ? 'Админ пользователей' : 'Модератор') ?>)
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="admin_profile.php">Профиль</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="admin_logout.php">Выйти</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Панель управления</h2>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Пользователи</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $users_count ?></h5>
                        <p class="card-text">Зарегистрированных слушателей</p>
                        <a href="admin_users.php" class="btn btn-light">Управление</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Направления</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $directions_count ?></h5>
                        <p class="card-text">Доступных направлений</p>
                        <a href="admin_directions.php" class="btn btn-light">Управление</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Документы</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $documents_count ?></h5>
                        <p class="card-text">Загруженных документов</p>
                        <a href="admin_documents.php" class="btn btn-light">Управление</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Создание тестов</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $documents_count ?></h5>
                        <p class="card-text"></p>
                        <a href="create_tests.php" class="btn btn-light">Управление</a>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($admin_role === 'super_admin'): ?>
        <div class="card mt-4">
            <div class="card-header">
                Последние действия
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Пользователь</th>
                            <th>Действие</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Здесь будет вывод последних действий из логов -->
                        <tr>
                            <td colspan="4" class="text-center">Логи действий будут здесь</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</body>

</html>