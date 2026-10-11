<?php
/**
 * Защищённая страница (дашборд) для проверки авторизации.
 */
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | S-CMS</title>
</head>
<body>
    <h1>Добро пожаловать, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    <p>Вы авторизованы.</p>
    <?php if (!empty($_SESSION['is_admin'])): ?>
        <p><a href="/admin/comments">Админ-панель</a></p>
    <?php endif; ?>
    <p><a href="/logout.php">Выйти</a></p>
</body>
</html>
