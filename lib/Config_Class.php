<?php


/**
 * Статический класс необходимый для содержания всех настроек из файлов конфигурации. работает как простое хранилище
 * 
 * Методы класса: 
 * 
 * Load - загрузка настроек из файлов конфигурации в класс;
 * 
 * Get - получение значения параметра по ключу 
 */
class Config 
{
    
    private static array $settings = []; //общий массив со всеми настройками 

    /**
     * Метод для загрузки настроек из конфигурационных файлов в общий массив класса.
     * @param string $filePath путь к конфигурационному файлу.
     * 
     * Метод принимает конфигурационные файлы с расширениями .ini, .php, .env и загружает все настройки сохраняя вложенность в общий массив класса.
     * 
     * Примеры использования: 
     * 
     * Config::load('C:/Users/Default/Documents/config.php');
     * 
     * Config::load('../config.ini');
     * 
     * Config::load('config.env');
     */
    public static function load(string $filePath): void 
    {
        if (!file_exists($filePath)) {
            throw new Exception("Файл конфигурации не найден: $filePath");
        }
        $extension = pathinfo($filePath, PATHINFO_EXTENSION); //получает инфу про расширение файла в пути
        $data = [];

        switch ($extension) {
            case 'php':
                $data = include $filePath;
                break;
            case 'ini':
                $data = parse_ini_file($filePath, true); //true для поддержки секций которые типо: [секция] и дальше параметры
                break;
            case 'env':
                $data = self::parseEnv($filePath);
                break;
            default:
                throw new Exception("Формат $extension не поддерживается.");
        }


        self::$settings = array_replace_recursive(self::$settings, (array)$data); // рекурсивно сливаем настройки, чтобы можно было загружать несколько файлов
    }

    /** 
    * Предназначен для получения значения из параметра, записанного в классе Config. 
    * @param string $key Ключ по которому будет происходить поиск необходимого параметра.
    * 
    * принимает значение в виде "секция.ключ" (например: db.host - где db это секция (вложенный массив) в которой находится ключ, а host - это ключ (необходимый вам параметр)).
    * @param mixed $default Значение, которое будет возвращать функция, если секция или ключ не были найдены. 
    * @return mixed возвращает значение переменной хранимой по ключу.
    * 
    * Вложенность может быть любой глубины, но суть работы остается та же (get(db.user.password) - вернет ключ password). 
    * Так же у ключа может не быть вложенности (get('ProjectName')).
    * 
    * Пример использования: $host = Config::get('db.host', 'localhost') -  возвращаемое значение будет либо host из конфига, либо localhost если в конфиге не было найдено ключа.
    */
    public static function get(string $key, $default = null)    
    {
        $data = self::$settings;
        $keys = explode('.', $key); // делит строку key на список разделяя через строки

        foreach ($keys as $segment) {
            if (isset($data[$segment])) // если сегмент существует 
                {
                $data = $data[$segment]; // то спускаемся ниже по вложенности
            } else {
                return $default; // возвращает дефолтное значение если не существует 
            }
        }
        return $data; // возвращает значение параметра
    }

    private static function parseEnv(string $filePath): array // парсер для .env файлов
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); //читает строки пропуская пустые
        $env = [];

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue; // пропуск строк с комментариями
            
            list($name, $value) = explode('=', $line, 2); // парсим через равно в формате ключ=значение
            $env[trim($name)] = trim($value); // кидаем в массив параметр и его значение 
        }

        return $env;
    }
    
}
