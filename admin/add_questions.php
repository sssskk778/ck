<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['test_id'])) {
    echo "Ошибка: отсутствует ID теста.";
    exit();
}

$test_id = (int)$_GET['test_id'];
$message = "";

// Получение информации о тесте
$stmt = $pdo->prepare("SELECT name FROM tests WHERE id = ?");
$stmt->execute([$test_id]);
$test = $stmt->fetch();

// Добавление вопроса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $question_text = trim($_POST['question_text']);
    $question_type = $_POST['question_type'];
    $points = (int)$_POST['points'];

    $pdo->prepare("INSERT INTO questions (test_id, question_text, question_type, points) VALUES (?, ?, ?, ?)")
        ->execute([$test_id, $question_text, $question_type, $points]);

    $question_id = $pdo->lastInsertId();

    for ($i = 0; $i < 4; $i++) {
        $text = trim($_POST['answers'][$i]);
        $is_correct = isset($_POST['correct'][$i]) ? 1 : 0;

        if (!empty($text)) {
            $pdo->prepare("INSERT INTO answers (question_id, answer_text, is_correct, `order`) VALUES (?, ?, ?, ?)")
                ->execute([$question_id, $text, $is_correct, $i]);
        }
    }

    $message = "✅ Вопрос успешно добавлен.";
}
// Удаление вопроса
if (isset($_GET['delete_question'])) {
    $qid = (int)$_GET['delete_question'];
    $pdo->prepare("DELETE FROM answers WHERE question_id = ?")->execute([$qid]);
    $pdo->prepare("DELETE FROM questions WHERE id = ?")->execute([$qid]);
    header("Location: add_questions.php?test_id=$test_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Добавление вопросов</title>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f6f8;
        padding: 40px;
    }

    .container {
        max-width: 850px;
        margin: auto;
        background: white;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2,
    h3 {
        color: #333;
    }

    label {
        font-weight: bold;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        margin-bottom: 15px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    input[type="submit"] {
        background-color: #007bff;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
    }

    input[type="submit"]:hover {
        background-color: #0056b3;
    }

    .message {
        margin-bottom: 20px;
        padding: 12px;
        background-color: #e9ffe8;
        border: 1px solid #b6f2b6;
        color: #236a23;
        border-radius: 6px;
    }

    .question-block {
        background-color: #f9f9f9;
        margin-bottom: 20px;
        padding: 15px;
        border-left: 5px solid #007bff;
        border-radius: 6px;
    }

    .answer {
        margin-left: 20px;
    }

    a {
        color: #dc3545;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Добавление вопросов к тесту №<?= $test_id ?> <?= $test ? "({$test['name']})" : "" ?></h2>

        <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Текст вопроса:</label>
            <textarea name="question_text" required></textarea>

            <label>Тип вопроса:</label>
            <select name="question_type" required>
                <option value="single">Один правильный ответ</option>
                <option value="multiple">Несколько правильных ответов</option>
            </select>

            <label>Баллы за вопрос:</label>
            <input type="number" name="points" value="1" min="1" required>

            <label>Варианты ответов:</label>
            <?php for ($i = 0; $i < 4; $i++): ?>
            <input type="text" name="answers[]" placeholder="Ответ <?= $i + 1 ?>"><br>
            <label><input type="checkbox" name="correct[<?= $i ?>]"> Правильный</label><br><br>
            <?php endfor; ?>

            <input type="submit" name="add_question" value="Добавить вопрос">
        </form>

        <hr>
        <h3>Существующие вопросы</h3>

        <?php
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE test_id = ? ORDER BY id DESC");
    $stmt->execute([$test_id]);
    $questions = $stmt->fetchAll();

    if ($questions):
        foreach ($questions as $q):
            $stmt = $pdo->prepare("SELECT * FROM answers WHERE question_id = ? ORDER BY `order` ASC");
            $stmt->execute([$q['id']]);
            $answers = $stmt->fetchAll();
    ?>
        <div class="question-block">
            <strong><?= htmlspecialchars($q['question_text']) ?></strong>
            <small>(<?= $q['question_type'] ?>, <?= $q['points'] ?> балл(ов))</small><br>
            <ul>
                <?php foreach ($answers as $a): ?>
                <li class="answer"><?= htmlspecialchars($a['answer_text']) ?>
                    <?= $a['is_correct'] ? '<strong style="color: green;"> (правильный)</strong>' : '' ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <a href="?test_id=<?= $test_id ?>&delete_question=<?= $q['id'] ?>"
                onclick="return confirm('Удалить вопрос?')">Удалить</a>
        </div>
        
        <?php
        endforeach;
    else:
        echo "<p>Вопросы еще не добавлены.</p>";
    endif;
    ?>
    <br>
        <a href="create_tests.php" style="
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