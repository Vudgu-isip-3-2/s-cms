<?php
require_once __DIR__ . '/../lib/autoload.php';
require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/Router.php';
require_once __DIR__ . '/../lib/Config_class.php';
require_once __DIR__ . '/../lib/Main.php';

$app = new Main();
$db  = $app->getDatabase();

$password = 'admin123';                       // ← пароль, который вы будете вводить при логине
$hash     = password_hash($password, PASSWORD_DEFAULT);

$id = $db->insert('user', [
    'username'      => 'admin',
    'email'         => 'admin@local',
    'last_name'     => 'Админ',
    'first_name'    => 'Админ',
    'password_hash' => $hash,
]);

echo "Пользователь создан, id = $id<br>";
echo "Логин: admin<br>";
echo "Пароль: $password<br>";
echo "<br>Удалите этот файл после использования!";