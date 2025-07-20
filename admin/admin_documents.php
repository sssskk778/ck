<?php
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SESSION['admin_role'] !== 'super_admin') {
    header("Location: admin_dashboard.php");
    exit;
}

// Обработка добавления документа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_document'])) {
    $category_id = (int)$_POST['category_id'];
    $name = sanitize($_POST['name']);
    
    if ($_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/documents/';
        $file_name = basename($_FILES['document']['name']);
        $file_path = $upload_dir . uniqid() . '_' . $file_name;
        
        if (move_uploaded_file($_FILES['document']['tmp_name'], $file_path)) {
            $stmt = $pdo->prepare("INSERT INTO documents (category_id, name, filename, is_published, created_at, updated_at) 
                                  VALUES (?, ?, ?, 1, NOW(), NOW())");
            $stmt->execute([$category_id, $name, $file_path]);
            $_SESSION['message'] = "Документ успешно добавлен";
        } else {
            $_SESSION['error'] = "Ошибка загрузки файла";
        }
    }
}

// Обработка удаления документа
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT filename FROM documents WHERE id = ?");
    $stmt->execute([$id]);
    $document = $stmt->fetch();
    
    if ($document && file_exists($document['filename'])) {
        unlink($document['filename']);
    }
    
    $pdo->prepare("DELETE FROM documents WHERE id = ?")->execute([$id]);
    $_SESSION['message'] = "Документ удален";
    header("Location: admin_documents.php");
    exit;
}

// Получаем список документов с категориями
$stmt = $pdo->query("SELECT d.*, c.name as category_name FROM documents d 
                    LEFT JOIN categories c ON d.category_id = c.id 
                    ORDER BY d.created_at DESC");
$documents = $stmt->fetchAll();

// Получаем список категорий
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление документами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>Управление документами</h2>
        
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
                Добавить новый документ
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Категория</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Название документа</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Файл документа</label>
                        <input type="file" class="form-control" id="document" name="document" required>
                    </div>
                    <button type="submit" name="add_document" class="btn btn-primary">Добавить</button>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                Список документов
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Категория</th>
                            <th>Файл</th>
                            <th>Дата добавления</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $document): ?>
                        <tr>
                            <td><?= $document['id'] ?></td>
                            <td><?= htmlspecialchars($document['name']) ?></td>
                            <td><?= htmlspecialchars($document['category_name']) ?></td>
                            <td>
                                <a href="<?= htmlspecialchars($document['filename']) ?>" target="_blank">Скачать</a>
                            </td>
                            <td><?= date('d.m.Y H:i', strtotime($document['created_at'])) ?></td>
                            <td>
                                <a href="admin_documents.php?delete=<?= $document['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Вы уверены?')">Удалить</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>