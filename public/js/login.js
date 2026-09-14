document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("loginForm");
    const errorsDiv = document.getElementById("errors");
    const submitBtn = document.getElementById("submitBtn");

    form.addEventListener("submit", function(e) {
        e.preventDefault();
        
        // 1. Очищаем предыдущие ошибки
        errorsDiv.style.display = 'none';
        errorsDiv.innerHTML = '';

        // 2. Собираем данные из полей
        const formData = new FormData(form);
        const data = {
            login: formData.get('login').trim(),
            password: formData.get('password')
        };

        // 3. Клиентская валидация через ValidationLibrary
        const validator = new ValidationLibrary();
        
        const validationResult = validator.validateForm(data, {
            login: [{name: "required", message: "Поле логин должно быть заполнено"}],
            password: [{name: "required", message: "Поле пароль должно быть заполнено"}]
        });

        // 4. Если есть ошибки валидации, показываем их
        if (validator.hasErrors(validationResult)) {
            const errorsList = validator.getErrorsList(validationResult);
            
            const ul = document.createElement('ul');
            errorsList.forEach(err => {
                const li = document.createElement('li');
                // Библиотека может возвращать объект с message или строку
                li.textContent = err.message || err; 
                ul.appendChild(li);
            });
            
            errorsDiv.appendChild(ul);
            errorsDiv.style.display = 'block';
            return; // Прерываем отправку на сервер, пока поля не заполнены
        }

        // 5. Отправка данных на сервер (задача #56)
        submitBtn.disabled = true;
        submitBtn.textContent = 'Вход...';

        fetch('/login_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Успех: перенаправляем на главную или в личный кабинет
                window.location.href = result.redirect || '/';
            } else {
                // Ошибка от сервера (например, "Неверный логин или пароль")
                errorsDiv.textContent = result.message || 'Произошла ошибка при входе';
                errorsDiv.style.display = 'block';
                
                // Возвращаем кнопку в исходное состояние
                submitBtn.disabled = false;
                submitBtn.textContent = 'Войти';
            }
        })
        .catch(error => {
            console.error('Ошибка сети:', error);
            errorsDiv.textContent = 'Ошибка сети. Проверьте подключение к интернету.';
            errorsDiv.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Войти';
        });
    });
});