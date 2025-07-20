<?php
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SESSION['admin_role'] !== 'super_admin' && $_SESSION['admin_role'] !== 'user_admin') {
    header("Location: admin_dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['block_user'])) {
        $user_id = (int)$_POST['user_id'];
        $stmt = $pdo->prepare("UPDATE listeners SET is_blocked = 1 WHERE id = ?");
        $stmt->execute([$user_id]);
        $_SESSION['message'] = "Пользователь успешно заблокирован";
    }

    if (isset($_POST['unblock_user'])) {
        $user_id = (int)$_POST['user_id'];
        $stmt = $pdo->prepare("UPDATE listeners SET is_blocked = 0 WHERE id = ?");
        $stmt->execute([$user_id]);
        $_SESSION['message'] = "Пользователь успешно разблокирован";
    }

    if (isset($_POST['reset_password'])) {
        $user_id = (int)$_POST['user_id'];
        $temp_password = bin2hex(random_bytes(8));
        $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE listeners SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);
        $_SESSION['message'] = "Пароль сброшен. Временный пароль: " . $temp_password;
    }

   if (isset($_POST['assign_test'])) {
    $user_id = (int)$_POST['user_id'];
    $test_id = (int)$_POST['test_id'];
    $due_date = $_POST['due_date'];
    
    // Проверяем, что выбранная дата не в прошлом
    $today = date('Y-m-d');
    if ($due_date < $today) {
        $_SESSION['message'] = "Ошибка: нельзя назначить тест на прошедшую дату";
    } else {
        // Проверяем, не назначен ли уже этот тест пользователю
        $stmt = $pdo->prepare("SELECT id FROM assigned_tests WHERE listener_id = ? AND test_id = ?");
        $stmt->execute([$user_id, $test_id]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "Этот тест уже назначен пользователю.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO assigned_tests (listener_id, test_id, due_date) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $test_id, $due_date]);
            $_SESSION['message'] = "Тест успешно назначен пользователю.";
        }
    }
}
        // НОВЫЙ ОБРАБОТЧИК ДЛЯ ОТМЕНЫ НАЗНАЧЕНИЯ ТЕСТА
    if (isset($_POST['unassign_test'])) {
        $user_id = (int)$_POST['user_id'];
        $test_id = (int)$_POST['test_id'];
        
        $stmt = $pdo->prepare("DELETE FROM assigned_tests WHERE listener_id = ? AND test_id = ?");
        $stmt->execute([$user_id, $test_id]);
        $_SESSION['message'] = "Назначение теста отменено.";
}
}

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'all';

$query = "SELECT l.*, d.name as direction_name FROM listeners l LEFT JOIN directions d ON l.direction_id = d.id WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (l.fio LIKE :search OR l.email LIKE :search OR l.phone LIKE :search)";
}

if ($filter === 'blocked') {
    $query .= " AND l.is_blocked = 1";
} elseif ($filter === 'active') {
    $query .= " AND l.is_blocked = 0";
}

$query .= " ORDER BY l.created_at DESC";

$stmt = $pdo->prepare($query);

if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
}

$stmt->execute();
$users = $stmt->fetchAll();
$assigned_tests_raw = $pdo->query("
    SELECT at.listener_id, at.test_id, at.due_date, t.name, t.attempts_limit
    FROM assigned_tests at
    LEFT JOIN tests t ON at.test_id = t.id
")->fetchAll(PDO::FETCH_ASSOC);

// Для каждого назначенного теста считаем, сколько попыток использовано
foreach ($assigned_tests_raw as &$test) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_tests WHERE user_id = ? AND test_id = ?");
    $stmt->execute([$test['listener_id'], $test['test_id']]);
    $test['attempts_used'] = (int)$stmt->fetchColumn();
}
unset($test);

// Группируем по пользователям
$assigned_tests = [];
foreach ($assigned_tests_raw as $test) {
    $assigned_tests[$test['listener_id']][] = $test;
}

$tests = $pdo->query("SELECT id, name FROM tests ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Управление пользователями</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/admin_navbar.php'; ?>

    <div class="container mt-4">
        <h2>Управление пользователями</h2>

        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">Фильтры и поиск</div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control"
                            placeholder="Поиск по ФИО, email или телефону" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="filter" class="form-select">
                            <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Все пользователи</option>
                            <option value="active" <?= $filter === 'active' ? 'selected' : '' ?>>Активные</option>
                            <option value="blocked" <?= $filter === 'blocked' ? 'selected' : '' ?>>Заблокированные
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Применить</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Список пользователей</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="usersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ФИО</th>
                                <th>Email</th>
                                <th>Телефон</th>
                                <th>Направление</th>
                                <th>Дата регистрации</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['fio']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone']) ?></td>
                                <td><?= $user['direction_name'] ? htmlspecialchars($user['direction_name']) : 'Не указано' ?>
                                </td>
                                <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <?php if ($user['is_blocked']): ?>
                                    <span class="badge bg-danger">Заблокирован</span>
                                    <?php else: ?>
                                    <span class="badge bg-success">Активен</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- НОВЫЙ БЛОК ДЛЯ ОТОБРАЖЕНИЯ НАЗНАЧЕННЫХ ТЕСТОВ -->
                                    <?php if (!empty($assigned_tests[$user['id']])): ?>
                                        <div class="mb-2">
                                            <strong>Назначенные тесты:</strong>
                                            <ul class="list-unstyled mb-1">
                                                <?php foreach ($assigned_tests[$user['id']] as $test): ?>
                                                    <li class="d-flex justify-content-between align-items-center">
                                                        <span>
                                                            <?= htmlspecialchars($test['name']) ?> —
                                                            до <?= date('d.m.Y', strtotime($test['due_date'])) ?> —
                                                            Попыток: <?= max(0, $test['attempts_limit'] - $test['attempts_used']) ?>
                                                        </span>
                                                        <form method="POST" style="display:inline;" 
                                                            onsubmit="return confirm('Вы уверены, что хотите отменить этот тест?');">
                                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                            <input type="hidden" name="test_id" value="<?= $test['test_id'] ?>">
                                                            <button type="submit" name="unassign_test" 
                                                                    class="btn btn-sm btn-outline-danger btn-sm">×</button>
                                                        </form>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    <div class="btn-group mb-1">
                                        <a href="admin_user_view.php?id=<?= $user['id'] ?>"
                                            class="btn btn-sm btn-info">Просмотр</a>
                                        <?php if ($user['is_blocked']): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" name="unblock_user"
                                                class="btn btn-sm btn-success">Разблокировать</button>
                                        </form>
                                        <?php else: ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" name="block_user"
                                                class="btn btn-sm btn-warning">Блокировать</button>
                                        </form>
                                        <?php endif; ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" name="reset_password"
                                                class="btn btn-sm btn-secondary">Сброс пароля</button>
                                        </form>

                                        <a href="view_result.php?user_id=<?= $user['id'] ?>" class="btn btn-sm btn-info">Посмотреть результаты</a>

                                    </div>

                                    <form method="POST" class="d-flex flex-column gap-1">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <select name="test_id" class="form-select form-select-sm" required>
                                            <option value="">Выберите тест</option>
                                            <?php foreach ($tests as $test): ?>
                                            <option value="<?= $test['id'] ?>"><?= htmlspecialchars($test['name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="date" name="due_date" class="form-control form-control-sm"
                                            required>
                                        <button type="submit" name="assign_test"
                                            class="btn btn-sm btn-primary">Назначить тест</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json'
            },
            order: [
                [5, 'desc']
            ]
        });
    });
    </script>
</body>

</html>