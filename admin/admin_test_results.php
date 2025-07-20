<?php
require_once 'config.php';

// Проверка авторизации и прав
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Получаем ID теста из параметров URL
$test_id = isset($_GET['test_id']) ? (int)$_GET['test_id'] : 0;

// Получаем информацию о тесте
$test_stmt = $pdo->prepare("SELECT * FROM tests WHERE id = ?");
$test_stmt->execute([$test_id]);
$test = $test_stmt->fetch();

if (!$test) {
    $_SESSION['error'] = "Тест не найден";
    header("Location: admin_tests.php");
    exit;
}

// Фильтрация и поиск
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$passed_filter = isset($_GET['passed']) ? sanitize($_GET['passed']) : 'all';

// Получаем результаты тестирования
$query = "SELECT ut.*, l.fio, l.email 
          FROM user_tests ut
          JOIN listeners l ON ut.user_id = l.id
          WHERE ut.test_id = ?";

if (!empty($search)) {
    $query .= " AND (l.fio LIKE :search OR l.email LIKE :search)";
}

if ($passed_filter === 'passed') {
    $query .= " AND ut.passed = 1";
} elseif ($passed_filter === 'failed') {
    $query .= " AND ut.passed = 0";
}

$query .= " ORDER BY ut.completed_at DESC";

$stmt = $pdo->prepare($query);

$params = [$test_id];
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
}

$stmt->execute($params);
$results = $stmt->fetchAll();

// Подсчет статистики
$stats_stmt = $pdo->prepare("SELECT 
                            COUNT(*) as total,
                            SUM(passed) as passed,
                            AVG(score) as avg_score
                            FROM user_tests WHERE test_id = ?");
$stats_stmt->execute([$test_id]);
$stats = $stats_stmt->fetch();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результаты тестирования: <?= htmlspecialchars($test['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Результаты теста: <?= htmlspecialchars($test['name']) ?></h2>
            <a href="admin_tests.php" class="btn btn-secondary">Назад к тестам</a>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <!-- Статистика по тесту -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title"><?= $stats['total'] ?></h5>
                        <p class="card-text">Всего попыток</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title"><?= $stats['passed'] ?></h5>
                        <p class="card-text">Успешных попыток</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title"><?= round($stats['avg_score'], 2) ?></h5>
                        <p class="card-text">Средний балл</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Фильтры и поиск -->
        <div class="card mb-4">
            <div class="card-header">
                Фильтры
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <input type="hidden" name="test_id" value="<?= $test_id ?>">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Поиск по ФИО или email" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="passed" class="form-select">
                            <option value="all" <?= $passed_filter === 'all' ? 'selected' : '' ?>>Все результаты</option>
                            <option value="passed" <?= $passed_filter === 'passed' ? 'selected' : '' ?>>Только успешные</option>
                            <option value="failed" <?= $passed_filter === 'failed' ? 'selected' : '' ?>>Только неудачные</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Применить</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Таблица результатов -->
        <div class="card">
            <div class="card-header">
                Список результатов
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="resultsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Пользователь</th>
                                <th>Email</th>
                                <th>Баллы</th>
                                <th>Статус</th>
                                <th>Дата прохождения</th>
                                <th>Попытка</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $result): ?>
                            <tr>
                                <td><?= $result['id'] ?></td>
                                <td><?= htmlspecialchars($result['fio']) ?></td>
                                <td><?= htmlspecialchars($result['email']) ?></td>
                                <td><?= $result['score'] ?> / <?= $result['max_score'] ?></td>
                                <td>
                                    <?php if ($result['passed']): ?>
                                        <span class="badge bg-success">Пройден</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Не пройден</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d.m.Y H:i', strtotime($result['completed_at'])) ?></td>
                                <td><?= $result['attempt_number'] ?></td>
                                <td>
                                    <a href="admin_test_result_detail.php?result_id=<?= $result['id'] ?>" class="btn btn-sm btn-info">Подробнее</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Кнопка экспорта -->
        <div class="mt-3">
            <a href="admin_test_export.php?test_id=<?= $test_id ?>&search=<?= urlencode($search) ?>&passed=<?= $passed_filter ?>" 
               class="btn btn-success">
                Экспорт в Excel
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#resultsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json'
                },
                order: [[5, 'desc']]
            });
        });
    </script>
</body>
</html>