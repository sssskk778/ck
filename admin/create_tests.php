<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

require_once 'config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_test'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $time_limit = (int)$_POST['time_limit'];
    $passing_score = (int)$_POST['passing_score'];
    $attempts_limit = (int)$_POST['attempts_limit'];
    $is_published = (int)$_POST['is_published'];
    $chat_link = trim($_POST['chat_link']);
    $group_link = trim($_POST['group_link']);

    try {
        $stmt = $pdo->prepare("INSERT INTO tests (name, description, time_limit, passing_score, attempts_limit, is_published, chat_link, group_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $description, $time_limit, $passing_score, $attempts_limit, $is_published, $chat_link, $group_link])) {
            $message = "✅ Тест успешно создан.";
        } else {
            $message = "❌ Ошибка при создании теста.";
        }
    } catch (PDOException $e) {
        $message = "❌ Ошибка: " . $e->getMessage();
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM tests WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: create_tests.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Создание теста</title>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f6f8;
        padding: 40px;
    }

    .container {
        max-width: 800px;
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
    input[type="url"],
    textarea,
    select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        margin-bottom: 20px;
        border-radius: 6px;
        border: 1px solid #ccc;
        box-sizing: border-box;
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

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: #007bff;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f8f8f8;
    }

    a {
        color: #007bff;
        text-decoration: none;
        font-weight: bold;
    }

    a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Создание нового теста</h2>
        

        <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <label>Название теста:</label>
            <input type="text" name="name" required>

            <label>Описание:</label>
            <textarea name="description" required></textarea>

            <label>Время на прохождение (в минутах):</label>
            <input type="number" name="time_limit" required>

            <label>Проходной балл:</label>
            <input type="number" name="passing_score" required>

            <label>Максимальное количество попыток:</label>
            <input type="number" name="attempts_limit" value="1">

            <label>Опубликовать тест?</label>
            <select name="is_published">
                <option value="1">Да</option>
                <option value="0">Нет</option>
            </select>

            <label>Ссылка на чат:</label>
            <input type="url" name="chat_link">

            <label>Ссылка на группу:</label>
            <input type="url" name="group_link">

            <input type="submit" name="create_test" value="Создать тест">
        </form>

        <h3>Список созданных тестов</h3>

        <?php
    $stmt = $pdo->query("SELECT id, name, description, time_limit, passing_score, attempts_limit, is_published FROM tests ORDER BY created_at DESC");
    $tests = $stmt->fetchAll();

    if (count($tests) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Время</th>
                <th>Баллы</th>
                <th>Попытки</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
            <?php foreach ($tests as $row): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= $row['time_limit'] ?> мин</td>
                <td><?= $row['passing_score'] ?></td>
                <td><?= $row['attempts_limit'] ?></td>
                <td><?= $row['is_published'] ? 'Да' : 'Нет' ?></td>
                <td>
                    <a href="add_questions.php?test_id=<?= $row['id'] ?>">Вопросы</a> |
                    <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Удалить тест?');">Удалить</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
        <p>Тесты пока не созданы.</p>
        <?php endif; ?>
        <br>
        <a href="admin_dashboard.php" style="
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