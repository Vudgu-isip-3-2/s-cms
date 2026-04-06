<?php
// autoload.php

spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/src/';
    
    // Простая карта: где искать классы
    $map = [
        'UserController' => $baseDir . 'controllers/' . $class . '.php',
        'User'           => $baseDir . 'models/' . $class . '.php',
    ];
    
    // Проверяем, есть ли класс в карте
    if (isset($map[$class])) {
        $file = $map[$class];
        if (file_exists($file)) {
            require_once $file;
        }
    }
});