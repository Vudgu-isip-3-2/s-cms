<?php
require_once __DIR__ . '/vendor/autoload.php';


use Dotenv\Dotenv;
use Controllers\AuthController;


// Загружаем .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//Также беру коммишки для пропитания бедным студентам
//13.03.2026 будет грустненький мини-комикс с Иллугой
use Dotenv\Dotenv;
use Controllers\AuthController;

// CORS headers для API1235664564567
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Обработка OPTIONS запросов (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Простая маршрутизация
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Убираем базовый путь
if (strpos($request, '/api') === 0) {
    $request = substr($request, 4);
}
//Убираем GET-параметры из строки запроса
if (($pos = strpos($request, '?')) !== false) {
    $request = substr($request, 0, '$pos');
}

//Маршрутизация
switch($request){
    case '/':
        echo "Добро пожаловать на главную!";
        break;
    case '/about':
        echo "Страница о нас";
        break;
    default:
        http_response_code(404);
        echo "404 - Страница не найдена";
        break;
}

// Маршруты
$authController = new AuthController();

switch (true) {
    // Получить требования к паролю
    case preg_match('/^\/auth\/password-requirements$/', $request) && $method === 'GET':
        $authController->getRequirements();
        break;
        
    // Регистрация
    case preg_match('/^\/auth\/register$/', $request) && $method === 'POST':
        $authController->register();
        break;
        
    // Вход
    case preg_match('/^\/auth\/login$/', $request) && $method === 'POST':
        $authController->login();
        break;
        
    // Смена пароля
    case preg_match('/^\/auth\/change-password$/', $request) && $method === 'POST':
        $authController->changePassword();
        break;
        
    // Принудительная смена пароля
    case preg_match('/^\/auth\/force-change-password$/', $request) && $method === 'POST':
        $authController->forceChangePassword();
        break;
        
    // Профиль (защищенный)
    case preg_match('/^\/profile$/', $request) && $method === 'GET':
        $authController->profile();
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Маршрут не найден']);
        break;

}
// Подсветка активного пункта меню #57
//Реализуйте подсветку текущего пункта меню (например, класс active),
//чтобы пользователь понимает, на какой странице находится.
//Сделайте это в CSS и добавьте проверку в шаблоне:
//если URL совпадает — добавлять класс.