<?php
// autoload.php

spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/src/';
    
    // Простая карта: где искать классы
    $map = [
        // Классы из /lib/
        'Main'         => __DIR__ . '/Main.php',
        'Config'       => __DIR__ . '/Config_class.php',
        'ErrorHandler' => __DIR__ . '/ErrorHandler.php',
        'Router'       => __DIR__ . '/Router.php',
        'DataBase'     => __DIR__ . '/DataBase.php',
    ];
    
    // Проверяем, есть ли класс в карте
    if (isset($map[$class])) {
        $file = $map[$class];
        if (file_exists($file)) {
            require_once $file;
        }
    }
});