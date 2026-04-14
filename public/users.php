<?php

// ==========================================
// 1. ПОДКЛЮЧЕНИЕ К БД (БЕЗ ИЗМЕНЕНИЙ)
// ==========================================
require_once __DIR__ . '/../lib/Config_Class.php';
require_once __DIR__ . '/../lib/DataBase.php';

$config = new Config(dirname(__DIR__) . '/.env');

$host     = $config->get('DB_HOST');
$dbname   = $config->get('DB_DATABASE');
$username = $config->get('DB_USERNAME');
$password = $config->get('DB_PASSWORD');

// Подключение к базе
try {
    $db = DataBase::getInstance($host, $dbname, $username, $password);
} catch (Exception $e) {
    die(" Ошибка подключения к БД: " . $e->getMessage());
}

// ==========================================
// 2. ОБРАБОТКА ФОРМ (ДОБАВЛЕНИЕ / УДАЛЕНИЕ)
// ==========================================
$message = '';
$message_type = ''; // 'success' или 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 🔹 УДАЛЕНИЕ ПОЛЬЗОВАТЕЛЯ
    if (isset($_POST['action']) && $_POST['action'] === 'delete_user') {
        $id = intval($_POST['id']); // Безопасность: приводим к целому числу
        if ($id > 0) {
            try {
                $db->query("DELETE FROM users WHERE id = $id");
                $message = " Пользователь удалён";
                $message_type = 'success';
            } catch (Exception $e) {
                $message = " Ошибка удаления: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }

    // 🔹 ДОБАВЛЕНИЕ ПОЛЬЗОВАТЕЛЯ (Исправлено под вашу структуру)
    if (isset($_POST['action']) && $_POST['action'] === 'add_user') {
        $username_new   = $_POST['username'] ?? '';
        $display_name   = $_POST['display_name'] ?? '';
        $role           = $_POST['role'] ?? 'user';
        $bio            = $_POST['bio'] ?? '';
        
        // Проверка только логина (пароля в БД нет)
        if ($username_new) {
            try {
                // is_active = 1 (активен), created_at = NOW() (текущее время)
                $sql = "INSERT INTO users (username, display_name, role, bio, is_active, created_at) 
                        VALUES ('$username_new', '$display_name', '$role', '$bio', 1, NOW())";
                
                $db->query($sql);
                $message = " Пользователь <b>" . htmlspecialchars($username_new) . "</b> создан!";
                $message_type = 'success';
            } catch (Exception $e) {
                $message = " Ошибка добавления: " . $e->getMessage();
                $message_type = 'error';
            }
        } else {
            $message = " Заполните обязательное поле: Логин";
            $message_type = 'error';
        }
    }
}

// ==========================================
// 3. ПОЛУЧЕНИЕ ДАННЫХ ДЛЯ ВЫВОДА
// ==========================================
$sql = "SELECT 
            u.id,
            u.username,
            u.display_name,
            u.role,
            u.bio,
            u.is_active,
            u.created_at,
            m.filename as avatar_filename,
            m.file_path as avatar_path
        FROM users u
        LEFT JOIN media m ON u.avatar_media_id = m.id
        ORDER BY u.created_at DESC";

$users = $db->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список пользователей - CMS</title>
    <style>
        /* Стили остаются без изменений */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f7fa; color: #333; line-height: 1.6; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 20px rgba(0,0,0,0.1); padding: 30px; }
        h1 { color: #2c3e50; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #eee; }
        
        .error { background: #fee; color: #c00; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #c00; }
        .success { background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #22c55e; }
        
        .stats { display: flex; gap: 20px; margin-bottom: 25px; flex-wrap: wrap; }
        .stat-card { background: #f8f9fa; padding: 15px 25px; border-radius: 8px; border-left: 4px solid #3498db; }
        .stat-card .number { font-size: 24px; font-weight: bold; color: #2c3e50; }
        .stat-card .label { color: #7f8c8d; font-size: 14px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #34495e; color: #fff; padding: 14px 12px; text-align: left; font-weight: 600; font-size: 14px; }
        td { padding: 14px 12px; border-bottom: 1px solid #eee; vertical-align: top; }
        tr:hover { background: #f8f9fa; }
        
        .avatar { width: 48px; height: 48px; border-radius: 50%; background: #ecf0f1; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #7f8c8d; font-size: 18px; overflow: hidden; }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .user-info { display: flex; align-items: center; gap: 12px; }
        .user-details strong { display: block; color: #2c3e50; }
        .user-details small { color: #7f8c8d; font-family: monospace; }
        
        .role { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .role.admin { background: #e74c3c20; color: #c0392b; }
        .role.editor { background: #f39c1220; color: #d35400; }
        .role.author { background: #3498db20; color: #2980b9; }
        .role.user { background: #95a5a620; color: #7f8c8d; }
        
        .status { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status.active { background: #2ecc7120; color: #27ae60; }
        .status.active::before { content: ''; width: 8px; height: 8px; background: #2ecc71; border-radius: 50%; }
        .status.inactive { background: #95a5a620; color: #7f8c8d; }
        
        .bio { color: #555; font-size: 14px; max-width: 300px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .date { color: #7f8c8d; font-size: 13px; white-space: nowrap; }
        
        .empty { text-align: center; padding: 40px; color: #7f8c8d; }
        .empty svg { width: 64px; height: 64px; margin-bottom: 15px; opacity: 0.5; }

        /* Стили формы добавления */
        .add-panel { background: #f0f9ff; border: 1px dashed #3498db; border-radius: 8px; padding: 20px; margin-bottom: 25px; }
        .add-panel summary { cursor: pointer; font-weight: 600; color: #2980b9; user-select: none; }
        .add-panel summary:hover { color: #1a5276; }
        .form-row { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px; }
        .form-group { flex: 1; min-width: 200px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #2c3e50; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        .btn-add { background: #2ecc71; color: white; border: none; padding: 10px 25px; border-radius: 6px; cursor: pointer; font-weight: 600; margin-top: 10px; }
        .btn-add:hover { background: #27ae60; }
        .btn-delete { background: #e74c3c; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600; }
        .btn-delete:hover { background: #c0392b; }

        @media (max-width: 768px) {
            .container { padding: 15px; }
            table { font-size: 14px; }
            th, td { padding: 10px 8px; }
            .bio { max-width: 200px; }
            .user-info { flex-direction: column; align-items: flex-start; }
            .form-row { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Пользователи системы</h1>
        
        <?php if ($message): ?>
            <div class="<?= $message_type === 'success' ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
             
        <!-- 🔹 Панель добавления пользователя -->
        <details class="add-panel">
            <summary> Добавить нового пользователя</summary>
            <form method="POST" style="margin-top: 15px;">
                <input type="hidden" name="action" value="add_user">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Логин *</label>
                        <input type="text" name="username" required placeholder="johndoe">
                    </div>
                    <div class="form-group">
                        <label>Отображаемое имя</label>
                        <input type="text" name="display_name" placeholder="John Doe">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Роль</label>
                        <select name="role">
                            <option value="user">Пользователь</option>
                            <option value="admin">Администратор</option>
                            <option value="editor">Редактор</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>О себе</label>
                        <input type="text" name="bio" placeholder="Краткая информация...">
                    </div>
                </div>
                
                <button type="submit" class="btn-add"> Создать пользователя</button>
            </form>
        </details>

        <!-- Таблица пользователей -->
        <?php if (!empty($users)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Пользователь</th>
                        <th>Роль</th>
                        <th>Статус</th>
                        <th>О себе</th>
                        <th>Дата регистрации</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="avatar">
                                        <?php if (!empty($user['avatar_path'])): ?>
                                            <img src="<?= htmlspecialchars($user['avatar_path']) ?>" 
                                                 alt="<?= htmlspecialchars($user['display_name']) ?>">
                                        <?php else: ?>
                                            <?= mb_strtoupper(mb_substr($user['display_name'] ?? 'U', 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="user-details">
                                        <strong><?= htmlspecialchars($user['display_name'] ?? '') ?></strong>
                                        <small>@<?= htmlspecialchars($user['username'] ?? '') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="role <?= htmlspecialchars($user['role'] ?? 'user') ?>">
                                    <?= htmlspecialchars($user['role'] ?? 'user') ?>
                                </span>
                            </td>
                            <td>
                                <span class="status <?= ($user['is_active'] ?? false) ? 'active' : 'inactive' ?>">
                                    <?= ($user['is_active'] ?? false) ? 'Активен' : 'Неактивен' ?>
                                </span>
                            </td>
                            <td>
                                <div class="bio">
                                    <?= !empty($user['bio']) ? htmlspecialchars($user['bio']) : '—' ?>
                                </div>
                            </td>
                            <td>
                                <time class="date" datetime="<?= $user['created_at'] ?? '' ?>">
                                    <?= !empty($user['created_at']) ? date('d.m.Y', strtotime($user['created_at'])) : '—' ?>
                                </time>
                            </td>
                            <td>
                                <!-- 🔹 Кнопка удаления -->
                                <form method="POST" style="display:inline;" 
                                      onsubmit="return confirm('Удалить пользователя «<?= addslashes($user['display_name'] ?? $user['username']) ?>»?');">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn-delete">🗑️ Удалить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
        <?php else: ?>
            <!-- Показываем, если пользователей нет -->
            <div class="empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <p>Пользователи не найдены</p>
                <p style="margin-top: 10px; font-size: 14px;">
                    Нажмите « Добавить нового пользователя», чтобы создать первого
                </p>
            </div>
        <?php endif; ?>
        
    </div>
</body>
</html>