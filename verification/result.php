<?php
session_start();
require_once '../admin/config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user']['id'];
$userTestId = isset($_GET['user_test_id']) ? (int)$_GET['user_test_id'] : 0;

if (!$userTestId) {
    die("Ошибка: попытка теста не найдена.");
}
$stmt = $pdo->prepare("
    SELECT ut.*, t.name, t.chat_link, t.group_link
    FROM user_tests ut
    JOIN tests t ON ut.test_id = t.id
    WHERE ut.id = ? AND ut.user_id = ?");
$stmt->execute([$userTestId, $userId]);
$userTest = $stmt->fetch();

if (!$userTest) {
    die("Попытка теста не найдена или не принадлежит вам.");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <script>
        history.pushState(null, null, location.href);
        window.onpopstate = function() {
        history.go(1);};
    </script>
    <meta charset="UTF-8">
    <title>Результат теста</title>
</head>
<body>
    <h2>Результат теста: <?= htmlspecialchars($userTest['name']) ?></h2>
    <p>Ваш балл: <strong><?= number_format($userTest['score'], 2, ',', ' ') ?> / <?= number_format($userTest['max_score'], 2, ',', ' ') ?></strong></p>


    <?php if ($userTest['passed']): ?>
        <p style="color:green"><strong>Поздравляем! Вы прошли тест.</strong></p>
        <?php if (!empty($userTest['chat_link'])): ?>
            <p><a href="<?= htmlspecialchars($userTest['chat_link']) ?>" target="_blank">Перейти в чат</a></p>
        <?php endif; ?>
        <?php if (!empty($userTest['group_link'])): ?>
            <p><a href="<?= htmlspecialchars($userTest['group_link']) ?>" target="_blank">Перейти в группу</a></p>
        <?php endif; ?>
    <?php else: ?>
        <p style="color:red"><strong>К сожалению, вы не прошли тест.</strong></p>
    <?php endif; ?>

    <p><a href="account.php">Вернуться в личный кабинет</a></p>
</body>
</html>
