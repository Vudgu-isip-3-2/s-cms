<?php
/**
 * Тест авторизации (логина) в S-CMS.
 *
 * Имитирует POST-запрос на страницу логина с правильными данными
 * и проверяет, что пользователь стал авторизованным:
 *   - появилась сессия (PHPSESSID в куках)
 *   - произошёл редирект на dashboard
 *   - повторный запрос с кукой возвращает авторизованную страницу
 *
 * Режимы работы:
 *   1. Если PHP доступен — запускает встроенный сервер и выполняет
 *      полноценный интеграционный тест с HTTP-запросами.
 *   2. Если PHP недоступен — выполняет статическую проверку структуры
 *      файлов (наличие ключевых элементов авторизации).
 *
 * Запуск: php test/LoginTest.php
 */

require_once __DIR__ . '/TestClass.php';

// ============================================================
// Вспомогательные функции
// ============================================================

/**
 * Проверяет, что файл существует и содержит указанную строку.
 */
function assertFileContains(string $file, string $needle, string $message): void
{
    if (!file_exists($file)) {
        TestRunner::assertEquals(true, false, "$message — файл не найден: $file");
        return;
    }
    $content = file_get_contents($file);
    TestRunner::assertEquals(
        true,
        str_contains($content, $needle),
        $message
    );
}

/**
 * Выполняет HTTP-запрос с сохранением кук (cookie jar).
 *
 * @return array{status: int, headers: array, body: string, cookies: array}
 */
function httpRequest(
    string $url,
    string $method = 'GET',
    ?array $postData = null,
    array $cookies = []
): array {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    // Устанавливаем куки
    if (!empty($cookies)) {
        $cookieStr = implode('; ', array_map(
            fn($k, $v) => "$k=$v",
            array_keys($cookies),
            $cookies
        ));
        curl_setopt($ch, CURLOPT_COOKIE, $cookieStr);
    }

    // POST-запрос
    if ($method === 'POST' && $postData !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    $headerStr = substr($response, 0, $headerSize);
    $body      = substr($response, $headerSize);

    // Парсим заголовки
    $headers = [];
    $lines = explode("\r\n", $headerStr);
    foreach ($lines as $line) {
        if (strpos($line, ':') !== false) {
            [$key, $val] = explode(':', $line, 2);
            $headers[trim($key)] = trim($val);
        }
    }

    // Извлекаем куки из Set-Cookie
    $responseCookies = [];
    if (isset($headers['Set-Cookie'])) {
        preg_match('/^([^=]+)=([^;]+)/', $headers['Set-Cookie'], $m);
        if ($m) {
            $responseCookies[$m[1]] = $m[2];
        }
    }

    return [
        'status'  => $status,
        'headers' => $headers,
        'body'    => $body,
        'cookies' => $responseCookies,
    ];
}

// ============================================================
// Часть 1. Статическая проверка структуры (работает всегда)
// ============================================================

$loginFile = __DIR__ . '/login.php';

// Проверяем наличие ключевых элементов авторизации
assertFileContains($loginFile, 'session_start()', 'login.php вызывает session_start()');
assertFileContains($loginFile, '$_SESSION', 'login.php использует $_SESSION');
assertFileContains($loginFile, 'password_verify', 'login.php проверяет пароль через password_verify');
assertFileContains($loginFile, '$_SESSION[\'user_id\']', 'login.php устанавливает $_SESSION[\'user_id\']');
assertFileContains($loginFile, '$_SESSION[\'username\']', 'login.php устанавливает $_SESSION[\'username\']');
assertFileContains($loginFile, 'header(\'Location:', 'login.php выполняет редирект при успехе');

// Проверяем наличие dashboard.php
assertFileContains(__DIR__ . '/dashboard.php', '$_SESSION[\'user_id\']', 'dashboard.php проверяет авторизацию');

// ============================================================
// Часть 2. Интеграционный тест (если PHP доступен)
// ============================================================

$phpAvailable = true;
exec('php -r "echo PHP_VERSION;" 2>NUL', $output, $exitCode);
if ($exitCode !== 0 || empty($output[0])) {
    $phpAvailable = false;
}

if (!$phpAvailable) {
    echo "\n⚠ PHP не найден — интеграционный тест пропущен.\n";
    echo "  Для полной проверки установите PHP и запустите: php test/LoginTest.php\n";
    TestRunner::summary();
    exit(0);
}

// ------------------------------------------------------------
// Запуск встроенного PHP-сервера
// ------------------------------------------------------------

$serverPort = 8765;
$baseUrl    = "http://127.0.0.1:$serverPort";
$docroot    = __DIR__;

// Запускаем сервер
$command = sprintf(
    'php -S %s:%d -t %s > NUL 2>&1',
    '127.0.0.1',
    $serverPort,
    escapeshellarg($docroot)
);

$descriptors = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
];

