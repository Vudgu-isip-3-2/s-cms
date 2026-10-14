<?php
// Если файл существует - отдаем его
if (file_exists($_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'])) {
    return false;
}

// Иначе обрабатываем через index.php
echo "<h1>Запрос обработан через index.php</h1>";
echo "<p>Путь: " . $_SERVER['REQUEST_URI'] . "</p>";
?>