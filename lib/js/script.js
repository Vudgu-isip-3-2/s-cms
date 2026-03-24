/**
 * Класс для генерации динамического меню
 */
class DynamicMenu {
    constructor(containerId, menuData) {
        this.container = document.getElementById(containerId);
        this.data = menuData;

        if (!this.container) {
            console.error(`Контейнер с ID "${containerId}" не найден.`);
            return;
        }

        this.init();
    }

    init() {
        // Очищаем контейнер и создаем корневой список
        this.container.innerHTML = '';
        const menuList = this.createList(this.data);
        this.container.appendChild(menuList);

        // Навешиваем глобальный обработчик кликов (делегирование событий)
        this.container.addEventListener('click', (e) => this.handleMenuClick(e));
    }

    /**
     * Рекурсивная функция создания списка
     * @param {Array} items - Массив объектов меню
     * @returns {HTMLElement} UL элемент
     */
    createList(items) {
        const ul = document.createElement('ul');
        ul.className = 'menu-list'; // Базовый класс

        items.forEach(item => {
            const li = document.createElement('li');
            li.className = 'menu-item';

            // Создаем ссылку/кнопку
            const link = document.createElement('a');
            link.href = item.url || '#';
            link.className = 'menu-link';
            link.dataset.url = item.url; // Для обработки кликов

            // Внутренний контент (иконка + текст)
            const contentSpan = document.createElement('span');
            contentSpan.className = 'link-content';

            if (item.icon) {
                contentSpan.innerHTML = `<span class="icon">${item.icon}</span>`;
            }
            contentSpan.innerHTML += `<span class="text">${item.label}</span>`;

            link.appendChild(contentSpan);

            // Если есть подменю
            if (item.children && item.children.length > 0) {
                // Добавляем стрелочку
                const arrow = document.createElement('span');
                arrow.className = 'arrow';
                arrow.innerHTML = '&#9662;'; // Символ стрелки вниз
                link.appendChild(arrow);

                li.appendChild(link);

                // Рекурсивный вызов для детей
                const subMenu = this.createList(item.children);
                subMenu.style.display = 'none'; // Скрываем подменю по умолчанию
                li.appendChild(subMenu);
            } else {
                li.appendChild(link);
            }

            ul.appendChild(li);
        });

        return ul;
    }

    /**
     * Обработка кликов по меню
     * @param {Event} e 
     */
    handleMenuClick(e) {
        // Ищем ближайший родительский элемент .menu-link
        const link = e.target.closest('.menu-link');

        if (!link) return;

        // Предотвращаем переход по ссылке, если это пункт с подменю
        const hasChildren = link.parentElement.querySelector('ul');
        if (hasChildren) {
            e.preventDefault();
            this.toggleSubMenu(link, hasChildren);
        } else {
            // Логика для обычной ссылки
            this.setActiveLink(link);
        }
    }

    /**
     * Открытие/закрытие подменю (аккордеон)
     */
    toggleSubMenu(link, subMenu) {
        const isExpanded = subMenu.style.display === 'block';

        // Закрываем все соседние подменю (опционально, можно убрать для мульти-открытия)
        const parentUl = link.parentElement.parentElement;
        parentUl.querySelectorAll('.menu-item > ul').forEach(ul => {
            if (ul !== subMenu) ul.style.display = 'none';
        });
        parentUl.querySelectorAll('.arrow').forEach(arrow => {
            if (arrow.parentElement !== link) arrow.style.transform = 'rotate(0deg)';
        });

        // Переключаем текущее
        subMenu.style.display = isExpanded ? 'none' : 'block';

        // Вращаем стрелочку
        const arrow = link.querySelector('.arrow');
        if (arrow) {
            arrow.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
            arrow.style.transition = 'transform 0.3s';
        }
    }

    /**
     * Подсветка активного элемента
     */
    setActiveLink(activeLink) {
        // Убираем класс active у всех ссылок
        this.container.querySelectorAll('.menu-link').forEach(link => {
            link.classList.remove('active');
        });
        // Добавляем текущему
        activeLink.classList.add('active');
    }
}