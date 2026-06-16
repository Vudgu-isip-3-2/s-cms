// Базовый фреймворк для создания тестов JavaScript

/**
 * Класс тестового фреймворка
 * Предоставляет методы для описания тестов, групп тестов, хуков и ассертов
 */
class TestFramework {
  constructor() {
    // Массив всех тестов и наборов тестов
    this.tests = [];
    
    // Хуки, которые выполняются перед каждым тестом (глобальные)
    this.beforeEachHooks = [];
    
    // Хуки, которые выполняются после каждого теста (глобальные)
    this.afterEachHooks = [];
    
    // Хуки, которые выполняются один раз перед всеми тестами
    this.beforeAllHooks = [];
    
    // Хуки, которые выполняются один раз после всех тестов
    this.afterAllHooks = [];
    
    // Текущий набор тестов (для вложенных describe)
    this.currentSuite = null;
  }


   // Добавление одиночного теста
  test(name, fn) {
    this.tests.push({ name, fn, type: 'test' });
  }

  // Добавление набора тестов (группировка)
  describe(name, fn) {
    // Создаем объект набора тестов
    const suite = { 
      name,           // имя набора
      tests: [],      // массив тестов внутри набора
      beforeEach: [], // хуки beforeEach для этого набора
      afterEach: []   // хуки afterEach для этого набора
    };
    
    // Устанавливаем текущий набор, чтобы хуки добавлялись в него
    this.currentSuite = suite;
    
    // Выполняем функцию, которая определит вложенные тесты
    fn();
    
    // Добавляем набор в общий список тестов
    this.tests.push({ type: 'suite', suite });
    
    // Сбрасываем текущий набор
    this.currentSuite = null;
  }

  /**
   * Хук, выполняющийся перед каждым тестом
   * Если вызван внутри describe - применяется только к тестам в этом describe
   * Если вызван глобально - применяется ко всем тестам
   */
  beforeEach(fn) {
    if (this.currentSuite) {
      // Если внутри describe - добавляем в текущий набор
      this.currentSuite.beforeEach.push(fn);
    } else {
      // Если глобально - добавляем в глобальные хуки
      this.beforeEachHooks.push(fn);
    }
  }

  // Хук, выполняющийся после каждого теста
  afterEach(fn) {
    if (this.currentSuite) {
      this.currentSuite.afterEach.push(fn);
    } else {
      this.afterEachHooks.push(fn);
    }
  }

  // Базовый ассерт - проверяет истинность условия
  assert(condition, message) {
    if (!condition) throw new Error(message || 'Assertion failed');
  }

  // Ассерт для проверки равенства (строгое сравнение ===)
  assertEquals(actual, expected, message) {
    if (actual !== expected) {
      throw new Error(message || `Expected ${expected}, but got ${actual}`);
    }
  }

  // Ассерт для глубокого сравнения объектов/массивов
  assertDeepEqual(actual, expected, message) {
    // Преобразуем объекты в JSON-строки для глубокого сравнения
    const actualStr = JSON.stringify(actual);
    const expectedStr = JSON.stringify(expected);
    
    if (actualStr !== expectedStr) {
      throw new Error(message || `Expected ${expectedStr}, but got ${actualStr}`);
    }
  }

  // Ассерт для проверки, что значение истинно (truthy)
  assertTrue(value, message) {
    if (!value) {
      throw new Error(message || `Expected true, but got ${value}`);
    }
  }

  // Ассерт для проверки, что значение ложно (falsy)
  assertFalse(value, message) {
    if (value) {
      throw new Error(message || `Expected false, but got ${value}`);
    }
  }

  // Ассерт для проверки, что значение не равно null или undefined
  assertExists(value, message) {
    if (value === null || value === undefined) {
      throw new Error(message || 'Expected value to exist');
    }
  }

  // Ассерт для проверки, что значение является массивом
  assertArray(value, message) {
    if (!Array.isArray(value)) {
      throw new Error(message || 'Expected value to be an array');
    }
  }

  // Ассерт для проверки типа значения
  assertType(value, type, message) {
    if (typeof value !== type) {
      throw new Error(message || `Expected type ${type}, but got ${typeof value}`);
    }
  }

  // Ассерт для проверки, что функция выбрасывает ошибку
  assertThrows(fn, expectedError, message) {
    let threw = false;
    let error = null;
    
    try {
      fn();
    } catch (e) {
      threw = true;
      error = e;
    }
    
    if (!threw) {
      throw new Error(message || 'Expected function to throw an error');
    }
    
    if (expectedError) {
      const errorMessage = error.message || error.toString();
      if (expectedError instanceof RegExp) {
        if (!expectedError.test(errorMessage)) {
          throw new Error(message || `Expected error to match ${expectedError}, but got ${errorMessage}`);
        }
      } else if (typeof expectedError === 'string') {
        if (!errorMessage.includes(expectedError)) {
          throw new Error(message || `Expected error to include "${expectedError}", but got "${errorMessage}"`);
        }
      }
    }
  }
}

// Экспортируем класс для использования в других модулях
module.exports = { TestFramework };