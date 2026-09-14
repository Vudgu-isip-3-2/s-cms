<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация | S-CMS</title>
    <style>
        /* Базовые стили, соответствующие духу проекта (см. public/users.php) */
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
        .form-group {
            margin-bottom: 20px;
        }
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
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
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
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background: #2980b9;
        }
        .btn-submit:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }
        #errors {
            margin-top: 20px;
            padding: 15px;
            background: #fee;
            border-left: 4px solid #c0392b;
            border-radius: 4px;
            color: #c0392b;
            font-size: 14px;
            display: none; /* Скрыт по умолчанию */
        }
        #errors ul {
            margin: 0;
            padding-left: 20px;
        }
        .auth-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #7f8c8d;
        }
        .auth-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }
        .auth-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="auth-container">
    <h2>Создать аккаунт</h2>
    
    <form id="registerForm" novalidate>
        <div class="form-group">
            <label for="username">Логин</label>
            <input type="text" id="username" name="username" placeholder="Придумайте логин" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="example@mail.com" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" placeholder="Минимум 6 символов" required>
        </div>

        <div class="form-group">
            <label for="confirmPassword">Подтверждение пароля</label>
            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Повторите пароль" required>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">Зарегистрироваться</button>
    </form>

    <div id="errors"></div>

    <div class="auth-link">
        Уже есть аккаунт? <a href="login.php">Войти</a>
    </div>
</div>

<!-- Подключаем библиотеку валидации из проекта -->
<script src="/public/js/ValidationLibrary.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("registerForm");
    const errorsDiv = document.getElementById("errors");
    const submitBtn = document.getElementById("submitBtn");

    form.addEventListener("submit", function(e) {
        e.preventDefault();
        
        // Очищаем предыдущие ошибки
        errorsDiv.style.display = 'none';
        errorsDiv.innerHTML = '';

        // Собираем данные из полей (используем name атрибуты для удобства отправки)
        const formData = new FormData(form);
        const data = {
            username: formData.get('username').trim(),
            email: formData.get('email').trim(),
            password: formData.get('password'),
            confirmPassword: formData.get('confirmPassword')
        };

        // 1. Клиентская валидация через ValidationLibrary
        const validator = new ValidationLibrary();
        
        const validationResult = validator.validateForm(data, {
            username: [{name: "required", message: "Логин обязателен"}, {name: "minLength", value: 3, message: "Логин должен быть не менее 3 символов"}],
            email: [{name: "required", message: "Email обязателен"}, {name: "email", message: "Некорректный формат email"}],
            password: [{name: "required", message: "Пароль обязателен"}, {name: "minLength", value: 6, message: "Пароль должен быть не менее 6 символов"}],
            confirmPassword: [{name: "required", message: "Подтвердите пароль"}]
        });

        // 2. Проверка совпадения паролей (специфичное правило)
        const passMatchError = validator.passwordMatch(data.password, data.confirmPassword);
        if (passMatchError) {
            validationResult.isValid = false;
            validationResult.results.confirmPassword.errors.push(passMatchError);
        }

        // 3. Если есть ошибки валидации, показываем их
        if (validator.hasErrors(validationResult)) {
            const errorsList = validator.getErrorsList(validationResult);
            
            const ul = document.createElement('ul');
            errorsList.forEach(err => {
                const li = document.createElement('li');
                li.textContent = err.message; // ValidationLibrary обычно возвращает объект с message
                ul.appendChild(li);
            });
            
            errorsDiv.appendChild(ul);
            errorsDiv.style.display = 'block';
            return; // Прерываем отправку на сервер
        }

        // 4. Отправка данных на сервер
        submitBtn.disabled = true;
        submitBtn.textContent = 'Регистрация...';

        fetch('/register_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json', // Отправляем как JSON для удобства парсинга на PHP
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Успех: перенаправляем на страницу входа или сразу авторизуем
                window.location.href = result.redirect || '/login.php?registered=1';
            } else {
                // Ошибка от сервера (например, "Такой логин уже занят")
                errorsDiv.textContent = result.message || 'Произошла ошибка при регистрации';
                errorsDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Зарегистрироваться';
            }
        })
        .catch(error => {
            console.error('Ошибка сети:', error);
            errorsDiv.textContent = 'Ошибка сети. Проверьте подключение к интернету.';
            errorsDiv.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Зарегистрироваться';
        });
    });
});
</script>

</body>
</html>