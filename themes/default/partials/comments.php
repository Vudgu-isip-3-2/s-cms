<?php
/** @var array $comments */
/** @var string $type  'post' | 'page' */
/** @var int    $id */
?>

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
                <strong><?= htmlspecialchars($c['user_name'] ?? 'Гость') ?></strong>
                <time><?= htmlspecialchars($c['created_at']) ?></time>
            </header>
            <p><?= nl2br(htmlspecialchars($c['body'])) ?></p>
        </article>
    <?php endforeach; ?>

    <?php if (!empty($_SESSION['user_id'])): ?>
        <form method="post" action="/comments/add" class="comment-form">
            <input type="hidden" name="<?= $type ?>_id" value="<?= (int)$id ?>">
            <textarea name="body" required minlength="3" maxlength="2000"
                      placeholder="Ваш комментарий..."><?= htmlspecialchars($_SESSION['comment_old'] ?? '') ?></textarea>
            <button type="submit">Отправить</button>
        </form>
        <?php unset($_SESSION['comment_old']); ?>
    <?php else: ?>
        <p><a href="/login">Войдите</a>, чтобы оставить комментарий.</p>
    <?php endif; ?>
</section>