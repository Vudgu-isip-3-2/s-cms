<?php
$url = "http://localhost/login.php"; // путь к файлу со скриптом авторизации (заглушка)
$cookie_file = DIR . '/cookies.txt';

$post_data = [
    'username' => 'admin' //заглушка
    'password' => 'password123' //заглушка
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
// включаем сохранение кук в файл
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); 
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo "Ответ сервера: " . $response . PHP_EOL;

// проверка на авториза2ию пользователя
if ($info['http_code'] == 200 && $response === 'success') {
    echo "Пользователь успешно авторизован";
} else {
    echo "Ошибка авторизации";
}

// удаляем временный файл с куками после теста
if (file_exists($cookie_file)) unlink($cookie_file);
?>