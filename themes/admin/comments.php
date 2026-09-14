<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление комментариями</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<h1>Комментарии</h1>

<table>
    <thead>
        <tr>
            <th>ID</th><th>Автор</th><th>Текст</th><th>Статус</th><th>Дата</th><th>Действия</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($comments as $c): ?>
        <tr>
            <td><?= (int)$c['id'] ?></td>
            <td><?= htmlspecialchars($c['user_name'] ?? 'Гость') ?></td>
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
                    <button name="action" value="delete" onclick="return confirm('Удалить?')">Удалить</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>