/**
 * @file DynamicMenu.js
 * @description Класс для создания динамического многоуровневого меню на основе JSON-данных.
 *              Поддерживает неограниченную вложенность, подсветку активных пунктов
 *              и работу в режиме "аккордеон" для подменю.
 * 
 * @author Ваше Имя
 * @version 1.0.0
 */

class DynamicMenu {
    /**
     * Создает экземпляр динамического меню
     * @param {string} containerId - ID HTML-элемента для рендеринга меню
     * @param {Array} menuData - Массив объектов с данными меню
     * @throws {Error} Если контейнер не найден в DOM
     */
    constructor(containerId, menuData) {
        this.container = document.getElementById(containerId);
        this.data = menuData;
        
        if (!this.container) {
            throw new Error(`Контейнер с ID "${containerId}" не найден в DOM.`);
        }

        this.init();
    }

    /**
     * Инициализирует меню: очищает контейнер и создаёт структуру
     * @private
     */
    init() {
        this.container.innerHTML = '';
        const menuList = this.createList(this.data);
        this.container.appendChild(menuList);
        
        // Делегирование событий для оптимизации
        this.container.addEventListener('click', (e) => this.handleMenuClick(e));
    }

    /**
     * Рекурсивно создаёт HTML-структуру списка меню
     * @param {Array} items - Массив объектов меню текущего уровня
     * @returns {HTMLElement} UL элемент со всем содержимым
     * @private
     */
    createList(items) {
        const ul = document.createElement('ul');
        ul.className = 'menu-list';

        items.forEach(item => {
            const li = document.createElement('li');
            li.className = 'menu-item';

            const link = document.createElement('a');
            link.href = item.url || '#';
            link.className = 'menu-link';
            link.dataset.url = item.url;

            // Контент ссылки: иконка + текст
            const contentSpan = document.createElement('span');
            contentSpan.className = 'link-content';
            
            if (item.icon) {
                const iconSpan = document.createElement('span');
                iconSpan.className = 'icon';
                iconSpan.textContent = item.icon;
                contentSpan.appendChild(iconSpan);
            }
            
            const textSpan = document.createElement('span');
            textSpan.className = 'text';
            textSpan.textContent = item.label;
            contentSpan.appendChild(textSpan);
            
            link.appendChild(contentSpan);

            // Обработка подменю
            if (item.children?.length) {
                const arrow = document.createElement('span');
                arrow.className = 'arrow';
                arrow.innerHTML = '&#9662;';
                link.appendChild(arrow);

                li.appendChild(link);

                // Рекурсивное создание вложенного списка
                const subMenu = this.createList(item.children);
                subMenu.style.display = 'none';
                li.appendChild(subMenu);
            } else {
                li.appendChild(link);
            }

            ul.appendChild(li);
        });

        return ul;
    }

    /**
     * Обрабатывает клики по элементам меню (делегирование)
     * @param {Event} e - Событие клика
     * @private
     */
    handleMenuClick(e) {
        const link = e.target.closest('.menu-link');
        if (!link) return;

        const hasChildren = link.parentElement.querySelector('ul');
        
        if (hasChildren) {
            e.preventDefault();
            this.toggleSubMenu(link, hasChildren);
        } else {
            this.setActiveLink(link);
        }
    }

    /**
     * Переключает видимость подменю в режиме аккордеона
     * @param {HTMLElement} link - Элемент ссылки
     * @param {HTMLElement} subMenu - Элемент UL подменю
     * @private
     */
    toggleSubMenu(link, subMenu) {
        const isExpanded = subMenu.style.display === 'block';
        const parentUl = link.parentElement.parentElement;
        
        // Закрываем соседние подменю (аккордеон)
        parentUl.querySelectorAll('.menu-item > ul').forEach(ul => {
            if (ul !== subMenu) ul.style.display = 'none';
        });
        
        parentUl.querySelectorAll('.arrow').forEach(arrow => {
            if (arrow.parentElement !== link) {
                arrow.style.transform = 'rotate(0deg)';
            }
        });

        // Переключаем текущее подменю
        subMenu.style.display = isExpanded ? 'none' : 'block';
        
        // Анимация стрелочки
        const arrow = link.querySelector('.arrow');
        if (arrow) {
            arrow.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
            arrow.style.transition = 'transform 0.3s';
        }
    }

    /**
     * Устанавливает класс 'active' для активной ссылки
     * @param {HTMLElement} activeLink - Элемент ссылки для активации
     * @private
     */
    setActiveLink(activeLink) {
        this.container.querySelectorAll('.menu-link').forEach(link => {
            link.classList.remove('active');
        });
        activeLink.classList.add('active');
    }

    /**
     * Программно выделяет пункт меню по URL
     * @param {string} url - URL пункта для активации
     * @public
     */
    setActiveByUrl(url) {
        const link = this.container.querySelector(`.menu-link[data-url="${url}"]`);
        if (link) {
            this.setActiveLink(link);
        }
    }

    /**
     * Обновляет данные меню и перерисовывает структуру
     * @param {Array} newData - Новые данные меню
     * @public
     */
    updateData(newData) {
        this.data = newData;
        this.init();
    }
}

// Экспорт для CommonJS
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DynamicMenu;
}