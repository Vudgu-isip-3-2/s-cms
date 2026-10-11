<?php

/**
 * Класс TestRunner
 * 
 * Простой инструмент для запуска тестов без сторонних библиотек.
 * Позволяет:
 * - проверять равенство значений
 * - проверять null / not null
 * - считать количество успешных и проваленных тестов
 * - выводить итоговую статистику
 */
class TestRunner
{
    /** @var int Количество успешно пройденных тестов */
    private static int $passed = 0;

    /** @var int Количество проваленных тестов */
    private static int $failed = 0;

    /**
     * Проверяет, равны ли два значения (строгое сравнение ===)
     *
     * @param mixed $expected Ожидаемое значение
     * @param mixed $actual Фактическое значение
     * @param string $message Сообщение теста
     */
    public static function assertEquals($expected, $actual, $message)
    {
        if ($expected === $actual) {
            self::$passed++;
            echo "PASS: $message\n";
        } else {
            self::$failed++;
            echo "FAIL: $message\n";
            echo "   Expected: " . var_export($expected, true) . "\n";
            echo "   Actual:   " . var_export($actual, true) . "\n";
        }
    }

    /**
     * Проверяет, что значение НЕ равно null
     */
    public static function assertNotNull($value, $message)
    {
        self::assertEquals(true, $value !== null, $message);
    }

    /**
     * Проверяет, что значение равно null
     */
    public static function assertNull($value, $message)
    {
        self::assertEquals(true, $value === null, $message);
    }

    /**
     * Выводит итог тестирования
     */
    public static function summary()
    {
        echo "\n=== RESULT ===\n";
        echo "Passed: " . self::$passed . "\n";
        echo "Failed: " . self::$failed . "\n";
    }
}

/**
 * =========================
 * Как пользоваться TestRunner
 * =========================
 * 
 * 1. Вызывай методы проверки для тестирования логики:
 * 
 *    TestRunner::assertEquals(5, 2 + 3, "Сложение работает");
 *    TestRunner::assertNotNull($user, "Пользователь найден");
 *    TestRunner::assertNull($error, "Ошибки нет");
 * 
 * 2. Каждый тест сразу выводит результат:
 *    - PASS: если тест прошёл
 *    - FAIL: если тест не прошёл (с ожидаемым и фактическим значением)
 * 
 * 3. В конце вызови:
 * 
 *    TestRunner::summary();
 * 
 *    чтобы увидеть общую статистику:
 *    сколько тестов прошло и сколько упало.
 * 
 * 4. Обычно все тесты пишутся подряд в одном файле:
 * 
 *    // Пример
 *    TestRunner::assertEquals(4, 2 + 2, "2+2=4");
 *    TestRunner::assertEquals(10, 5 * 2, "5*2=10");
 * 
 *    TestRunner::summary();
 * 
 * 5. Запуск:
 *    просто выполни файл через PHP:
 * 
 *    php test.php
 * 
 * Это простой аналог unit-тестов, полезен для обучения
 * или быстрой проверки логики без подключения PHPUnit.
 */