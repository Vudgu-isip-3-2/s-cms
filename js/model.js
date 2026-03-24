class ModalManager {
    constructor() {
        this.modalSelector = '.modal';
        this.openSelector = '[data-modal-open]';
        this.closeSelector = '[data-modal-close]';
        this.activeClass = 'is-open';
        this.bodyLockClass = 'modal-lock';

        this.openedModals = new Set();

        this.handleClick = this.handleClick.bind(this);
        this.handleKeydown = this.handleKeydown.bind(this);
    }

    init() {
        document.addEventListener('click', this.handleClick);
        document.addEventListener('keydown', this.handleKeydown);
    }

    // получить модалку по id или элементу
    getModal(target) {
        if (typeof target === 'string') return document.getElementById(target);
        if (target instanceof HTMLElement) return target;
        return null;
    }

    // открыть модалку
    open(target) {
        const modal = this.getModal(target);
        if (!modal) return;

        modal.classList.add(this.activeClass);
        modal.setAttribute('aria-hidden', 'false');
        this.openedModals.add(modal);

        this.updateBody();
    }

    // закрыть модалку
    close(target) {
        const modal = this.getModal(target);
        if (!modal) return;

        modal.classList.remove(this.activeClass);
        modal.setAttribute('aria-hidden', 'true');
        this.openedModals.delete(modal);

        this.updateBody();
    }

    // закрыть все
    closeAll() {
        document.querySelectorAll(this.modalSelector).forEach(m => {
            m.classList.remove(this.activeClass);
            m.setAttribute('aria-hidden', 'true');
        });

        this.openedModals.clear();
        this.updateBody();
    }

    // блокировка скролла
    updateBody() {
        document.body.classList.toggle(
            this.bodyLockClass,
            this.openedModals.size > 0
        );
    }

    // обработка кликов
    handleClick(e) {
        const openBtn = e.target.closest(this.openSelector);
        if (openBtn) return this.open(openBtn.dataset.modalOpen);

        const closeBtn = e.target.closest(this.closeSelector);
        if (closeBtn) return this.close(closeBtn.dataset.modalClose);

        const modal = e.target.closest(this.modalSelector);
        if (modal && e.target === modal) this.close(modal);
    }

    // ESC закрывает всё
    handleKeydown(e) {
        if (e.key === 'Escape') this.closeAll();
    }
}

// запуск
document.addEventListener('DOMContentLoaded', () => {
    const modalManager = new ModalManager();
    modalManager.init();

    // доступ из консоли при необходимости
    window.modalManager = modalManager;
});