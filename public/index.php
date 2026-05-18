<?php
// Подключение менеджера тем
require_once __DIR__ . '/../lib/Theme/ThemeManager.php';
use Lib\Theme\ThemeManager;

// Инициализация менеджера тем (тема берется из конфига)
$themeManager = new ThemeManager(__DIR__ . '/../lib/Theme/config/theme_config.php');

ob_start();

// Основной контент страницы
?>
<div class="cms-content">
    <h1>Добро пожаловать в CMS</h1>
    <p>Сайт работает в фиксированной теме, заданной в конфигурационном файле.</p>
</div>
<?php
$content = ob_get_clean();

// Рендеринг с выбранной темой
$pageTitle = "Главная страница";
echo $themeManager->render($content, $pageTitle);
require_once __DIR__ . '/../lib/autoload.php';
require_once __DIR__ . '/../lib/Router.php';
require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/Config_class.php';
require_once __DIR__ . '/../lib/Main.php';
$router = new Router();
$params = $router->getParams();
foreach ($params as $key => $value) {
    if($key == 'route'&& $value == 'user'){
        include __DIR__ . '/../public/users.php';
    }
}
$app = new Main();
?>