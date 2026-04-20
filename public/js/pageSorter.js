// Класс  реализует сортировку элементов списка с помощью drag-and-drop
class PageSorter {

  // Конструктор принимает объект настроек
  constructor(options = {}) {

    // Находим список по CSS-селектору 
    this.list = document.querySelector(options.listSelector);

   
    this.useLocalStorage = options.useLocalStorage !== false;

    // Если список не найден — выбрасываем ошибку
    if (!this.list) {
      throw new Error('Список не найден');
    }

    // Переменная для хранения текущего перетаскиваемого элемента
    this.draggedItem = null;

    // Инициализация обработчиков событий
    this.init();

    // Загрузка сохранённого порядка
    this.loadOrder();
  }

  // Метод инициализации drag-and-drop
  init() {
    // Получаем все элементы списка
    const items = this.list.querySelectorAll('li');

    items.forEach(item => {

      // Разрешаем перетаскивание элемента
      item.setAttribute('draggable', true);

      // Событие начала перетаскивания
      item.addEventListener('dragstart', () => {
        this.draggedItem = item;
        item.classList.add('dragging'); // визуальный эффект
      });

      // Событие окончания перетаскивания
      item.addEventListener('dragend', () => {
        item.classList.remove('dragging');
        this.clearOverStyles(); // убираем подсветку
      });

      // Событие при наведении на элемент
      item.addEventListener('dragover', (e) => {
        e.preventDefault(); // обязательно для drop
        item.classList.add('over'); // подсветка зоны
      });

      // Событие, когда курсор уходит с элемента
      item.addEventListener('dragleave', () => {
        item.classList.remove('over');
      });

      // Событие сброса 
      item.addEventListener('drop', (e) => {
        e.preventDefault();

        // Если перетаскиваемый элемент не совпадает с текущим
        if (this.draggedItem !== item) {

          // Вставляем перетаскиваемый элемент перед текущим
          this.list.insertBefore(this.draggedItem, item);

          // Сохраняем новый порядок
          this.saveOrder();
        }

        item.classList.remove('over');
      });
    });
  }

  // Удаляет подсветку со всех элементов списка
  clearOverStyles() {
    this.list.querySelectorAll('li').forEach(li => {
      li.classList.remove('over');
    });
  }

  // Возвращает текущий порядок элементов
  getOrder() {
    return Array.from(this.list.children).map(li => li.dataset.id);
  }

  // Сохраняет порядок элементов в localStorage
  saveOrder() {
    if (!this.useLocalStorage) return;

    const order = this.getOrder();
    localStorage.setItem('pageOrder', JSON.stringify(order));
  }

  // Загружает порядок элементов из localStorage
  loadOrder() {
    if (!this.useLocalStorage) return;

    const saved = localStorage.getItem('pageOrder');
    if (!saved) return;

    const order = JSON.parse(saved);
    const items = Array.from(this.list.children);

    // Восстанавливаем порядок элементов
    order.forEach(id => {
      const item = items.find(li => li.dataset.id === id);
      if (item) this.list.appendChild(item);
    });
  }

  // Сбрасывает сохранённый порядок
  resetOrder() {
    localStorage.removeItem('pageOrder');
  }
}

// Экспорт класса в глобальную область
if (typeof window !== 'undefined') {
  window.PageSorter = PageSorter;
}