<?php

/**
 * Нагрузочное тестирование создания страниц.
 * Issue #28 — тестировщик.
 *
 * Делает 150 итераций "создания страницы" и замеряет время отклика.
 * Создание эмулируется через HTTP-запрос к приложению (index.php)
 * с параметрами, которые в будущем будет принимать API создания страниц.
 *
 * Запуск (из корня проекта, при поднятом docker compose):
 *   docker compose exec php-apache php test/LoadTest_CreatePage.php
 */

const ITERATIONS = 150;
const BASE_URL   = 'http://localhost:8086';

$times = [];
$errors = 0;

echo "=== Нагрузочный тест: создание страниц ===\n";
echo "Итераций: " . ITERATIONS . "\n";
echo "URL: " . BASE_URL . "\n\n";

for ($i = 1; $i <= ITERATIONS; $i++) {
    $params = http_build_query([
        'action'  => 'create_page',
        'title'   => "Load Test Page #{$i}",
        'content' => "Содержимое тестовой страницы #{$i}",
        'author'  => 'load-tester',
    ]);

    $url = BASE_URL . '/?' . $params;
    $start = microtime(true);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    $elapsedMs = (microtime(true) - $start) * 1000;

    if ($curlErr || $httpCode >= 400 || $httpCode === 0) {
        $errors++;
        printf("[%3d] ОШИБКА  HTTP=%s err=%s\n", $i, $httpCode ?: '-', $curlErr ?: '-');
    } else {
        $times[] = $elapsedMs;
        printf("[%3d] OK      %.1f мс\n", $i, $elapsedMs);
    }
}

// === Итоги ===
echo "\n=== РЕЗУЛЬТАТЫ ===\n";
printf("Всего запросов:      %d\n", ITERATIONS);
printf("Успешных:            %d\n", count($times));
printf("Ошибок:              %d\n", $errors);

if (count($times) > 0) {
    $avg = array_sum($times) / count($times);
    printf("Среднее время:       %.1f мс\n", $avg);
    printf("Минимум:             %.1f мс\n", min($times));
    printf("Максимум:            %.1f мс\n", max($times));

    sort($times);
    $median = $times[(int)floor(count($times) / 2)];
    printf("Медиана:             %.1f мс\n", $median);

    if (count($times) >= 20) {
        $first10 = array_slice($times, 0, 10);
        $last10  = array_slice($times, -10);
        $avgFirst = array_sum($first10) / 10;
        $avgLast  = array_sum($last10) / 10;
        printf("\nПервые 10 в среднем:    %.1f мс\n", $avgFirst);
        printf("Последние 10 в среднем: %.1f мс\n", $avgLast);
        printf("Деградация:             %+.1f%%\n", (($avgLast - $avgFirst) / $avgFirst) * 100);
    }

    echo "\n=== ВЫВОД ===\n";
    if ($errors === 0 && ($avgLast ?? 0) < ($avgFirst ?? 0) * 1.5) {
        echo "База данных и приложение выдерживают нагрузку, деградации не выявлено.\n";
    } else {
        echo "ВНИМАНИЕ: есть ошибки или рост времени отклика.\n";
    }
} else {
    echo "Все запросы завершились ошибкой — приложение недоступно.\n";
}