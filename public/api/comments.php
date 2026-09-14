<?php
// public/api/comments.php
header('Content-Type: application/json; charset=utf-8');
// Разрешаем запросы с любого источника (для локальной разработки)
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Подключаем наш класс Comment (предполагаем, что он уже создан, см. шаг 2)
// Если автозагрузка не настроена, раскомментируйте строку ниже:
require_once __DIR__ . '/../../lib/Comment.php'; 

$method = $_SERVER['REQUEST_METHOD'];

try {
    $commentModel = new Comment();

    if ($method === 'GET') {
        // Получение комментариев
        $postId = filter_input(INPUT_GET, 'post_id', FILTER_VALIDATE_INT);
        if ($postId) {
            $comments = $commentModel->getApprovedByPostId($postId);
            echo json_encode(['success' => true, 'data' => $comments]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Не указан ID записи']);
        }
    } elseif ($method === 'POST') {
        // Добавление комментария
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        $postId = filter_var($data['post_id'] ?? 0, FILTER_VALIDATE_INT);
        $content = trim($data['content'] ?? '');
        $authorName = trim($data['author_name'] ?? 'Гость');

        if (!$postId || strlen($content) < 2) {
            throw new Exception('Ошибка: Неверные данные (нужен post_id и текст > 2 символов)');
        }

        // Сохраняем в БД со статусом 'pending' (на модерации)
        $commentModel->create($postId, $content, null, $authorName);
        
        echo json_encode(['success' => true, 'message' => 'Комментарий отправлен на модерацию']);
    } 
    // Методы PUT и DELETE можно добавить позже для админки

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
