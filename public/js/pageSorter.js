class PageSorter {
  constructor(options = {}) {
    this.list = document.querySelector(options.listSelector);
    this.useLocalStorage = options.useLocalStorage !== false;

    if (!this.list) {
      throw new Error('Список не найден');
    }

    this.draggedItem = null;

    this.init();
    this.loadOrder();
  }

  init() {
    const items = this.list.querySelectorAll('li');

    items.forEach(item => {
      item.setAttribute('draggable', true);

      item.addEventListener('dragstart', (e) => {
        this.draggedItem = item;
        item.classList.add('dragging');
      });

      item.addEventListener('dragend', () => {
        item.classList.remove('dragging');
        this.clearOverStyles();
      });

      item.addEventListener('dragover', (e) => {
        e.preventDefault();
        item.classList.add('over');
      });

      item.addEventListener('dragleave', () => {
        item.classList.remove('over');
      });

      item.addEventListener('drop', (e) => {
        e.preventDefault();

        if (this.draggedItem !== item) {
          this.list.insertBefore(this.draggedItem, item);
          this.saveOrder();
        }

        item.classList.remove('over');
      });
    });
  }

  clearOverStyles() {
    this.list.querySelectorAll('li').forEach(li => {
      li.classList.remove('over');
    });
  }

  getOrder() {
    return Array.from(this.list.children).map(li => li.dataset.id);
  }

  saveOrder() {
    if (!this.useLocalStorage) return;

    const order = this.getOrder();
    localStorage.setItem('pageOrder', JSON.stringify(order));
  }

  loadOrder() {
    if (!this.useLocalStorage) return;

    const saved = localStorage.getItem('pageOrder');
    if (!saved) return;

    const order = JSON.parse(saved);
    const items = Array.from(this.list.children);

    order.forEach(id => {
      const item = items.find(li => li.dataset.id === id);
      if (item) this.list.appendChild(item);
    });
  }

  resetOrder() {
    localStorage.removeItem('pageOrder');
  }
}

// экспорт
if (typeof window !== 'undefined') {
  window.PageSorter = PageSorter;
}