$process = proc_open($command, $descriptors, $pipes);
if (!is_resource($process)) {
    TestRunner::assertEquals(true, false, 'Не удалось запустить PHP-сервер');
    TestRunner::summary();
    exit(1);
}

// Даём серверу время на запуск
usleep(500000); // 0.5 сек

try {
    // --------------------------------------------------------
    // Тест 1: POST-запрос с правильными данными
    // --------------------------------------------------------
    $response = httpRequest("$baseUrl/login.php", 'POST', [
        'username' => 'admin',
        'password' => 'admin123',
    ]);

    // Проверяем редирект на dashboard (303 See Other или 302 Found)
    TestRunner::assertEquals(
        true,
        in_array($response['status'], [302, 303], true),
        'При правильных данных происходит редирект (302/303)'
    );

    // Проверяем, что  Location ведёт на dashboard
    $location = $response['headers']['Location'] ?? '';
    TestRunner::assertEquals(
        true,
        str_contains($location, 'dashboard'),
        'Редирект ведёт на dashboard'
    );

    // --------------------------------------------------------
    // Тест 2: Установлена кука сессии (PHPSESSID)
    // --------------------------------------------------------
    $setCookie = $response['headers']['Set-Cookie'] ?? '';
    TestRunner::assertEquals(
        true,
        str_contains($setCookie, 'PHPSESSID'),
        'Установлена кука сессии PHPSESSID'
    );

    // Извлекаем значение PHPSESSID
    preg_match('/PHPSESSID=([^;]+)/', $setCookie, $m);
    $sessionId = $m[1] ?? '';
    TestRunner::assertEquals(
        true,
        $sessionId !== '',
        'Значение PHPSESSID не пустое'
    );

    // --------------------------------------------------------
    // Тест 3: Повторный запрос с кукой — доступен dashboard
    // --------------------------------------------------------
    $dashboard = httpRequest(
        "$baseUrl/dashboard.php",
        'GET',
        null,
        ['PHPSESSID' => $sessionId]
    );

    TestRunner::assertEquals(
        200,
        $dashboard['status'],
        'С авторизованной сессией dashboard доступен (200)'
    );

    // --------------------------------------------------------
    // Тест 4: Без куки dashboard недоступен (редирект на login)
    // --------------------------------------------------------
    $noAuth = httpRequest("$baseUrl/dashboard.php");
    TestRunner::assertEquals(
        true,
        in_array($noAuth['status'], [302, 303], true),
        'Без сессии dashboard перенаправляет на login (302/303)'
    );

    // --------------------------------------------------------
    // Тест 5: Неправильный пароль — нет сессии
    // --------------------------------------------------------
    $badLogin = httpRequest("$baseUrl/login.php", 'POST', [
        'username' => 'admin',
        'password' => 'wrongpassword',
    ]);

    TestRunner::assertEquals(
        401,
        $badLogin['status'],
        'При неправильном пароле возвращается 401'
    );

    $badCookie = $badLogin['headers']['Set-Cookie'] ?? '';
    TestRunner::assertEquals(
        false,
        str_contains($badCookie, 'PHPSESSID'),
        'При неправильном пароле сессия НЕ создаётся'
    );

} catch (Throwable $e) {
    TestRunner::assertEquals(true, false, 'Исключение: ' . $e->getMessage());
} finally {
    // Останавливаем сервер
    $status = proc_get_status($process);
    if ($status['running']) {
        proc_terminate($process);
    }
    proc_close($process);
}

// ============================================================
// Итог
// ============================================================
TestRunner::summary();
