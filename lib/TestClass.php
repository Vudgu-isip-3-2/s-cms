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
     *
     * @param mixed $value Проверяемое значение
     * @param string $message Сообщение теста
     */
    public static function assertNotNull($value, $message)
    {
        self::assertEquals(true, $value !== null, $message);
    }

    /**
     * Проверяет, что значение равно null
     *
     * @param mixed $value Проверяемое значение
     * @param string $message Сообщение теста
     */
    public static function assertNull($value, $message)
    {
        self::assertEquals(true, $value === null, $message);
    }

    /**
     * Выводит итог тестирования:
     * - сколько прошло
     * - сколько упало
     */
    public static function summary()
    {
        echo "\n=== RESULT ===\n";
        echo "Passed: " . self::$passed . "\n";
        echo "Failed: " . self::$failed . "\n";
    }
}