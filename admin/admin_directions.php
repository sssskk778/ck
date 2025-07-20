<?php
require_once 'config.php';

// Проверка авторизации и прав
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SESSION['admin_role'] !== 'super_admin') {
    header("Location: admin_dashboard.php");
    exit;
}

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_direction'])) {
        $name = sanitize($_POST['name']);
        $short_name = sanitize($_POST['short_name']);
        $program = sanitize($_POST['program']);
        $qualification = sanitize($_POST['qualification']);
        $target_audience = sanitize($_POST['target_audience']);
        $order = (int)$_POST['order'];
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        $for_it = isset($_POST['for_it']) ? 1 : 0;
        
        $stmt = $pdo->prepare("INSERT INTO directions (name, short_name, program, qualification, target_audience, `order`, is_published, for_it, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$name, $short_name, $program, $qualification, $target_audience, $order, $is_published, $for_it]);
        
        $_SESSION['message'] = "Направление успешно добавлено";
        header("Location: admin_directions.php");
        exit;
    }
    
    if (isset($_POST['edit_direction'])) {
        $id = (int)$_POST['id'];
        $name = sanitize($_POST['name']);
        $short_name = sanitize($_POST['short_name']);
        $program = sanitize($_POST['program']);
        $qualification = sanitize($_POST['qualification']);
        $target_audience = sanitize($_POST['target_audience']);
        $order = (int)$_POST['order'];
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        $for_it = isset($_POST['for_it']) ? 1 : 0;
        
        $stmt = $pdo->prepare("UPDATE directions SET name = ?, short_name = ?, program = ?, qualification = ?, target_audience = ?, `order` = ?, is_published = ?, for_it = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$name, $short_name, $program, $qualification, $target_audience, $order, $is_published, $for_it, $id]);
        
        $_SESSION['message'] = "Направление успешно обновлено";
        header("Location: admin_directions.php");
        exit;
    }
    
    if (isset($_POST['delete_direction'])) {
        $id = (int)$_POST['id'];
        
        // Проверяем, есть ли пользователи на этом направлении
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM listeners WHERE direction_id = ?");
        $check_stmt->execute([$id]);
        $users_count = $check_stmt->fetchColumn();
        
        if ($users_count > 0) {
            $_SESSION['error'] = "Нельзя удалить направление, так как есть пользователи, привязанные к нему";
        } else {
            $stmt = $pdo->prepare("DELETE FROM directions WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['message'] = "Направление успешно удалено";
        }
        
        header("Location: admin_directions.php");
        exit;
    }
}

// Получаем список направлений
$stmt = $pdo->query("SELECT * FROM directions ORDER BY `order`, name");
$directions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление направлениями</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>Управление направлениями</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header">
                Добавить новое направление
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Название направления</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-3">
                            <label for="short_name" class="form-label">Короткое название</label>
                            <input type="text" class="form-control" id="short_name" name="short_name" maxlength="40" required>
                        </div>
                        <div class="col-md-3">
                            <label for="order" class="form-label">Порядок сортировки</label>
                            <input type="number" class="form-control" id="order" name="order" min="1" required>
                        </div>
                        <div class="col-md-12">
                            <label for="program" class="form-label">Программа обучения</label>
                            <textarea class="form-control" id="program" name="program" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="qualification" class="form-label">Квалификация</label>
                            <input type="text" class="form-control" id="qualification" name="qualification" required>
                        </div>
                        <div class="col-md-6">
                            <label for="target_audience" class="form-label">Целевая аудитория</label>
                            <input type="text" class="form-control" id="target_audience" name="target_audience" required>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_published" name="is_published" checked>
                                <label class="form-check-label" for="is_published">Опубликовано</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="for_it" name="for_it">
                                <label class="form-check-label" for="for_it">Для IT-специальностей</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="add_direction" class="btn btn-primary">Добавить направление</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                Список направлений
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="directionsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Короткое название</th>
                                <th>Квалификация</th>
                                <th>Порядок</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($directions as $direction): ?>
                            <tr>
                                <td><?= $direction['id'] ?></td>
                                <td><?= htmlspecialchars($direction['name']) ?></td>
                                <td><?= htmlspecialchars($direction['short_name']) ?></td>
                                <td><?= htmlspecialchars($direction['qualification']) ?></td>
                                <td><?= $direction['order'] ?></td>
                                <td>
                                    <?php if ($direction['is_published']): ?>
                                        <span class="badge bg-success">Опубликовано</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Скрыто</span>
                                    <?php endif; ?>
                                    <?php if ($direction['for_it']): ?>
                                        <span class="badge bg-info">Для IT</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal<?= $direction['id'] ?>">
                                            Просмотр
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $direction['id'] ?>">
                                            Редактировать
                                        </button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $direction['id'] ?>">
                                            <button type="submit" name="delete_direction" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены, что хотите удалить это направление?')">
                                                Удалить
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Модальное окно просмотра -->
                                    <div class="modal fade" id="viewModal<?= $direction['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"><?= htmlspecialchars($direction['name']) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Короткое название:</strong> <?= htmlspecialchars($direction['short_name']) ?></p>
                                                    <p><strong>Квалификация:</strong> <?= htmlspecialchars($direction['qualification']) ?></p>
                                                    <p><strong>Целевая аудитория:</strong> <?= htmlspecialchars($direction['target_audience']) ?></p>
                                                    <p><strong>Порядок сортировки:</strong> <?= $direction['order'] ?></p>
                                                    <p><strong>Статус:</strong> 
                                                        <?= $direction['is_published'] ? 'Опубликовано' : 'Скрыто' ?>
                                                        <?= $direction['for_it'] ? ' (Для IT-специальностей)' : '' ?>
                                                    </p>
                                                    <hr>
                                                    <h6>Программа обучения:</h6>
                                                    <div style="white-space: pre-line;"><?= htmlspecialchars($direction['program']) ?></div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Модальное окно редактирования -->
                                    <div class="modal fade" id="editModal<?= $direction['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Редактирование направления</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?= $direction['id'] ?>">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label for="edit_name_<?= $direction['id'] ?>" class="form-label">Название направления</label>
                                                                <input type="text" class="form-control" id="edit_name_<?= $direction['id'] ?>" name="name" value="<?= htmlspecialchars($direction['name']) ?>" required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="edit_short_name_<?= $direction['id'] ?>" class="form-label">Короткое название</label>
                                                                <input type="text" class="form-control" id="edit_short_name_<?= $direction['id'] ?>" name="short_name" value="<?= htmlspecialchars($direction['short_name']) ?>" maxlength="40" required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="edit_order_<?= $direction['id'] ?>" class="form-label">Порядок сортировки</label>
                                                                <input type="number" class="form-control" id="edit_order_<?= $direction['id'] ?>" name="order" value="<?= $direction['order'] ?>" min="1" required>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <label for="edit_program_<?= $direction['id'] ?>" class="form-label">Программа обучения</label>
                                                                <textarea class="form-control" id="edit_program_<?= $direction['id'] ?>" name="program" rows="3" required><?= htmlspecialchars($direction['program']) ?></textarea>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="edit_qualification_<?= $direction['id'] ?>" class="form-label">Квалификация</label>
                                                                <input type="text" class="form-control" id="edit_qualification_<?= $direction['id'] ?>" name="qualification" value="<?= htmlspecialchars($direction['qualification']) ?>" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="edit_target_audience_<?= $direction['id'] ?>" class="form-label">Целевая аудитория</label>
                                                                <input type="text" class="form-control" id="edit_target_audience_<?= $direction['id'] ?>" name="target_audience" value="<?= htmlspecialchars($direction['target_audience']) ?>" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="edit_is_published_<?= $direction['id'] ?>" name="is_published" <?= $direction['is_published'] ? 'checked' : '' ?>>
                                                                    <label class="form-check-label" for="edit_is_published_<?= $direction['id'] ?>">Опубликовано</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="edit_for_it_<?= $direction['id'] ?>" name="for_it" <?= $direction['for_it'] ? 'checked' : '' ?>>
                                                                    <label class="form-check-label" for="edit_for_it_<?= $direction['id'] ?>">Для IT-специальностей</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                                        <button type="submit" name="edit_direction" class="btn btn-primary">Сохранить изменения</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
            $('#directionsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json'
                },
                order: [[4, 'asc']]
            });
        });
    </script>
</body>
</html>