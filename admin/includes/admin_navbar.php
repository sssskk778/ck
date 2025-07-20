<?php
// Обязательно запускаем сессию в начале файла
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php">Цифровая кафедра</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'admin_dashboard.php' ? 'active' : '' ?>"
                        href="admin_dashboard.php">Главная</a>
                </li>
                <?php if (isset($_SESSION['admin_role']) && ($_SESSION['admin_role'] === 'super_admin' || $_SESSION['admin_role'] === 'user_admin')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'admin_users.php' ? 'active' : '' ?>"
                        href="admin_users.php">Пользователи</a>
                </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'admin_directions.php' ? 'active' : '' ?>"
                        href="admin_directions.php">Направления</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'admin_documents.php' ? 'active' : '' ?>"
                        href="admin_documents.php">Документы</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'create_tests.php' ? 'active' : '' ?>"
                        href="create_tests.php">Создание тестов</a>

                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'admin_settings.php' ? 'active' : '' ?>"
                        href="admin_settings.php">Настройки</a>
                </li>

                <?php endif; ?>
            </ul>
            <?php if (isset($_SESSION['admin_name'], $_SESSION['admin_role'])): ?>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown">
                        <?= htmlspecialchars($_SESSION['admin_name']) ?>
                        (<?= $_SESSION['admin_role'] === 'super_admin' ? 'Главный админ' : ($_SESSION['admin_role'] === 'user_admin' ? 'Админ пользователей' : 'Модератор') ?>)
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="admin_profile.php">Профиль</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="admin_logout.php">Выйти</a></li>
                    </ul>
                </li>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>