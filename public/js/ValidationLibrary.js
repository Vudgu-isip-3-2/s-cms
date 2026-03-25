/**
 * Validation Library - Библиотека для валидации форм
 * @version 1.0.0
 * 
 * Что делает: проверяет данные из форм (email, телефон, пароли и т.д.)
 * Как использовать: 
 *   const validator = new ValidationLibrary();
 *   const result = validator.validateField('test@mail.com', [{ name: 'email' }]);
 */

class ValidationLibrary {
  /**
   * Создание валидатора
   * @param {Object} options - настройки (можно передать свои тексты ошибок)
   * @example
   * const validator = new ValidationLibrary({
   *   requiredMessage: 'Заполните это поле'
   * });
   */
  constructor(options = {}) {
    // Тексты ошибок. Можно поменять через options или метод setMessages()
    this.messages = {
      required: options.requiredMessage || 'Это поле обязательно для заполнения',
      email: options.emailMessage || 'Введите корректный email адрес',
      min: options.minMessage || 'Значение должно быть не меньше {min}',
      max: options.maxMessage || 'Значение должно быть не больше {max}',
      minLength: options.minLengthMessage || 'Минимальная длина {minLength} символов',
      maxLength: options.maxLengthMessage || 'Максимальная длина {maxLength} символов',
      passwordMatch: options.passwordMatchMessage || 'Пароли не совпадают',
      phone: options.phoneMessage || 'Введите корректный номер телефона'
    };
  }

  /**
   * Проверка что поле заполнено
   * @param {*} value - значение для проверки
   * @returns {string|null} - текст ошибки или null если ошибок нет
   * @example
   * validator.required(''); // "Это поле обязательно для заполнения"
   * validator.required('text'); // null
   */
  required(value) {
    if (value === undefined || value === null || value === '') {
      return this.messages.required;
    }
    return null;
  }

  /**
   * Проверка email
   * @param {string} value - email для проверки
   * @returns {string|null} - текст ошибки или null
   * @example
   * validator.email('test@mail.com'); // null
   * validator.email('bad-email'); // "Введите корректный email адрес"
   */
  email(value) {
    if (!value) return null;
    
    const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
    if (!emailRegex.test(value)) {
      return this.messages.email;
    }
    return null;
  }

  /**
   * Проверка телефона (российские номера)
   * Принимает форматы: +7 999 123-45-67, 8-999-123-45-67, 79991234567
   * @param {string} value - номер телефона
   * @returns {string|null} - текст ошибки или null
   */
  phone(value) {
    if (!value) return null;
    
    // Убираем пробелы, скобки, дефисы, оставляем только цифры
    const cleanPhone = value.replace(/[\s\-\(\)]/g, '');
    const phoneRegex = /^(\+7|7|8)?[0-9]{10}$/;
    
    if (!phoneRegex.test(cleanPhone)) {
      return this.messages.phone;
    }
    return null;
  }

  /**
   * Проверка минимального значения (для чисел)
   * @param {number} value - число
   * @param {number} minValue - минимальное допустимое значение
   * @returns {string|null} - текст ошибки или null
   */
  min(value, minValue) {
    if (value === undefined || value === null) return null;
    
    if (typeof value === 'number' && value < minValue) {
      return this.messages.min.replace('{min}', minValue);
    }
    return null;
  }

  /**
   * Проверка максимального значения (для чисел)
   * @param {number} value - число
   * @param {number} maxValue - максимальное допустимое значение
   * @returns {string|null} - текст ошибки или null
   */
  max(value, maxValue) {
    if (value === undefined || value === null) return null;
    
    if (typeof value === 'number' && value > maxValue) {
      return this.messages.max.replace('{max}', maxValue);
    }
    return null;
  }

  /**
   * Проверка минимальной длины строки
   * @param {string} value - текст
   * @param {number} length - минимальная длина
   * @returns {string|null} - текст ошибки или null
   */
  minLength(value, length) {
    if (!value) return null;
    
    if (value.length < length) {
      return this.messages.minLength.replace('{minLength}', length);
    }
    return null;
  }

  /**
   * Проверка максимальной длины строки
   * @param {string} value - текст
   * @param {number} length - максимальная длина
   * @returns {string|null} - текст ошибки или null
   */
  maxLength(value, length) {
    if (!value) return null;
    
    if (value.length > length) {
      return this.messages.maxLength.replace('{maxLength}', length);
    }
    return null;
  }

  /**
   * Проверка совпадения паролей
   * @param {string} password - пароль
   * @param {string} confirmPassword - подтверждение пароля
   * @returns {string|null} - текст ошибки или null
   * @example
   * validator.passwordMatch('123', '123'); // null
   * validator.passwordMatch('123', '456'); // "Пароли не совпадают"
   */
  passwordMatch(password, confirmPassword) {
    if (password !== confirmPassword) {
      return this.messages.passwordMatch;
    }
    return null;
  }

  /**
   * Проверка что значение - число
   * @param {*} value - значение
   * @returns {string|null} - текст ошибки или null
   */
  number(value) {
    if (!value) return null;
    
    if (isNaN(value) || typeof value === 'boolean') {
      return 'Введите число';
    }
    return null;
  }

