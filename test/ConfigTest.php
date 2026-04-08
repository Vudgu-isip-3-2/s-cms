<?php

require_once __DIR__ . '/../lib/Config_Class.php';
require_once __DIR__ . '/../lib/TestClass.php';

// Создаём экземпляр Config с тестовым файлом
$config = new Config(__DIR__ . '/test.env');


// ======================
// Тесты
// ======================
TestRunner::assertNotNull($config->get('APP_NAME'), 'Конфиг загружается');
TestRunner::assertEquals('test-app', $config->get('APP_NAME'), 'Получение APP_NAME');
TestRunner::assertEquals('1234', $config->get('APP_PORT'), 'Получение APP_PORT');
TestRunner::assertNull($config->get('not.exist.key'), 'Несуществующий ключ возвращает null');
TestRunner::assertEquals('default-value', $config->get('not.exist', 'default-value'), 'Работает default значение');
TestRunner::assertEquals('test-app', $config->get('APP_NAME'), 'Повторный load работает');

// ======================
// Вывод результата
// ======================
TestRunner::summary();