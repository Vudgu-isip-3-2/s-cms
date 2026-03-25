const menuData = [
    {
        label: "Главная",           // Текст пункта
        url: "/",                   // Ссылка (опционально)
        icon: "🏠"                  // Иконка (опционально)
    },
    {
        label: "Каталог",
        url: "/catalog",
        icon: "📦",
        children: [                // Вложенные пункты
            {
                label: "Электроника",
                url: "/catalog/electronics",
                children: [
                    { label: "Смартфоны", url: "/catalog/electronics/phones" },
                    { label: "Ноутбуки", url: "/catalog/electronics/laptops" }
                ]
            },
            { label: "Одежда", url: "/catalog/clothing" }
        ]
    },
    {
        label: "Контакты",
        url: "/contacts",
        icon: "📞"
    }
];

/* Базовая структура меню */
.menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
    font-family: sans-serif;
}

.menu-item {
    position: relative;
    border-bottom: 1px solid #eee;
}

.menu-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    text-decoration: none;
    color: #333;
    cursor: pointer;
    transition: background 0.2s;
}

.menu-link:hover {
    background: #f8f9fa;
}

.menu-link.active {
    background: #007bff;
    color: white;
}

/* Контент ссылки */
.link-content {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}

.icon {
    font-size: 1.1em;
}

/* Стрелочка для подменю */
.arrow {
    margin-left: auto;
    font-size: 0.8em;
    transition: transform 0.3s ease;
}

/* Вложенные уровни */
.menu-item > ul {
    padding-left: 24px;
    background: #fafafa;
}

/* Скрытие/показ подменю */
.menu-item > ul[style*="display: none"] + .arrow {
    transform: rotate(0deg);
}

Пример Динамическая загрузка меню с сервера
async function initMenu() {
    try {
        const response = await fetch('/api/menu');
        const menuData = await response.json();
        
        const menu = new DynamicMenu('mainMenu', menuData);
        menu.setActiveByUrl(window.location.pathname);
        
    } catch (error) {
        console.error('Ошибка загрузки меню:', error);
        // Fallback: показать статичное меню
        const fallback = [{ label: "Ошибка загрузки", url: "#" }];
        new DynamicMenu('mainMenu', fallback);
    }
}

initMenu();