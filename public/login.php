<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему | S-CMS</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="auth-container">
    <h2>Вход в систему</h2>
    
    <form id="loginForm" novalidate>
        <div class="form-group">
            <label for="login">Логин или Email</label>
            <input type="text" id="login" name="login" placeholder="Введите ваш логин" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" placeholder="Введите ваш пароль" required>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">Войти</button>
    </form>

    <div id="errors"></div>

    <div class="auth-link">
        Нет аккаунта? <a href="register.php">Зарегистрироваться</a>
    </div>
</div>

<!-- Подключаем библиотеку валидации из проекта -->
<script src="/public/js/ValidationLibrary.js"></script>
<script src="login.js"></script>
</body>
</html>