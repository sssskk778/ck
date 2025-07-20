<?php
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$result_id = isset($_GET['result_id']) ? (int)$_GET['result_id'] : 0;

// Получаем основную информацию о результате
$result_stmt = $pdo->prepare("SELECT ut.*, l.fio, l.email, t.name as test_name 
                             FROM user_tests ut
                             JOIN listeners l ON ut.user_id = l.id
                             JOIN tests t ON ut.test_id = t.id
                             WHERE ut.id = ?");
$result_stmt->execute([$result_id]);
$result = $result_stmt->fetch();

if (!$result) {
    $_SESSION['error'] = "Результат тестирования не найден";
    header("Location: admin_tests.php");
    exit;
}

// Получаем ответы пользователя
$answers_stmt = $pdo->prepare("SELECT ua.*, q.question_text, a.answer_text, a.is_correct as answer_is_correct
                              FROM user_answers ua
                              LEFT JOIN questions q ON ua.question_id = q.id
                              LEFT JOIN answers a ON ua.answer_id = a.id
                              WHERE ua.user_test_id = ?");
$answers_stmt->execute([$result_id]);
$answers = $answers_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результат тестирования: <?= htmlspecialchars($result['fio']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Результат тестирования</h2>
            <a href="admin_test_results.php?test_id=<?= $result['test_id'] ?>" class="btn btn-secondary">Назад к результатам</a>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                Общая информация
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Тест:</strong> <?= htmlspecialchars($result['test_name']) ?></p>
                        <p><strong>Пользователь:</strong> <?= htmlspecialchars($result['fio']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($result['email']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Баллы:</strong> <?= $result['score'] ?> из <?= $result['max_score'] ?></p>
                        <p><strong>Статус:</strong> 
                            <?php if ($result['passed']): ?>
                                <span class="badge bg-success">Пройден</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Не пройден</span>
                            <?php endif; ?>
                        </p>
                        <p><strong>Дата прохождения:</strong> <?= date('d.m.Y H:i', strtotime($result['completed_at'])) ?></p>
                        <p><strong>Попытка:</strong> <?= $result['attempt_number'] ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                Ответы пользователя
            </div>
            <div class="card-body">
                <?php foreach ($answers as $answer): ?>
                <div class="mb-4 p-3 <?= $answer['is_correct'] ? 'bg-light' : 'bg-danger bg-opacity-10' ?>" style="border-left: 4px solid <?= $answer['is_correct'] ? 'green' : 'red' ?>;">
                    <h5>Вопрос: <?= htmlspecialchars($answer['question_text']) ?></h5>
                    
                    <?php if ($answer['answer_id']): ?>
                        <p><strong>Выбранный ответ:</strong> <?= htmlspecialchars($answer['answer_text']) ?></p>
                        <p><strong>Правильность:</strong> 
                            <?= $answer['is_correct'] ? 
                                '<span class="badge bg-success">Правильно</span>' : 
                                '<span class="badge bg-danger">Неправильно</span>' ?>
                        </p>
                    <?php else: ?>
                        <p><strong>Текстовый ответ:</strong> <?= htmlspecialchars($answer['answer_text']) ?></p>
                        <p><strong>Оценка:</strong> <?= $answer['points'] ?> баллов</p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>