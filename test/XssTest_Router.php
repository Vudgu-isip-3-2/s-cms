<?php
/**
 * Тест защиты от XSS при выводе пользовательских данных.
 * Issue #30.
 */

require_once __DIR__ . '/../lib/TestClass.php';
require_once __DIR__ . '/../lib/Router.php';

const XSS_PAYLOAD = '<script>alert("XSS")</script>';

echo "=== Тест защиты от XSS (#30) ===\n";
echo "Пейлоад: " . XSS_PAYLOAD . "\n\n";

$_GET = [
    'title'   => XSS_PAYLOAD,
    'content' => 'Текст с атакой: ' . XSS_PAYLOAD,
    'author'  => '<img src=x onerror=alert(1)>',
];

$router = new Router();
ob_start();
$router->render();
$output = ob_get_clean();

echo "--- Вывод Router::render() ---\n";
echo $output . "\n";
echo "------------------------------\n\n";

TestRunner::assertEquals(
    false,
    strpos($output, '<script>') !== false,
    'Сырой тег <script> отсутствует в выводе'
);

TestRunner::assertEquals(
    false,
    strpos($output, '<img') !== false,
    'Сырой тег <img> отсутствует в выводе'
);

TestRunner::assertEquals(
    true,
    strpos($output, '&lt;script&gt;') !== false,
    'Экранированный &lt;script&gt; присутствует в выводе'
);

TestRunner::assertEquals(
    true,
    strpos($output, '&lt;img') !== false,
    'Экранированный &lt;img&gt; присутствует в выводе'
);

foreach ($_GET as $key => $value) {
    $escaped = htmlspecialchars($value);
    TestRunner::assertEquals(
        true,
        strpos($output, $escaped) !== false,
        "Значение поля '$key' выведено экранированным"
    );
}

$_GET = ['safe' => 'Обычный текст без тегов'];
$router2 = new Router();
ob_start();
$router2->render();
$safeOutput = ob_get_clean();

TestRunner::assertEquals(
    true,
    strpos($safeOutput, 'Обычный текст без тегов') !== false,
    'Безопасный текст выводится без изменений'
);

TestRunner::summary();