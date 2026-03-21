<?php

class Config {
    private static array $settings = []; //общий массив со всеми настройками в формате ключ-значение

    public static function load(string $filePath): void //загружает данные из файла в общий массив настроек
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

    public static function get(string $key, $default = null) //получает параметр по ключу с поддержкой вложенности (db.host где db - секция, а host - параметр)
    
    {
        $data = self::$settings;
        $keys = explode('.', $key);

        foreach ($keys as $segment) {
            if (isset($data[$segment])) {
                $data = $data[$segment];
            } else {
                return $default;
            }
        }
        return $data;
    }

    private static function parseEnv(string $filePath): array //парсер для .env файлов
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); //читает строки пропуская пустые
        $env = [];

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue; //пропуск строк с комментариями
            
            list($name, $value) = explode('=', $line, 2); //парсим через равно в формате ключ=значение
            $env[trim($name)] = trim($value);
        }

        return $env;
    }
}
