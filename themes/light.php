<?php
/**
 * Светлая тема оформления
*/
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="<?php echo $themeCssUrl; ?>">
    
    <?php
    $settings = $themeManager->getThemeSettings(); // Доп метатеги из настроек темы
    if (isset($settings['primary_color'])): ?>
    <style>
        :root {
            --primary-color: <?php echo $settings['primary_color']; ?>;
            --secondary-color: <?php echo $settings['secondary_color']; ?>;
            --container-width: <?php echo $settings['container_width'] ?? '1200px'; ?>;
        }
    </style>
    <?php endif; ?>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">Мой Сайт</div>
            </div>
        </div>
    </header>
    
    <main>
        <div class="container">
            <?php if ($pageContent): ?>
                <?php echo $pageContent; ?>
            <?php else: ?>
                <div class="card">
                    <h1>Добро пожаловать!</h1>
                    <p>Текущая тема: <strong><?php echo $themeInfo['name']; ?></strong></p>
                    <p><?php echo $themeInfo['description']; ?></p>
                    
                    <div class="info-block">
                        <h3>Информация для разработчика</h3>
                        <p>Для смены темы отредактируйте файл:</p>
                        <code>lib/Theme/theme_config.php</code>
                        <p>Измените значение <strong>'active_theme'</strong> на нужную тему.</p>
                    </div>
                </div>
                
                <div class="card">
                    <h3>Особенности системы:</h3>
                    <ul>
                        <li>Тема выбирается в конфигурационном файле</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p class="dev-note">Тема заданa в конфигурационном файле</p>
        </div>
    </footer>
</body>
</html>
