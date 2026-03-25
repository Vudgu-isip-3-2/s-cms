/**
 * Управление модальными окнами
 * 
 * Использование:
 * <button data-modal-open="modal1">Открыть</button>
 * <div id="modal1" class="modal">...</div>
 */
class ModalManager {
    constructor() {
        // --- Конфигурация ---
        this.modalSelector = '.modal';               // селектор модалок
        this.openSelector = '[data-modal-open]';     // кнопки открытия (data-modal-open="id")
        this.closeSelector = '[data-modal-close]';   // кнопки закрытия (data-modal-close="id")
        this.activeClass = 'is-open';                // класс активной модалки
        this.bodyLockClass = 'modal-lock';           // класс для блокировки скролла

        // --- Состояние ---
        this.openedModals = new Set(); // список открытых модалок (Set не хранит дубли)

        // --- Привязка контекста ---
        this.handleClick = this.handleClick.bind(this);
        this.handleKeydown = this.handleKeydown.bind(this);
    }

    /**
     * Инициализация: навешиваем глобальные обработчики
     * Вызывать один раз после загрузки DOM
     */
    init() {
        if (this.isInitialized) return;
        document.addEventListener('click', this.handleClick);
        document.addEventListener('keydown', this.handleKeydown);
        this.isInitialized = true;
    }

    /**
     * Получить элемент модалки
     * @param {string|HTMLElement} target - ID модалки или DOM-элемент
     * @returns {HTMLElement|null}
     */
    getModal(target) {
        if (typeof target === 'string') return document.getElementById(target);
        if (target instanceof HTMLElement) return target;
        return null;
    }

    /**
     * Открыть модальное окно
     * @param {string|HTMLElement} target - ID модалки или DOM-элемент
     */
    open(target) {
        const modal = this.getModal(target);
        if (!modal || modal.classList.contains(this.activeClass)) return;

        // Добавляем класс активности
        modal.classList.add(this.activeClass);
        // Для скринридеров
        modal.setAttribute('aria-hidden', 'false');
        // Сохраняем в список открытых
        this.openedModals.add(modal);
        // Блокируем скролл если это первая открытая модалка
        this.updateBodyLock();
    }

    /**
     * Закрыть модальное окно
     * @param {string|HTMLElement} target - ID модалки или DOM-элемент
     */
    close(target) {
        const modal = this.getModal(target);
        if (!modal || !modal.classList.contains(this.activeClass)) return;

        // Убираем класс активности
        modal.classList.remove(this.activeClass);
        // Для скринридеров
        modal.setAttribute('aria-hidden', 'true');
        // Удаляем из списка открытых
        this.openedModals.delete(modal);
        // Разблокируем скролл если не осталось открытых
        this.updateBodyLock();
    }

    /**
     * Закрыть все модальные окна
     */
    closeAll() {
        // Перебираем все модалки и закрываем
        document.querySelectorAll(this.modalSelector).forEach(modal => {
            modal.classList.remove(this.activeClass);
            modal.setAttribute('aria-hidden', 'true');
        });
        // Очищаем список открытых
        this.openedModals.clear();
        // Разблокируем скролл
        this.updateBodyLock();
    }

    /**
     * Блокирует/разблокирует скролл body
     * Блокируем если есть хотя бы одна открытая модалка
     */
    updateBodyLock() {
        document.body.classList.toggle(this.bodyLockClass, this.openedModals.size > 0);
    }

    /**
     * Обработчик кликов:
     * 1. Клик по кнопке открытия → открываем модалку
     * 2. Клик по кнопке закрытия → закрываем модалку
     * 3. Клик по фону (оверлею) → закрываем модалку
     */
    handleClick(e) {
        // Проверяем кнопку открытия
        const openBtn = e.target.closest(this.openSelector);
        if (openBtn) {
            const modalId = openBtn.dataset.modalOpen;
            if (modalId) this.open(modalId);
            return;
        }

        // Проверяем кнопку закрытия
        const closeBtn = e.target.closest(this.closeSelector);
        if (closeBtn) {
            const modalId = closeBtn.dataset.modalClose;
            if (modalId) this.close(modalId);
            return;
        }

        // Проверяем клик по фону (оверлею) - клик именно на .modal, а не на его содержимое
        const modal = e.target.closest(this.modalSelector);
        if (modal && e.target === modal) {
            this.close(modal);
        }
    }

    /**
     * Обработчик клавиш: закрываем все модалки по ESC
     */
    handleKeydown(e) {
        if (e.key === 'Escape' && this.openedModals.size > 0) {
            this.closeAll();
        }
    }
}

// Автоматический запуск при загрузке DOM
document.addEventListener('DOMContentLoaded', () => {
    const modalManager = new ModalManager();
    modalManager.init();
    
    // Для отладки из консоли (можно убрать в продакшене)
    if (window.location.hostname === 'localhost') {
        window.modalManager = modalManager;
    }
});