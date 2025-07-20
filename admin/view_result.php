<?php
session_start();
require_once 'config.php';

// Проверка авторизации и роли
if (!isset($_SESSION['admin_id']) || !in_array($_SESSION['admin_role'], ['super_admin', 'user_admin'])) {
    header("Location: ../admin_login.php");
    exit;
}
// Получаем user_id из GET
if (!isset($_GET['user_id'])) {
    echo "Пользователь не указан.";
    exit;
}

$userId = (int)$_GET['user_id'];

// Получение информации о пользователе
$stmt = $pdo->prepare("SELECT * FROM listeners WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "Пользователь не найден.";
    exit;
}

ob_start();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Результаты пользователя</title>
    <link rel="stylesheet" href="../style.css"> <!-- подключи CSS, если есть -->
</head>

<body>
    <div class="container">
        

        <div class="detail-card" style="margin-top: 30px;">
            <h3>История прохождения тестов</h3>
            <?php
            $stmt = $pdo->prepare("
                SELECT ut.*, t.name 
                FROM user_tests ut
                JOIN tests t ON ut.test_id = t.id
                WHERE ut.user_id = ?
                ORDER BY ut.completed_at DESC
            ");
            $stmt->execute([$userId]);
            $attempts = $stmt->fetchAll();

            if ($attempts):
            ?>
                <table border="1" cellpadding="8" cellspacing="0">
                    <tr>
                        <th>Название теста</th>
                        <th>Баллы</th>
                        <th>Максимум</th>
                        <th>Статус</th>
                        <th>Дата</th>
                    </tr>
                    <?php foreach ($attempts as $attempt): ?>
                    <tr>
                        <td><?= htmlspecialchars($attempt['name']) ?></td>
                        <td><?= $attempt['score'] ?></td>
                        <td><?= $attempt['max_score'] ?></td>
                        <td style="color: <?= $attempt['passed'] ? 'green' : 'red' ?>">
                            <?= $attempt['passed'] ? 'Пройден' : 'Не пройден' ?>
                        </td>
                        <td><?= date('d.m.Y H:i', strtotime($attempt['completed_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>Пока нет результатов прохождения тестов.</p>
            <?php endif; ?>
        </div>

        <br>
        <a href="admin_users.php" style="
    display: inline-block;
    margin-bottom: 20px;
    padding: 8px 16px;
    background-color: #007bff;;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
">Назад</a>
    </div>
</body>

</html>
<?php ob_end_flush(); ?>
