<?php
ob_start();

require_once __DIR__ . '/../lib/autoload.php';
require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/Router.php';
require_once __DIR__ . '/../lib/Config_class.php';
require_once __DIR__ . '/../lib/Main.php';
require_once __DIR__ . '/../lib/Comment.php';
require_once __DIR__ . '/../lib/Theme/ThemeManager.php';

use Lib\Theme\ThemeManager;

session_start();

if (empty($_SESSION['user_id'])) {
    ob_end_clean();
    http_response_code(403);
    exit('Доступ запрещён');
}

$app = new Main();
$db  = $app->getDatabase();

if (!Comment::isAdmin($db, (int)$_SESSION['user_id'])) {
    ob_end_clean();
    http_response_code(403);
    exit('Доступ запрещён: нужны права администратора или модератора');
}

$model  = new Comment($db);
$action = $_POST['action'] ?? null;
$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($action && $id > 0) {
    switch ($action) {
        case 'approve': $model->approve($id); break;
        case 'reject':  $model->reject($id);  break;
        case 'delete':  $model->delete($id);  break;
    }
    ob_end_clean();
    header('Location: /admin_comments.php');
    exit;
}

$comments = $model->getAllForAdmin();
ob_end_clean();

$themeManager = new ThemeManager(__DIR__ . '/../lib/Theme/config/theme_config.php');

ob_start();
?>
<div class="cms-content">
    <h1>Управление комментариями</h1>

    <?php if (empty($comments)): ?>
        <p>Комментариев пока нет.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <thead>
                <tr>
                    <th>ID</th><th>Объект</th><th>Автор</th><th>Текст</th>
                    <th>Статус</th><th>Дата</th><th>Действия</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($comments as $c): ?>
                <tr>
                    <td><?= (int)$c['id'] ?></td>
                    <td><?= htmlspecialchars($c['entity_type']) ?> #<?= (int)$c['entity_id'] ?></td>
                    <td><?= htmlspecialchars($c['user_name'] ?? 'Удалён') ?></td>
                    <td><?= htmlspecialchars(mb_substr($c['body'], 0, 120)) ?></td>
                    <td><?= $c['is_approved'] ? 'Одобрен' : 'На модерации' ?></td>
                    <td><?= htmlspecialchars($c['created_at']) ?></td>
                    <td>
                        <form method="post" style="display:inline">
                            <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                            <?php if (!$c['is_approved']): ?>
                                <button name="action" value="approve">Одобрить</button>
                            <?php else: ?>
                                <button name="action" value="reject">Снять</button>
                            <?php endif; ?>
                            <button name="action" value="delete"
                                    onclick="return confirm('Удалить?')">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
echo $themeManager->render($content, 'Комментарии — админ');