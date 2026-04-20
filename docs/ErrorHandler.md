# ErrorHandler
Класс для обработки и логирования ошибок.
## О классе
`ErrorHandler` предоставляет унифицированный механизм перехвата всех типов ошибок:
- Стандартные ошибки PHP (warning, notice и т.д.)
- Неперехваченные исключения
- Фатальные ошибки (E_ERROR, E_PARSE и т.д.)
Все ошибки записываются в лог-файл с подробной информацией и трассировкой стека.
## Установка
Просто скопируйте класс в ваш проект и подключите его.
```php
require_once 'path/to/ErrorHandler.php';
```
## Методы

### register()

Регистрирует все обработчики ошибок, исключений и фатальных ошибок.

**Сигнатура:**

```php

public static function register(bool $debug = false, ?string $logFile = null): void
```
**Параметры:**

|Параметр|Тип|По умолчанию|Описание|
|---|---|---|---|
|`$debug`|bool|`false`|Режим отладки:  <br>- `true`: ошибки только логируются  <br>- `false` (production): показывается красивое сообщение |
|`$logFile`|string\|null|`null`|Путь к файлу лога. Если `null`, используется `'./logs/errors.log'`|

**Примеры:**

```php

// Production режим
ErrorHandler::register();
// Режим отладки
ErrorHandler::register(true);
// С пользовательским путём к логу
ErrorHandler::register(false, __DIR__ . '/logs/errors.log');
```
---
### handleError()

Обработчик стандартных ошибок PHP. Преобразует ошибки в исключения `ErrorException`.

**Сигнатура:**

```php

public static function handleError(int $level, string $message, string $file, int $line): bool
```
**Параметры:**

|Параметр|Тип|Описание|
|---|---|---|
|`$level`|int|Уровень ошибки (E_WARNING, E_NOTICE и т.д.)|
|`$message`|string|Сообщение об ошибке|
|`$file`|string|Файл, в котором произошла ошибка|
|`$line`|int|Строка с ошибкой|

**Возвращает:** `bool` (всегда выбрасывает исключение)

**Примечание:** Этот метод вызывается автоматически через `set_error_handler()`.

---

### handleException()

Обработчик неперехваченных исключений.

**Сигнатура:**

```php

public static function handleException(\Throwable $exception): void
```
**Параметры:**

|Параметр|Тип|Описание|
|---|---|---|
|`$exception`|\Throwable|Объект исключения|

**Примечание:** Этот метод вызывается автоматически через `set_exception_handler()`.

---

### handleShutdown()

Обработчик фатальных ошибок времени выполнения.

**Сигнатура:**

```php

public static function handleShutdown(): void
```
**Примечание:** Этот метод вызывается автоматически через `register_shutdown_function()`.

## Формат логирования
Лог-файл содержит записи в следующем формате:
```text

[2025-03-24 15:30:45] [Uncaught Exception] Undefined variable: undefinedVar in /path/to/file.php:22
Stack trace:
#0 /path/to/file.php(22): ErrorHandler::handleError()
#1 {main}
--------------------------------------------------------------------------------
```
