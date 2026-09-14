<?php
require_once __DIR__ . '/../../lib/autoload.php';

use Lib\DataBase;
use Lib\Comment;
use Lib\ErrorHandler;

session_start();

// Проверка прав администратора
if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    http_response_code(403);
    exit('Доступ запрещён');
}

$db = new DataBase(/* ваши параметры */);
$commentModel = new Comment($db);

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$id     = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if ($action && $id > 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'approve': $commentModel->approve($id); break;
        case 'reject':  $commentModel->reject($id);  break;
        case 'delete':  $commentModel->delete($id);  break;
    }
    header('Location: /admin/comments');
    exit;
}

$comments = $commentModel->getAllForAdmin();

// Подключаем шаблон
require_once __DIR__ . '/../../themes/admin/comments.php';