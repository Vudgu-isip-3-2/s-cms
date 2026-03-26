/**
 * @file PageSorter.js
 * @description Класс для сортировки списка страниц/пунктов меню через drag-and-drop.
 *              Порядок сохраняется в LocalStorage или через AJAX.
 * @version 1.0.0
 */
class PageSorter {
  /**
   * Создание экземпляра
   * @param {Object} options
   * @param {string} options.listSelector - селектор UL/OL списка
   * @param {string|null} [options.saveUrl=null] - URL для сохранения порядка
   * @param {boolean} [options.useLocalStorage=true] - сохранять порядок локальн
   */
  constructor(options = {}) {
    this.listSelector = options.listSelector;
    this.saveUrl = options.saveUrl || null;
    this.useLocalStorage = options.useLocalStorage !== false;

    this.list = document.querySelector(this.listSelector);
    if (!this.list) throw new Error(`Список ${this.listSelector} не найден`);

    this.init();
  }

  /**
   * Инициализация drag-and-drop
   * @private
   */
  init() {
    // Восстановление порядка
    if (this.useLocalStorage) {
      const saved = JSON.parse(localStorage.getItem('pageOrder') || '[]');
      if (saved.length) {
        const items = Array.from(this.list.children);
        saved.forEach(id => {
          const item = items.find(li => li.dataset.id === id);
          if (item) this.list.appendChild(item);
        });
      }
    }

    // SortableJS
    this.sortable = new Sortable(this.list, {
      animation: 150,
      onEnd: () => this.saveOrder()
    });
  }

  /**
   * Сохранение текущего порядка
   */
  saveOrder() {
    const order = Array.from(this.list.children).map(li => li.dataset.id);

    if (this.useLocalStorage) localStorage.setItem('pageOrder', JSON.stringify(order));

    if (this.saveUrl) {
      fetch(this.saveUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order })
      })
      .then(res => res.json())
      .then(() => console.log('Порядок сохранён'))
      .catch(err => console.error(err));
    }
  }

  /**
   * Получить текущий порядок элементов
   * @returns {Array<string>}
   */
  getOrder() {
    return Array.from(this.list.children).map(li => li.dataset.id);
  }

  /**
   * Сбросить порядок на исходный
   */
  resetOrder() {
    localStorage.removeItem('pageOrder');
  }
}

// Экспорт для браузер
if (typeof window !== 'undefined') window.PageSorter = PageSorter;

// Экспорт для Node.js
if (typeof module !== 'undefined' && module.exports) module.exports = PageSorter;