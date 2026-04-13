<?php
// users.php - Страница вывода списка пользователей

require_once __DIR__ . '/../lib/DataBase.php';
require_once __DIR__ . '/../lib/Config_Class.php';


$config = new Config(__DIR__ . '/../.env');  // ← создаём объект!

$db = DataBase::getInstance('mysql', 's-cms', 's-cms', 'secret');
    
    if (!$db->isConnected()) {
        throw new Exception("Не удалось подключиться к базе данных");
        echo "Не удалось подключиться к базе данных";
    }
    
    // Запрос к базе
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
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        .error {
            background: #fee;
            color: #c00;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #c00;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 15px 25px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        .stat-card .number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .stat-card .label {
            color: #7f8c8d;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #34495e;
            color: #fff;
            padding: 14px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 14px 12px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #ecf0f1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #7f8c8d;
            font-size: 18px;
            overflow: hidden;
        }
        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-details strong {
            display: block;
            color: #2c3e50;
        }
        .user-details small {
            color: #7f8c8d;
            font-family: monospace;
        }
        .role {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .role.admin { background: #e74c3c20; color: #c0392b; }
        .role.editor { background: #f39c1220; color: #d35400; }
        .role.author { background: #3498db20; color: #2980b9; }
        .role.user { background: #95a5a620; color: #7f8c8d; }
        
        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status.active {
            background: #2ecc7120;
            color: #27ae60;
        }
        .status.active::before {
            content: '';
            width: 8px;
            height: 8px;
            background: #2ecc71;
            border-radius: 50%;
        }
        .status.inactive {
            background: #95a5a620;
            color: #7f8c8d;
        }
        .bio {
            color: #555;
            font-size: 14px;
            max-width: 300px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .date {
            color: #7f8c8d;
            font-size: 13px;
            white-space: nowrap;
        }
        .empty {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
        .empty svg {
            width: 64px;
            height: 64px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        @media (max-width: 768px) {
            .container { padding: 15px; }
            table { font-size: 14px; }
            th, td { padding: 10px 8px; }
            .bio { max-width: 200px; }
            .user-info { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1> Пользователи системы</h1>
        
        <?php if (isset($error)): ?>
            <div class="error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
             
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="avatar">
                                        <?php if ($user['avatar_path']): ?>
                                            <img src="<?= htmlspecialchars($user['avatar_path']) ?>" 
                                                 alt="<?= htmlspecialchars($user['display_name']) ?>">
                                        <?php else: ?>
                                            <?= mb_strtoupper(mb_substr($user['display_name'], 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="user-details">
                                        <strong><?= htmlspecialchars($user['display_name']) ?></strong>
                                        <small>@<?= htmlspecialchars($user['username']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="role <?= htmlspecialchars($user['role']) ?>">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="status <?= $user['is_active'] ? 'active' : 'inactive' ?>">
                                    <?= $user['is_active'] ? 'Активен' : 'Неактивен' ?>
                                </span>
                            </td>
                            <td>
                                <div class="bio">
                                    <?= $user['bio'] ? htmlspecialchars($user['bio']) : '—' ?>
                                </div>
                            </td>
                            <td>
                                <time class="date" datetime="<?= $user['created_at'] ?>">
                                    <?= date('d.m.Y', strtotime($user['created_at'])) ?>
                                </time>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    </div>
</body>
</html>