<?php
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Получаем данные текущего администратора
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

// Обработка изменения данных
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    // Проверяем текущий пароль (прямое сравнение без password_verify)
    if (!empty($current_password) && $current_password !== $admin['password']) {
        $_SESSION['error'] = "Текущий пароль неверен";
    } else {
        // Обновляем данные
        $update_data = [
            'name' => $name,
            'email' => $email,
            'id' => $_SESSION['admin_id']
        ];
        
        $query = "UPDATE admins SET name = :name, email = :email";
        
        // Если указан новый пароль
        if (!empty($new_password)) {
            $update_data['password'] = $new_password; // Сохраняем пароль в открытом виде
            $query .= ", password = :password";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($update_data);
        
        $_SESSION['admin_name'] = $name;
        $_SESSION['message'] = "Профиль успешно обновлен";
        header("Location: admin_profile.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль администратора</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>Профиль администратора</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($admin['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($admin['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Текущий пароль (для смены)</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Новый пароль</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                    </div>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>