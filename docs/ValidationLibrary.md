# ValidationLibrary

## Описание

Библиотека для валидации данных в HTML формах. Проверяет поля на заполненность, корректность email, телефона, диапазоны чисел, длину строк и совпадение паролей.

---

## Подключение

<script src="путь/к/validation-library.js"></script>
```

## Создание экземпляра

```javascript
// Стандартное создание
const validator = new ValidationLibrary();

// С кастомными сообщениями
const validator = new ValidationLibrary({
    requiredMessage: 'Заполните поле',
    emailMessage: 'Неправильный email',
    phoneMessage: 'Неверный телефон',
    minMessage: 'Минимум {min}',
    maxMessage: 'Максимум {max}',
    minLengthMessage: 'Минимум {minLength} символов',
    maxLengthMessage: 'Максимум {maxLength} символов',
    passwordMatchMessage: 'Пароли не совпадают'
});
```

## required(value)

Проверяет, что поле заполнено. Возвращает текст ошибки или null.

```javascript
validator.required('');     // "Это поле обязательно для заполнения"
validator.required('text'); // null


```
## email(value)

Проверяет корректность email. Возвращает текст ошибки или null.

```javascript
validator.email('test@mail.com'); // null
validator.email('bad');           // "Введите корректный email адрес"


```
## phone(value)

Проверяет российский номер телефона. Принимает форматы: +7 999 123-45-67, 8-999-123-45-67, 79991234567.

```javascript
validator.phone('+7 999 123-45-67'); // null
validator.phone('123');              // "Введите корректный номер телефона"


```
## min(value, minValue)

Проверяет, что число не меньше указанного значения.

```javascript
validator.min(15, 18); // "Значение должно быть не меньше 18"
validator.min(20, 18); // null



```
## max(value, maxValue)

Проверяет, что число не больше указанного значения.

```javascript
validator.max(150, 100); // "Значение должно быть не больше 100"
validator.max(50, 100);  // null



```
## minLength(value, length)

Проверяет, что длина строки не меньше указанной.

```javascript
validator.minLength('ab', 3);   // "Минимальная длина 3 символов"
validator.minLength('abc', 3);  // null



```
## maxLength(value, length)

Проверяет, что длина строки не больше указанной.

```javascript
validator.maxLength('abcdef', 3); // "Максимальная длина 3 символов"
validator.maxLength('abc', 3);    // null



```
## passwordMatch(password, confirm)

Проверяет совпадение паролей.

```javascript
validator.passwordMatch('123', '123'); // null
validator.passwordMatch('123', '456'); // "Пароли не совпадают"



```
## number(value)

Проверяет, что значение является числом.

```javascript
validator.number('abc'); // "Введите число"
validator.number('123'); // null



```
## integer(value)

Проверяет, что значение является целым числом.

```javascript
validator.integer('1.5'); // "Введите целое число"
validator.integer('5');   // null



```
## validateField(value, rules)

Проверяет одно поле по нескольким правилам. Возвращает объект с полями isValid и errors.

```javascript
const result = validator.validateField('', [
    { name: 'required' },
    { name: 'minLength', params: 3 }
]);
// result = { isValid: false, errors: ['Это поле обязательно для заполнения'] }



```
## validateForm(data, schema)

Проверяет всю форму. Принимает объект с данными и объект со схемой. Возвращает объект с полями isValid и results.

```javascript
const data = {
    username: 'john',
    email: 'john@mail.com'
};

const schema = {
    username: [{ name: 'required' }],
    email: [{ name: 'required' }, { name: 'email' }]
};

const result = validator.validateForm(data, schema);
// result = { isValid: true, results: { username: {...}, email: {...} } }



```
## getErrorsList(validationResult)

Преобразует результат валидации в массив ошибок с указанием поля.

```javascript
const errors = validator.getErrorsList(result);
// errors = [{ field: 'username', message: 'Минимальная длина 3 символов' }]



```
## getFirstError(validationResult)

Возвращает текст первой ошибки или null.

```javascript
const firstError = validator.getFirstError(result);



```
## hasErrors(validationResult)

Возвращает true, если есть ошибки.

```javascript
if (validator.hasErrors(result)) {
    console.log('Есть ошибки');
}



```
## setMessages(customMessages)

Устанавливает кастомные сообщения об ошибках. Возвращает this для цепочки вызовов.

```javascript
validator.setMessages({
    required: 'Заполните поле',
    email: 'Неправильный email'
});



```
## Пример Html кода
/**
 * Validation Library - Библиотека для валидации форм
 * @version 1.0.0
 */

class ValidationLibrary {
  constructor(options = {}) {
    // Сообщения об ошибках (можно менять)
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
   * Проверка обязательного поля
   */
  required(value) {
    if (value === undefined || value === null || value === '') {
      return this.messages.required;
    }
    return null;
  }

  /**
   * Проверка email
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
   */
  phone(value) {
    if (!value) return null;
    
    // Убираем все пробелы, скобки, дефисы
    const cleanPhone = value.replace(/[\s\-\(\)]/g, '');
    const phoneRegex = /^(\+7|7|8)?[0-9]{10}$/;
    
    if (!phoneRegex.test(cleanPhone)) {
      return this.messages.phone;
    }
    return null;
  }

  /**
   * Проверка минимального значения
   */
  min(value, minValue) {
    if (value === undefined || value === null) return null;
    
    if (typeof value === 'number' && value < minValue) {
      return this.messages.min.replace('{min}', minValue);
    }
    return null;
  }

  /**
   * Проверка максимального значения
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
   */
  passwordMatch(password, confirmPassword) {
    if (password !== confirmPassword) {
      return this.messages.passwordMatch;
    }
    return null;
  }

  /**
   * Проверка что значение - число
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
   */
  integer(value) {
    if (!value) return null;
    
    if (!Number.isInteger(Number(value))) {
      return 'Введите целое число';
    }
    return null;
  }

  /**
   * Валидация одного поля
   * @param {*} value - значение для проверки
   * @param {Array} rules - массив правил
   * @returns {Object} - результат валидации
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
   * Валидация всей формы
   * @param {Object} data - данные формы { поле: значение }
   * @param {Object} schema - схема валидации { поле: [правила] }
   * @returns {Object} - результат валидации
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
   * Получить список всех ошибок в виде массива
   * @param {Object} validationResult - результат валидации
   * @returns {Array} - массив ошибок [{ field, message }]
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
   * Получить первую ошибку
   * @param {Object} validationResult - результат валидации
   * @returns {string|null} - текст ошибки или null
   */
  getFirstError(validationResult) {
    const errors = this.getErrorsList(validationResult);
    return errors.length > 0 ? errors[0].message : null;
  }

  /**
   * Проверить есть ли ошибки
   * @param {Object} validationResult - результат валидации
   * @returns {boolean}
   */
  hasErrors(validationResult) {
    return !validationResult.isValid;
  }

  /**
   * Установить свои сообщения об ошибках
   * @param {Object} customMessages - объект с сообщениями
   */
  setMessages(customMessages) {
    this.messages = { ...this.messages, ...customMessages };
    return this;
  }
}

// Экспорт для разных окружений
if (typeof module !== 'undefined' && module.exports) {
  module.exports = ValidationLibrary;
}

if (typeof window !== 'undefined') {
  window.ValidationLibrary = ValidationLibrary;
}
