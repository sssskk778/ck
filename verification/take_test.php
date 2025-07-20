<?php
session_start();
require_once '../admin/config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user']['id'];
$testId = isset($_GET['test_id']) ? (int)$_GET['test_id'] : 0;

if (!$testId) {
    die("Ошибка: тест не найден.");
}

$stmt = $pdo->prepare("SELECT * FROM tests WHERE id = ?");
$stmt->execute([$testId]);
$test = $stmt->fetch();

if (!$test) {
    die("Тест не существует.");
}
// Проверка: есть ли уже успешная попытка
$stmt = $pdo->prepare("
    SELECT id FROM user_tests 
    WHERE user_id = ? AND test_id = ? AND passed = 1 
    ORDER BY completed_at DESC 
    LIMIT 1
");
$stmt->execute([$userId, $testId]);
$passedTest = $stmt->fetch();

if ($passedTest) {
    header("Location: result.php?user_test_id=" . $passedTest['id']);
    exit;
}

// Проверка лимита попыток
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_tests WHERE user_id = ? AND test_id = ?");
$stmt->execute([$userId, $testId]);
$attemptsUsed = $stmt->fetchColumn();






if ($attemptsUsed >= $test['attempts_limit']) {
    echo "<h2>Вы использовали все попытки для прохождения теста.</h2>";
    echo "<p><a href='account.php'>Вернуться в личный кабинет</a></p>";
    exit;
}

// Создание новой попытки каждый раз
$timeLimitSeconds = (int)$test['time_limit'] * 60;
$startedAt = time();
$timeLeft = $timeLimitSeconds;






if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT id, points FROM questions WHERE test_id = ?");
    $stmt->execute([$testId]);
    $questions = $stmt->fetchAll();

    $totalScore = 0;
    $maxScore = 0;

    $pdo->prepare("INSERT INTO user_tests (user_id, test_id, score, max_score, passed, started_at, completed_at) 
                   VALUES (?, ?, 0, 0, 0, NOW(), NOW())")->execute([$userId, $testId]);
    $userTestId = $pdo->lastInsertId();

    foreach ($questions as $question) {
    $qId = $question['id'];
    $maxScore += $question['points'];

    $correctStmt = $pdo->prepare("SELECT id FROM answers WHERE question_id = ? AND is_correct = 1");
    $correctStmt->execute([$qId]);
    $correctIds = $correctStmt->fetchAll(PDO::FETCH_COLUMN);

    $userAnswers = isset($_POST['answers'][$qId]) ? (array)$_POST['answers'][$qId] : [];

    $correctCount = count($correctIds);

    $correctChosen = count(array_intersect($userAnswers, $correctIds));

    if ($correctCount > 0) {
        $scoreForQuestion = ($question['points'] / $correctCount) * $correctChosen;
    } else {
        $scoreForQuestion = 0;
    }

   

    $totalScore += $scoreForQuestion;

    foreach ($userAnswers as $answerId) {
        $isAnswerCorrect = in_array($answerId, $correctIds) ? 1 : 0;
        $pdo->prepare("INSERT INTO user_answers (user_test_id, question_id, answer_id, is_correct, points)
                       VALUES (?, ?, ?, ?, ?)")
            ->execute([$userTestId, $qId, $answerId, $isAnswerCorrect, 0]);
    }
}


    $passingScore = (int) $test['passing_score'];
    $passed = $totalScore >= $passingScore ? 1 : 0;

    $pdo->prepare("UPDATE user_tests SET score = ?, max_score = ?, passed = ?, completed_at = NOW() WHERE id = ?")
        ->execute([$totalScore, $maxScore, $passed, $userTestId]);

    header("Location: result.php?user_test_id=" . $userTestId);
exit;

}

$stmt = $pdo->prepare("SELECT * FROM questions WHERE test_id = ?");
$stmt->execute([$testId]);
$questions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta http-equiv="Cache-Control" content="no-store" />
    <script>
        let userTestId = null; // Установим после отправки

        // Добавим фальшивую запись в историю, чтобы "назад" вызывал popstate
        window.addEventListener('load', function () {
            history.pushState(null, '', window.location.href);
        });

        // Обработка нажатия кнопки "Назад"
        window.addEventListener('popstate', function (e) {
            const confirmLeave = confirm("Вы уверены, что хотите покинуть тест? Ваш прогресс будет зафиксирован и вы перейдете к результатам.");
            if (confirmLeave) {
                // Если тест уже отправлен — перенаправим на результаты
                if (userTestId) {
                    window.location.href = "result.php?user_test_id=" + userTestId;
                } else {
                    // Отправляем форму принудительно
                    document.getElementById('testForm').submit();
                }
            } else {
                // Вернём состояние, чтобы остаться на странице
                history.pushState(null, '', window.location.href);
            }
        });

        // Отлов формы и запоминаем ID попытки после отправки
        document.getElementById('testForm').addEventListener('submit', function (e) {
            // предупреждение при ручной отправке
            const confirmed = confirm("Вы уверены, что хотите отправить тест? После этого изменить ответы будет невозможно.");
            if (!confirmed) {
                e.preventDefault();
            } else {
                // Установка ID попытки после успешной отправки (сервер перекинет сам)
                // Но на случай "назад" до этого — пусть отправка всё равно произойдёт
            }
        });
    </script>


    <meta charset="UTF-8">
    <title><?= htmlspecialchars($test['name']) ?></title>
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

    h2 {
        margin-bottom: 20px;
    }

    .question {
        margin-bottom: 30px;
    }

    .question-title {
        font-weight: bold;
        margin-bottom: 10px;
    }

    .answers label {
        display: block;
        margin-bottom: 5px;
    }

    button {
        padding: 10px 25px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    button:hover {
        background: #0056b3;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2><?= htmlspecialchars($test['name']) ?></h2>
        <p><?= nl2br(htmlspecialchars($test['description'])) ?></p>
        <div id="timer">Осталось времени: <span id="time"></span></div>
        <form method="post" id="testForm">
            <?php foreach ($questions as $q): ?>
            <div class="question">
                <div class="question-title"><?= htmlspecialchars($q['question_text']) ?> (<?= $q['points'] ?> балл(ов))
                </div>
                <div class="answers">
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM answers WHERE question_id = ? ORDER BY `order` ASC");
                    $stmt->execute([$q['id']]);
                    $answers = $stmt->fetchAll();

                    $type = $q['question_type'] === 'multiple' ? 'checkbox' : 'radio';
                    foreach ($answers as $a):
                        $name = "answers[{$q['id']}]";
                        if ($type === 'checkbox') $name .= "[]";
                    ?>
                    <label>
                        <input type="<?= $type ?>" name="<?= $name ?>" value="<?= $a['id'] ?>">
                        <?= htmlspecialchars($a['answer_text']) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>    
        
            <?php endforeach; ?>
            <button type="submit" id="submitBtn">Отправить ответы</button>
        </form>
        <script>
                let timeLeft = <?= $timeLeft ?>; // Время в секундах от PHP

                function formatTime(seconds) {
                    let m = Math.floor(seconds / 60);
                    let s = seconds % 60;
                    return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                }

                function updateTimer() {
                    const timerEl = document.getElementById('time');
                    const submitBtn = document.getElementById('submitBtn');

                    if (timeLeft <= 0) {
                        timerEl.textContent = "00:00";
                        alert("Время на прохождение теста истекло. Тест будет автоматически отправлен.");
                        submitBtn.disabled = true;

                        document.getElementById('testForm').submit();
                    } else {
                        timerEl.textContent = formatTime(timeLeft);
                        timeLeft--;
                        setTimeout(updateTimer, 1000);
                    }
                }

                updateTimer();

                document.getElementById('testForm').addEventListener('submit', function (e) {
                const confirmed = confirm("Вы уверены, что хотите отправить тест? После этого изменить ответы будет невозможно.");
                if (!confirmed) {
                    e.preventDefault(); // Отменить отправку
                }
});

            </script>
    </div>
</body>

</html>  