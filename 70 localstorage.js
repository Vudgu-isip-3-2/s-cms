// Функция для применения темы
function applyTheme(themeName) {
    // Удаляем все существующие классы тем
    const body = document.body;
    const themes = ['light', 'dark', 'blue', 'green'];
    
    themes.forEach(theme => {
        body.classList.remove(theme);
    });
    
    // Добавляем новый класс темы
    body.classList.add(themeName);
    
    // Сохраняем тему в localStorage
    localStorage.setItem('selectedTheme', themeName);
    
    console.log(`Применена тема: ${themeName}`);
}

// Функция для загрузки сохранённой темы
function loadSavedTheme() {
    // Пытаемся получить сохранённую тему из localStorage
    const savedTheme = localStorage.getItem('selectedTheme');
    
    // Если тема сохранена и существует в списке доступных тем
    if (savedTheme && ['light', 'dark', 'blue', 'green'].includes(savedTheme)) {
        applyTheme(savedTheme);
    } else {
        // Если нет сохранённой темы, применяем тему по умолчанию (light)
        applyTheme('light');
    }
}

// Функция для сброса темы
function resetTheme() {
    // Удаляем сохранённую тему
    localStorage.removeItem('selectedTheme');
    // Применяем тему по умолчанию
    applyTheme('light');
}

// Ждём загрузки DOM
document.addEventListener('DOMContentLoaded', () => {
    // Загружаем сохранённую тему при загрузке страницы
    loadSavedTheme();
    
    // Находим все кнопки выбора темы
    const themeButtons = document.querySelectorAll('.theme-btn');
    
    // Добавляем обработчики событий для кнопок
    themeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const theme = button.getAttribute('data-theme');
            applyTheme(theme);
        });
    });
    
    // Кнопка сброса
    const resetBtn = document.querySelector('.reset-btn');
    if (resetBtn) {
        resetBtn.addEventListener('click', resetTheme);
    }
});
//# 70