<?php
session_start();
require 'db.php';

// Включим отладку
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = '';
$email = '';
$phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email']));
    $phone = trim($_POST['phone']);

    try {
        $stmt = $pdo->prepare("SELECT id, fio, email, phone FROM listeners WHERE email = ? AND phone = ? LIMIT 1");
        $stmt->execute([$email, $phone]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['fio'],
                'email' => $user['email'],
                'phone' => $user['phone']
            ];
            
            // Определяем базовый URL
            $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            
            // Полный путь к account.php
            $redirect_url = "$base_url/modul/verification/account.php";
            
            // Проверка перед редиректом
            if (headers_sent($file, $line)) {
                die("Редирект невозможен. Заголовки уже отправлены в $file на строке $line");
            }
            
            header("Location: $redirect_url", true, 302);
            exit;
        } else {
            $error = "Неправильный email или телефон.";
        }
    } catch (PDOException $e) {
        $error = "Ошибка базы данных: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f2f4f8; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #fff; padding: 30px 40px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: left; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="email"], input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; width: 100%; }
        button:hover { background: #0056b3; }
        .error { color: #dc3545; margin: 10px 0; padding: 10px; background: #f8d7da; border-radius: 4px; }
        .admin-link { 
            display: block; 
            text-align: center; 
            margin-top: 15px; 
            color: #6c757d; 
            text-decoration: none;
        }
        .admin-link:hover { 
            color: #5a6268;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Авторизация</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="text" id="phone" name="phone" required value="<?= htmlspecialchars($phone) ?>" placeholder="+7(___) ___-__-__">
            </div>

            <button type="submit">Войти</button>
            <a href="../admin/admin_login.php" class="admin-link">Войти как администратор</a>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#phone').inputmask({
                mask: '+7(999) 999-99-99',
                placeholder: '_',
                showMaskOnHover: true,
                showMaskOnFocus: true,
                clearIncomplete: true
            });

            $('form').submit(function(e) {
                var phone = $('#phone').val();
                var phoneRegex = /^\+7\(\d{3}\) \d{3}-\d{2}-\d{2}$/;
                
                if (!phoneRegex.test(phone)) {
                    alert('Некорректно набранный номер');
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>