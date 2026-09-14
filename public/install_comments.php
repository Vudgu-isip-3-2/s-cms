<?php
/**
 * Установочный скрипт для системы комментариев.
 * Запускается ОДИН РАЗ: http://localhost:8086/install_comments.php
 * После успешного выполнения — УДАЛИТЕ ЭТОТ ФАЙЛ.
 */
ob_start();

require_once __DIR__ . '/../lib/autoload.php';
require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/Router.php';
require_once __DIR__ . '/../lib/Config_class.php';
require_once __DIR__ . '/../lib/Main.php';

// Создаём соединение с БД (то же, что использует проект)
$app = new Main();
$db  = $app->getDatabase();

$log = [];

// ---------- Шаг 1. Создать таблицу comments ----------
try {
    $db->query("
        CREATE TABLE IF NOT EXISTS `comments` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `entity_type` VARCHAR(32) NOT NULL COMMENT 'тип объекта: post, page',
            `entity_id` INT NOT NULL,
            `user_id` INT DEFAULT NULL,
            `parent_id` INT DEFAULT NULL,
            `body` TEXT NOT NULL,
            `is_approved` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_comments_entity` (`entity_type`, `entity_id`, `is_approved`),
            KEY `idx_comments_user` (`user_id`),
            CONSTRAINT `fk_comments_user`
                FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
                ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT `fk_comments_parent`
                FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`)
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
    ");
    $log[] = ['ok', 'Таблица `comments` создана (или уже существовала).'];
} catch (Exception $e) {
    $log[] = ['err', 'Ошибка создания таблицы: ' . $e->getMessage()];
}

// ---------- Шаг 2. Найти ID роли admin ----------
$adminRoleId = null;
try {
    $row = $db->queryOne("SELECT id FROM `role` WHERE name = 'admin' LIMIT 1");
    if ($row && isset($row['id'])) {
        $adminRoleId = (int)$row['id'];
        $log[] = ['ok', "Роль 'admin' найдена (role_id = {$adminRoleId})."];
    } else {
        // На случай, если роли ещё нет — создаём её
        $newId = $db->insert('role', [
            'name'        => 'admin',
            'description' => 'Полный доступ к системе',
        ]);
        $adminRoleId = (int)$newId;
        $log[] = ['ok', "Роль 'admin' создана (role_id = {$adminRoleId})."];
    }
} catch (Exception $e) {
    $log[] = ['err', 'Ошибка получения роли admin: ' . $e->getMessage()];
}

// ---------- Шаг 3. Назначить роль admin первому пользователю ----------
if ($adminRoleId !== null) {
    try {
        $user = $db->queryOne("SELECT id, username FROM `user` ORDER BY id ASC LIMIT 1");

        if (!$user) {
            $log[] = ['warn', 'Пользователей нет. Сначала зарегистрируйтесь через /reg.php, затем перезапустите этот скрипт.'];
        } else {
            $userId = (int)$user['id'];

            $existing = $db->queryOne(
                "SELECT 1 FROM `user_role` WHERE user_id = ? AND role_id = ? LIMIT 1",
                [$userId, $adminRoleId]
            );

            if ($existing) {
                $log[] = ['ok', "Пользователь '{$user['username']}' (id={$userId}) уже имеет роль admin."];
            } else {
                $db->insert('user_role', [
                    'user_id' => $userId,
                    'role_id' => $adminRoleId,
                ]);
                $log[] = ['ok', "Пользователю '{$user['username']}' (id={$userId}) назначена роль admin."];
            }
        }
    } catch (Exception $e) {
        $log[] = ['err', 'Ошибка назначения роли: ' . $e->getMessage()];
    }
}

// ---------- Шаг 4. Показать результат ----------
ob_end_clean();
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Установка системы комментариев</title>
    <style>
        body { font-family: sans-serif; padding: 30px; max-width: 800px; margin: 0 auto; }
        h1 { border-bottom: 2px solid #333; padding-bottom: 10px; }
        ul { list-style: none; padding: 0; }
        li { padding: 10px 14px; margin: 6px 0; border-radius: 6px; }
        .ok   { background: #e6ffed; border-left: 4px solid #2ecc71; }
        .err  { background: #ffe6e6; border-left: 4px solid #e74c3c; }
        .warn { background: #fff8e1; border-left: 4px solid #f39c12; }
        .done { margin-top: 30px; padding: 16px; background: #f0f0f0; border-radius: 6px; font-size: 14px; }
        code { background: #eee; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Установка системы комментариев</h1>
    <ul>
        <?php foreach ($log as [$type, $msg]): ?>
            <li class="<?= $type ?>"><?= htmlspecialchars($msg) ?></li>
        <?php endforeach; ?>
    </ul>

    <div class="done">
        <strong>Готово.</strong> Теперь <u>обязательно удалите файл</u>
        <code>public/install_comments.php</code>, чтобы никто не смог запустить его повторно.
    </div>

    <p style="margin-top:20px">
        Проверить работу: <a href="/admin_comments.php">/admin_comments.php</a>
    </p>
</body>
</html>