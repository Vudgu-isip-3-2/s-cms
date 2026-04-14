<?php
// autoload.php

spl_autoload_register(function ($fullClass) {
    $libDir = __DIR__ . '/'; // Директория, в которой запускается данный скрипт
    
    if (strpos($fullClass, '\\') !== false) { // Проверяем есть ли обратные слеши, если это они - превращаем их в путь
        $relativePath = str_replace('\\', '/', $fullClass) . '.php';
        $file = $libDir . $relativePath;
        
        if (file_exists($file)) { // Если класс найден подключаем его.
            require_once $file;
            // Завершаем работу, класс найден, загружен, все ок!
            return;
        }
    }
    // Если неймспейса нет - просто имя класса.
    $className = $fullClass;
    
    if (!is_dir($libDir)) return;
    
    // Создаём рекурсивный итератор для обхода ВСЕХ подпапок внутри
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($libDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY // Возвращает только файла
    );
    
    // Проходим по каждому найденному файлу
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') { // Проверяем его расширение
            if ($file->getFilename() === 'autoload.php') continue; // Пропускаем этот файл
            
            $content = file_get_contents($file->getPathname()); // Читаем содержимое в строку
            
            if (preg_match('/\bclass\s+' . preg_quote($className, '/') . '\b/i', $content)) { // Парсим строку, находим классы с помощью регулярного выражения
                require_once $file->getPathname(); // Если класс найден, подключаем его
                return;
            }
        }
    }
});