  /**
   * Проверка что значение - целое число
   * @param {*} value - значение
   * @returns {string|null} - текст ошибки или null
   */
  integer(value) {
    if (!value) return null;
    
    if (!Number.isInteger(Number(value))) {
      return 'Введите целое число';
    }
    return null;
  }

  /**
   * Проверка одного поля по нескольким правилам
   * @param {*} value - значение для проверки
   * @param {Array} rules - массив правил
   * @returns {Object} { isValid: true/false, errors: ['ошибка1', 'ошибка2'] }
   * 
   * @example
   * validator.validateField('', [
   *   { name: 'required' }
   * ]);
   * // { isValid: false, errors: ['Это поле обязательно для заполнения'] }
   * 
   * @example
   * validator.validateField('test@mail.com', [
   *   { name: 'required' },
   *   { name: 'email' }
   * ]);
   * // { isValid: true, errors: [] }
   */
  validateField(value, rules) {
    const errors = [];
    
    for (const rule of rules) {
      let error = null;
      
      if (rule.name === 'required') {
        error = this.required(value);
      } else if (rule.name === 'email') {
        error = this.email(value);
      } else if (rule.name === 'phone') {
        error = this.phone(value);
      } else if (rule.name === 'min') {
        error = this.min(value, rule.params);
      } else if (rule.name === 'max') {
        error = this.max(value, rule.params);
      } else if (rule.name === 'minLength') {
        error = this.minLength(value, rule.params);
      } else if (rule.name === 'maxLength') {
        error = this.maxLength(value, rule.params);
      } else if (rule.name === 'number') {
        error = this.number(value);
      } else if (rule.name === 'integer') {
        error = this.integer(value);
      }
      
      if (error) {
        errors.push(error);
      }
    }
    
    return {
      isValid: errors.length === 0,
      errors: errors
    };
  }

  /**
   * Проверка всей формы (нескольких полей)
   * @param {Object} data - объект с данными { поле: значение }
   * @param {Object} schema - объект с правилами { поле: [правила] }
   * @returns {Object} { isValid: true/false, results: { поле: { isValid, errors } } }
   * 
   * @example
   * const data = {
   *   name: '',
   *   email: 'test@mail.com'
   * };
   * 
   * const schema = {
   *   name: [{ name: 'required' }],
   *   email: [{ name: 'required' }, { name: 'email' }]
   * };
   * 
   * const result = validator.validateForm(data, schema);
   * // result.isValid = false
   * // result.results.name.isValid = false
   * // result.results.email.isValid = true
   */
  validateForm(data, schema) {
    const results = {};
    let isValid = true;
    
    for (const [fieldName, rules] of Object.entries(schema)) {
      const value = data[fieldName];
      const result = this.validateField(value, rules);
      
      results[fieldName] = result;
      
      if (!result.isValid) {
        isValid = false;
      }
    }
    
    return {
      isValid: isValid,
      results: results
    };
  }

  /**
   * Преобразует результат валидации в удобный массив ошибок
   * @param {Object} validationResult - результат от validateForm()
   * @returns {Array} - [{ field: 'поле', message: 'ошибка' }]
   * 
   * @example
   * const errors = validator.getErrorsList(result);
   * // [
   * //   { field: 'name', message: 'Это поле обязательно' },
   * //   { field: 'email', message: 'Введите корректный email' }
   * // ]
   */
  getErrorsList(validationResult) {
    const errors = [];
    
    for (const [field, result] of Object.entries(validationResult.results)) {
      if (!result.isValid) {
        result.errors.forEach(error => {
          errors.push({
            field: field,
            message: error
          });
        });
      }
    }
    
    return errors;
  }

  /**
   * Получить текст первой ошибки
   * @param {Object} validationResult - результат от validateForm()
   * @returns {string|null} - текст ошибки или null
   * 
   * @example
   * const firstError = validator.getFirstError(result);
   * if (firstError) {
   *   alert(firstError); // показать первую ошибку
   * }
   */
  getFirstError(validationResult) {
    const errors = this.getErrorsList(validationResult);
    return errors.length > 0 ? errors[0].message : null;
  }

  /**
   * Проверить есть ли ошибки в форме
   * @param {Object} validationResult - результат от validateForm()
   * @returns {boolean} - true если есть ошибки
   * 
   * @example
   * if (validator.hasErrors(result)) {
   *   console.log('Форма заполнена неправильно');
   * }
   */
  hasErrors(validationResult) {
    return !validationResult.isValid;
  }

  /**
   * Изменить тексты ошибок
   * @param {Object} customMessages - объект с новыми текстами
   * @returns {ValidationLibrary} - возвращает себя (для цепочки вызовов)
   * 
   * @example
   * validator.setMessages({
   *   required: 'Заполните поле!',
   *   email: 'Неправильный email!'
   * });
   */
  setMessages(customMessages) {
    this.messages = { ...this.messages, ...customMessages };
    return this;
  }
}

// Экспорт для использования в Node.js
if (typeof module !== 'undefined' && module.exports) {
  module.exports = ValidationLibrary;
}

// Экспорт для использования в браузере
if (typeof window !== 'undefined') {
  window.ValidationLibrary = ValidationLibrary;
}