<?php
/**
 * Страница входа (логина) в S-CMS.
 * Обрабатывает POST-запрос с username/email и password,
 * при успешной авторизации создаёт сессию и перенаправляет на dashboard.
 */

require_once __DIR__ . '/../lib/Config_Class.php';
require_once __DIR__ . '/../lib/DataBase.php';

session_start();

// Если пользователь уже авторизован — сразу на dashboard
if (!empty($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Заполните все поля';
        http_response_code(400);
    } else {
        $config = new Config(dirname(__DIR__) . '/.env');
        $db = DataBase::getInstance(
            $config->get('DB_HOST', 'localhost'),
            $config->get('DB_DATABASE', 'cms_bd'),
            $config->get('DB_USERNAME', 'root'),
            $config->get('DB_PASSWORD', '')
        );

        // Ищем пользователя по логину или email
        $user = $db->queryOne(
            'SELECT * FROM user WHERE username = ? OR email = ? LIMIT 1',
            [$username, $username]
        );

        if ($user && password_verify($password, $user['password_hash'])) {
            // Успешная авторизация — создаём сессию
            $_SESSION['user_id']  = (int) $user['id'];
            $_SESSION['username'] = $user['username'];

            // Определяем роли пользователя
            $roles = $db->query(
                'SELECT r.name FROM role r
                 JOIN user_role ur ON r.id = ur.role_id
                 WHERE ur.user_id = ?',
                [$user['id']]
            );
            $_SESSION['is_admin'] = in_array('admin', array_column($roles, 'name'), true);

            header('Location: /dashboard.php');
            exit;
        }

        $error = 'Неверный логин или пароль';
        http_response_code(401);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | S-CMS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .auth-container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 24px;
            font-size: 24px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-submit:hover { background: #2980b9; }
        .error {
            margin-bottom: 20px;
            padding: 12px;
            background: #fee;
            border-left: 4px solid #c0392b;
            border-radius: 4px;
            color: #c0392b;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="auth-container">
    <h2>Вход в систему</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/login.php">
        <div class="form-group">
            <label for="username">Логин или Email</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn-submit">Войти</button>
    </form>
</div>
</body>
</html>
