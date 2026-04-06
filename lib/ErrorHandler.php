<?php

/**
 * Класс ErrorHandler 
 * предназначен для логирования ошибок в файл
 */
class ErrorHandler
{
    /**
     * Режим отладки
     * 
     * @var bool true - только логирование, false - production режим
     */
    private static $debug = true;

    /**
     * Путь к файлу лога
     * 
     * @var string
     */
    private static $logFile = '../env/logs/errors.log';

    /**
     * Регистрирует обработчики ошибок, исключений и фатальных ошибок
     * 
     * Этот метод должен быть вызван в самом начале приложения (например, в index.php).
     * Он устанавливает глобальные обработчики для всех типов ошибок.
     * 
     * @param bool $debug Режим отладки:
     *                    - true: ошибки только логируются, на экран не выводятся
     *                    - false (production): ошибки логируются и показывается дружественное сообщение
     * @param string|null $logFile Путь к файлу лога. Если не указан, используется 'logs/errors.log'
     */
    public static function register($debug = false, $logFile = null): void
    {
        self::$debug = (bool) $debug;

        if ($logFile !== null) {
            self::$logFile = (string) $logFile;
        }
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        // Устанавливаем обработчики
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
    * Эта функция перехватывает стандартные ошибки 
    * 
    * @param int $level Уровень ошибки (E_WARNING, E_NOTICE и т.д.)
    * @param string $message Сообщение об ошибке
    * @param string $file Файл, в котором произошла ошибка
    * @param int $line Строка, на которой произошла ошибка
    * @throws \ErrorException Всегда выбрасывает исключение с данными об ошибке
    * 
    * @internal Этот метод вызывается автоматически через set_error_handler()
    */
    public static function handleError($level, $message, $file, $line): bool
    {
        // Преобразуем ошибку в исключение, чтобы обработать её единообразно
        throw new \ErrorException($message, 0, $level, $file, $line);
    }

    /**
     * Обработчик неперехваченных исключений
     * 
     * Перехватывает все исключения, которые не были обработаны в коде приложения.
     * @param \Throwable $exception Объект исключения
     * @internal Этот метод вызывается автоматически через set_exception_handler()
     */
    public static function handleException($exception): void
    {
        self::processError(
            'Uncaught Exception',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
    }
    /**
     * Обработчик фатальных ошибок
     * 
     * Перехватывает фатальные ошибки времени выполнения (E_ERROR, E_PARSE и т.д.),
     * которые не могут быть обработаны стандартным обработчиком ошибок.
     * @internal Этот метод вызывается автоматически через register_shutdown_function()
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::processError(
                'Fatal Error',
                $error['message'],
                $error['file'],
                $error['line'],
                '' // для фатальных ошибок трассировка недоступна
            );
        }
    }
    /**
     * Основная логика обработки ошибки,
     * Если $debug = true, то ошибка просто логируется, иначе выводится сообщение на странице
     * 
     * @param string $type Тип ошибки (Exception, Fatal Error и т.д.)
     * @param string $message Сообщение об ошибке
     * @param string $file Файл с ошибкой
     * @param int $line Строка с ошибкой
     * @param string $trace Трассировка стека вызовов
     */
    private static function processError($type, $message, $file, $line, $trace): void
    {
        self::logError($type, $message, $file, $line, $trace);
        if (!self::$debug) {
            self::displayUserFriendlyMessage();
        }
        exit(1); // завершаем выполнение скрипта после обработки ошибки
    }

    /**
     * Записывает информацию об ошибке в лог-файл
     * 
     * Форматирует ошибку в удобочитаемый вид и добавляет в конец файла лога.
     * Формат записи:
     * [Дата] [Тип] Сообщение в Файл:Строка
     * Stack trace:
     * трассировка (если есть)
     * @param string $type Тип ошибки
     * @param string $message Сообщение об ошибке
     * @param string $file Файл с ошибкой
     * @param int $line Строка с ошибкой
     * @param string $trace Трассировка стека вызовов
     * @example Пример записи в логе:
     * [2025-03-24 15:30:45] [Uncaught Exception] Undefined variable: undefinedVar in /path/to/file.php:22
     * Stack trace:
     * #0 /path/to/file.php(22): ErrorHandler::handleError()
     * #1 {main}
     */
    private static function logError($type, $message, $file, $line, $trace): void
    {
        $date = date('Y-m-d H:i:s');
        $logEntry = sprintf(
            "[%s] [%s] %s in %s:%d\n",
            $date,
            $type,
            $message,
            $file,
            $line
        );
        if ($trace) {
            $logEntry .= "Stack trace:\n$trace\n";
        }
        $logEntry .= str_repeat('-', 80) . "\n"; //разделитель, чтобы не была "каша" в логах

        // Запись в файл с блокировкой
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    private static function displayUserFriendlyMessage(): void
    {
        if (PHP_SAPI !== 'cli') {
            http_response_code(500);
            echo '<!DOCTYPE html><html><head><title>Error</title><style>body{text-align:center; padding:50px; font-family:sans-serif;}</style></head><body>';
            echo '<h1>Извините, произошла ошибка</h1>';
            echo '<p>Мы уже работаем над её исправлением. Пожалуйста, попробуйте позже.</p>';
            echo '</body></html>';
        } else {
            echo "\nПроизошла внутренняя ошибка. Подробности записаны в лог.\n";
        }
    }
}