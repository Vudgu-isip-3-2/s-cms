<?php
ob_start();

require_once __DIR__ . '/../lib/autoload.php';
require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/Router.php';
require_once __DIR__ . '/../lib/Config_class.php';
require_once __DIR__ . '/../lib/Main.php';
require_once __DIR__ . '/../lib/Comment.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    exit('Method Not Allowed');
}

if (empty($_SESSION['user_id'])) {
    ob_end_clean();
    header('Location: /login.php');
    exit;
}

$body       = trim($_POST['body'] ?? '');
$entityType = $_POST['entity_type'] ?? 'post';
$entityId   = isset($_POST['entity_id']) ? (int)$_POST['entity_id'] : 0;
$parentId   = isset($_POST['parent_id']) && $_POST['parent_id'] !== ''
    ? (int)$_POST['parent_id'] : null;

if (!in_array($entityType, ['post', 'page'], true)) {
    $entityType = 'post';
}

$errors = [];
if (mb_strlen($body) < 3)    $errors[] = 'Комментарий слишком короткий.';
if (mb_strlen($body) > 2000) $errors[] = 'Комментарий слишком длинный.';
if ($entityId <= 0)          $errors[] = 'Не указан объект комментария.';

if ($errors) {
    $_SESSION['comment_errors'] = $errors;
    $_SESSION['comment_old']    = $body;
    ob_end_clean();
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit;
}

$app   = new Main();
$model = new Comment($app->getDatabase());

$model->create([
    'entity_type' => $entityType,
    'entity_id'   => $entityId,
    'user_id'     => (int)$_SESSION['user_id'],
    'parent_id'   => $parentId,
    'body'        => $body,
]);

$_SESSION['comment_success'] = 'Комментарий отправлен на модерацию.';
ob_end_clean();
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit;