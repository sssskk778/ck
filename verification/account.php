<?php
ob_start();
session_start();

// Проверяем авторизацию через нашу новую систему
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

$userId = $_SESSION['user']['id'];

// Получаем данные пользователя из таблицы listeners
$stmt = $pdo->prepare("
    SELECT 
        fio, 
        email, 
        phone, 
        university, 
        faculty, 
        group_name, 
        documents_number,
        specialization,
        image
    FROM listeners 
    WHERE id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: login.php");
    exit;
}

// Подготовка данных для отображения
$avatar = $user['image'] ?? 'default-avatar.jpg';
$full_name = $user['fio'];
$university = $user['university'] ?? '—';
$faculty = $user['faculty'] ?? '—';
$group = $user['group_name'] ?? '—';
$documents = $user['documents_number'] ?? '—';
$specialization = $user['specialization'] ?? '—';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Личный кабинет слушателя</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 20px;
    }

    .container {
        max-width: 800px;
        margin: 20px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
    }

    .avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 30px;
        border: 3px solid #007bff;
    }

    .profile-info h2 {
        margin: 0 0 10px;
        color: #333;
    }

    .profile-info p {
        margin: 5px 0;
        color: #666;
    }

    .details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 30px;
    }

    .detail-card {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #007bff;
    }

    .detail-card h3 {
        margin-top: 0;
        color: #007bff;
    }

    .logout-btn {
        display: inline-block;
        margin-top: 30px;
        padding: 10px 20px;
        background: #dc3545;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
    }

    .logout-btn:hover {
        background: #c82333;
    }
    </style>
</head>

<body>
    
    <div class="container">
        <div class="detail-card" style="margin-top: 30px;">
            <p>
        Добро пожаловать на обучающую платформу! Ниже вы найдёте назначенные вам тесты. <br>
        Перед началом рекомендуем внимательно ознакомиться с инструкциями и рекомендациями, приведёнными в этом видео.<br><br>

        Успехов в обучении и прохождении тестов!
    </p>
    <h3>Видео-приветствие 1</h3>
    <div style="margin-bottom: 15px;">
        <video width="100%" height="auto" controls poster="" preload="auto">
            <source src="Lake.mp4" type="video/mp4">
            Ваш браузер не поддерживает воспроизведение видео.
        </video>
    </div>

    
    <h3>Видео-приветствие 2</h3>
    <div style="margin-bottom: 15px;">
        <video width="100%" height="auto" controls poster="" preload="auto">
            <source src="Lake.mp4" type="video/mp4">
            Ваш браузер не поддерживает воспроизведение видео.
        </video>
    </div>
    
</div>

        <div class="profile-header">
            <img src="<?= htmlspecialchars($avatar) ?>" alt="Аватар" class="avatar">
            <div class="profile-info">
                <h2><?= htmlspecialchars($full_name) ?></h2>
                <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
                <p><strong>Телефон:</strong> <?= htmlspecialchars($_SESSION['user']['phone']) ?></p>
            </div>
        </div>

        <div class="details">
            <div class="detail-card">
                <h3>Образование</h3>
                <p><strong>Университет:</strong> <?= htmlspecialchars($university) ?></p>
                <p><strong>Факультет:</strong> <?= htmlspecialchars($faculty) ?></p>
                <p><strong>Группа:</strong> <?= htmlspecialchars($group) ?></p>
            </div>

            <div class="detail-card">
                <h3>Документы</h3>
                <p><strong>Номер документа:</strong> <?= htmlspecialchars($documents) ?></p>
                <p><strong>Специализация:</strong> <?= htmlspecialchars($specialization) ?></p>
            </div>
        </div>

<?php
// Получаем все активные назначенные тесты (у которых срок сдачи еще не истек)
$stmt = $pdo->prepare("
    SELECT at.*, t.name, t.description, t.passing_score, t.time_limit, t.attempts_limit
    FROM assigned_tests at
    JOIN tests t ON at.test_id = t.id
    WHERE at.listener_id = ? AND at.due_date >= CURDATE()
    ORDER BY at.due_date ASC
");
$stmt->execute([$userId]);
$assigned_tests = $stmt->fetchAll();

if (!empty($assigned_tests)): ?>
    <div class="detail-card" style="margin-top: 30px;">
        <h3>Назначенные тесты (<?= count($assigned_tests) ?>)</h3>
        
        <?php foreach ($assigned_tests as $test): 
            $now = time();
            $dueDateTimestamp = strtotime($test['due_date']);
            $daysLeft = ceil(($dueDateTimestamp - $now) / 86400);
            
            // Считаем использованные попытки
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_tests WHERE user_id = ? AND test_id = ?");
            $stmt->execute([$userId, $test['test_id']]);
            $usedAttempts = (int)$stmt->fetchColumn();
            $attemptsLeft = $test['attempts_limit'] > 0 ? max(0, $test['attempts_limit'] - $usedAttempts) : '∞';
        ?>
            <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                <p><strong>Название:</strong> <?= htmlspecialchars($test['name']) ?></p>
                <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($test['description'])) ?></p>
                <p><strong>Срок сдачи:</strong> <?= date('d.m.Y', strtotime($test['due_date'])) ?> 
                    (осталось дней: <?= $daysLeft ?>)</p>
                <p><strong>Минимальный балл:</strong> <?= $test['passing_score'] ?></p>
                <p><strong>Время на выполнение:</strong> <?= $test['time_limit'] ?> минут</p>
                <p><strong>Осталось попыток:</strong> <?= $attemptsLeft ?></p>
                
                <?php
                // Проверяем, есть ли успешное прохождение теста
                $stmt = $pdo->prepare("SELECT id FROM user_tests WHERE user_id = ? AND test_id = ? AND passed = 1 ORDER BY completed_at DESC LIMIT 1");
                $stmt->execute([$userId, $test['test_id']]);
                $passedTest = $stmt->fetch();
                ?>

                <?php if ($passedTest): ?>
                    <a href="result.php?user_test_id=<?= $passedTest['id'] ?>" class="btn btn-success"
                        style="padding: 8px 15px; background: #28a745; color: white; border-radius: 5px; display: inline-block; margin-top: 5px;">
                        Посмотреть результат
                    </a>
                <?php else: ?>
                    <a href="take_test.php?test_id=<?= $test['test_id'] ?>" class="btn btn-primary"
                        style="padding: 8px 15px; background: #007bff; color: white; border-radius: 5px; display: inline-block; margin-top: 5px;">
                        Пройти тест
                    </a>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p><strong>На данный момент вам не назначено активных тестов.</strong></p>
<?php endif; ?>
<a href="logout.php" class="logout-btn">Выйти из системы</a>

</body>

</html>
<?php ob_end_flush(); ?>