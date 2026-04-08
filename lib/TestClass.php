<?php

class TestRunner
{
    private static int $passed = 0;
    private static int $failed = 0;

    public static function assertEquals($expected, $actual, $message)
    {
        if ($expected === $actual) {
            self::$passed++;
            echo "✅ PASS: $message\n";
        } else {
            self::$failed++;
            echo "❌ FAIL: $message\n";
            echo "   Expected: " . var_export($expected, true) . "\n";
            echo "   Actual:   " . var_export($actual, true) . "\n";
        }
    }

    public static function assertNotNull($value, $message)
    {
        self::assertEquals(true, $value !== null, $message);
    }

    public static function assertNull($value, $message)
    {
        self::assertEquals(true, $value === null, $message);
    }

    public static function summary()
    {
        echo "\n=== RESULT ===\n";
        echo "Passed: " . self::$passed . "\n";
        echo "Failed: " . self::$failed . "\n";
    }
}