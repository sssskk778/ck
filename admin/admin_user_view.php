<?php
require_once 'config.php';

// Проверка авторизации и прав
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SESSION['admin_role'] !== 'super_admin' && $_SESSION['admin_role'] !== 'user_admin') {
    header("Location: admin_dashboard.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin_users.php");
    exit;
}

$user_id = (int)$_GET['id'];

// Получаем информацию о пользователе
$stmt = $pdo->prepare("SELECT l.*, d.name as direction_name FROM listeners l LEFT JOIN directions d ON l.direction_id = d.id WHERE l.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['message'] = "Пользователь не найден";
    header("Location: admin_users.php");
    exit;
}

// Получаем результаты тестов пользователя (если есть таблица тестов)
// $tests_stmt = $pdo->prepare("SELECT * FROM user_tests WHERE user_id = ? ORDER BY completed_at DESC");
// $tests_stmt->execute([$user_id]);
// $tests = $tests_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль пользователя</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <h2>Профиль пользователя: <?= htmlspecialchars($user['fio']) ?></h2>
                
                <div class="card mb-4">
                    <div class="card-header">
                        Основная информация
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ФИО:</strong> <?= htmlspecialchars($user['fio']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                                <p><strong>Телефон:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                                <p><strong>Уровень образования:</strong> <?= htmlspecialchars($user['education_level']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Университет:</strong> <?= htmlspecialchars($user['university']) ?></p>
                                <p><strong>Факультет:</strong> <?= htmlspecialchars($user['faculty']) ?></p>
                                <p><strong>Группа:</strong> <?= htmlspecialchars($user['group_name']) ?></p>
                                <p><strong>Направление:</strong> <?= htmlspecialchars($user['direction_name']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        Дополнительная информация
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Специализация:</strong> <?= htmlspecialchars($user['specialization']) ?></p>
                                <p><strong>Дата рождения:</strong> <?= $user['birthday'] ? date('d.m.Y', strtotime($user['birthday'])) : 'Не указана' ?></p>
                                <p><strong>Страна:</strong> <?= htmlspecialchars($user['country']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Год начала обучения:</strong> <?= $user['start_year'] ?? 'Не указан' ?></p>
                                <p><strong>Номер документа:</strong> <?= htmlspecialchars($user['documents_number']) ?></p>
                                <p><strong>Иностранный студент:</strong> <?= $user['is_foreign'] ? 'Да' : 'Нет' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        Статус и действия
                    </div>
                    <div class="card-body">
                        <p class="text-center">
                            <?php if ($user['is_blocked']): ?>
                                <span class="badge bg-danger">Заблокирован</span>
                            <?php else: ?>
                                <span class="badge bg-success">Активен</span>
                            <?php endif; ?>
                        </p>
                        
                        <div class="d-grid gap-2">
                            <?php if ($user['is_blocked']): ?>
                                <form method="POST" action="admin_users.php">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="unblock_user" class="btn btn-success w-100">Разблокировать</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="admin_users.php">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="block_user" class="btn btn-warning w-100">Блокировать</button>
                                </form>
                            <?php endif; ?>
                            
                            <form method="POST" action="admin_users.php">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button type="submit" name="reset_password" class="btn btn-secondary w-100">Сбросить пароль</button>
                            </form>
                            
                            <a href="#" class="btn btn-info w-100">Редактировать</a>
                        </div>
                    </div>
                </div>
                
                <?php if ($user['image']): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        Фото профиля
                    </div>
                    <div class="card-body text-center">
                        <img src="/uploads/<?= htmlspecialchars($user['image']) ?>" alt="Фото профиля" class="img-fluid rounded">
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Раздел с результатами тестов (если есть таблица тестов) -->
        <!--
        <div class="card">
            <div class="card-header">
                Результаты тестов
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Тест</th>
                            <th>Дата прохождения</th>
                            <th>Результат</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tests as $test): ?>
                        <tr>
                            <td><?= htmlspecialchars($test['test_name']) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($test['completed_at'])) ?></td>
                            <td><?= $test['score'] ?> / <?= $test['max_score'] ?></td>
                            <td>
                                <?php if ($test['passed']): ?>
                                    <span class="badge bg-success">Пройден</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Не пройден</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        -->
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>