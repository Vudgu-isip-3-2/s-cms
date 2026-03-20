/**
         * УПРАВЛЕНИЕ МОДАЛЬНЫМИ ОКНАМИ
         */

        // Функция открытия: меняет display на block для конкретного ID
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = "block";
                // Блокируем скролл основной страницы при открытом окне
                document.body.style.overflow = "hidden";
            }
        }

        // Функция закрытия: возвращает display: none
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = "none";
                // Возвращаем скролл
                document.body.style.overflow = "auto";
            }
        }

        // Глобальный слушатель событий клика
        window.onclick = function(event) {
            // Если пользователь кликнул на область .modal (вне контента), закрываем окно
            if (event.target.classList.contains('modal')) {
                event.target.style.display = "none";
                document.body.style.overflow = "auto";
            }
        }

        // Обработка клавиши Esc для удобства (UX)
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(m => m.style.display = 'none');
                document.body.style.overflow = "auto";
            }
        });