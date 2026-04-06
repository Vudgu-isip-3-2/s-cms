<?php
echo "Helow world";
// Подключение менеджера тем
require_once __DIR__ . '/../lib/Theme/ThemeManager.php';

use Lib\Theme\ThemeManager;

// Инициализация менеджера тем (тема берется из конфига)
$themeManager = new ThemeManager(__DIR__ . '/../lib/Theme/theme_config.php');

ob_start();

// Основной контент страницы
?>
<div class="cms-content">
    <h1>Добро пожаловать в CMS</h1>
    <p>Сайт работает в фиксированной теме, заданной в конфигурационном файле.</p>
    <!-- Остальной контент -->
</div>
<?php
$content = ob_get_clean();

// Рендеринг с выбранной темой
$pageTitle = "Главная страница";
echo $themeManager->render($content, $pageTitle);
?>