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

$entityType = 'post';
$entityId   = (int)($_GET['id'] ?? 0);
$comments   = [];

if ($entityId > 0) {
    $app      = new Main();
    $model    = new Comment($app->getDatabase());
    $comments = $model->getApproved($entityType, $entityId);
}

ob_end_clean();

$themeManager = new ThemeManager(__DIR__ . '/../lib/Theme/config/theme_config.php');

ob_start();
?>
<div class="cms-content">
    <h1>Запись #<?= $entityId ?></h1>
    <!-- Здесь ваш существующий вывод записи -->

    <section class="comments">
        <h3>Комментарии (<?= count($comments) ?>)</h3>

        <?php if (!empty($_SESSION['comment_success'])): ?>
            <p class="success"><?= htmlspecialchars($_SESSION['comment_success']) ?></p>
            <?php unset($_SESSION['comment_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['comment_errors'])): ?>
            <ul class="errors">
                <?php foreach ($_SESSION['comment_errors'] as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php unset($_SESSION['comment_errors']); ?>
        <?php endif; ?>

        <?php foreach ($comments as $c): ?>
            <article class="comment" id="comment-<?= (int)$c['id'] ?>">
                <header>
                    <strong><?= htmlspecialchars($c['user_name'] ?? 'Удалён') ?></strong>
                    <time><?= htmlspecialchars($c['created_at']) ?></time>
                </header>
                <p><?= nl2br(htmlspecialchars($c['body'])) ?></p>
            </article>
        <?php endforeach; ?>

        <?php if (!empty($_SESSION['user_id'])): ?>
            <form method="post" action="/comments_add.php" class="comment-form">
                <input type="hidden" name="entity_type" value="<?= $entityType ?>">
                <input type="hidden" name="entity_id"   value="<?= $entityId ?>">
                <textarea name="body" required minlength="3" maxlength="2000"
                          placeholder="Ваш комментарий..."><?= htmlspecialchars($_SESSION['comment_old'] ?? '') ?></textarea>
                <button type="submit">Отправить</button>
            </form>
            <?php unset($_SESSION['comment_old']); ?>
        <?php else: ?>
            <p><a href="/login.php">Войдите</a>, чтобы оставить комментарий.</p>
        <?php endif; ?>
    </section>
</div>
<?php
$content = ob_get_clean();
echo $themeManager->render($content, 'Запись #' . $entityId);