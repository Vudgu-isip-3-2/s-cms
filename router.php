<?php
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Параметры из адресной строки</h1>";

if (!empty($_GET)) {
    echo "<ul>";
    foreach ($_GET as $key => $value) {
        $key = htmlspecialchars($key);
        $value = htmlspecialchars($value);
        echo "<li><b>$key</b> = $value</li>";
    }
    echo "</ul>";
} else {
    echo "<p>В адресной строке нет параметров.</p>";
}
?>