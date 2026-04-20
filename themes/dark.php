<?php
/**
 * Тёмная тема оформления
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
    $settings = $themeManager->getThemeSettings();
    ?>
    <style>
        :root {
            --primary-color: <?php echo $settings['primary_color'] ?? '#9b59b6'; ?>;
            --secondary-color: <?php echo $settings['secondary_color'] ?? '#e74c3c'; ?>;
            --container-width: <?php echo $settings['container_width'] ?? '1200px'; ?>;
            --border-radius: <?php echo $settings['border_radius'] ?? '8px'; ?>;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <span class="logo-text">Мой Сайт</span>
                </div>
            </div>
        </div>
    </header>
    
    <main>
        <div class="container">
            <?php if ($pageContent): ?>
                <?php echo $pageContent; ?>
            <?php else: ?>
                <div class="hero-section">
                    <h1>Добро пожаловать</h1>
                    <p>Сайт работает в тёмной теме оформления</p>
                    <button class="btn btn-large" onclick="alert('Добро пожаловать!')">
                        Начать работу
                    </button>
                </div>
                
                <!-- Карточка с информацией о теме -->
                <div class="card">
                    <div class="card-header">
                        <span class="card-icon"></span>
                        <h2><?php echo $themeInfo['name']; ?></h2>
                    </div>
                    <p><?php echo $themeInfo['description']; ?></p>
                    
                    <div class="info-block">
                        <h3>Информация для разработчика</h3>
                        <p>Текущая тема: <strong><?php echo $themeInfo['name']; ?></strong></p>
                        <p>Для смены темы отредактируйте файл:</p>
                        <code>lib/Theme/theme_config.php</code>
                        <p>Измените значение <strong>'active_theme'</strong> на нужную тему:</p>
                        <pre>'active_theme' => 'light'  // Светлая тема
'active_theme' => 'dark'   // Тёмная тема</pre>
                    </div>
                    
                    <div class="theme-features">
                        <h3>Особенности тёмной темы:</h3>
                        <ul>
                            <li>✓ Меньшая нагрузка на глаза при длительной работе</li>
                            <li>✓ Экономия энергии на OLED-экранах</li>
                            <li>✓ Стильный современный внешний вид</li>
                            <li>✓ Улучшенная читаемость в тёмных помещениях</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Карточка с преимуществами -->
                <div class="card">
                    <h3>Преимущества использования тёмной темы:</h3>
                    <div class="advantages-grid">
                        <div class="advantage-item">
                            <div class="advantage-icon"></div>
                            <h4>Комфорт для глаз</h4>
                            <p>Снижает усталость глаз при длительной работе за компьютером</p>
                        </div>
                        <div class="advantage-item">
                            <div class="advantage-icon">🔋</div>
                            <h4>Экономия энергии</h4>
                            <p>На OLED-дисплеях потребляет на 30-40% меньше энергии</p>
                        </div>
                        <div class="advantage-item">
                            <div class="advantage-icon"></div>
                            <h4>Эстетика</h4>
                            <p>Современный и стильный внешний вид</p>
                        </div>
                        <div class="advantage-item">
                            <div class="advantage-icon"></div>
                            <h4>Ночной режим</h4>
                            <p>Идеально подходит для использования в тёмное время суток</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p class="dev-note">Тема задана в конфигурационном файле</p>
        </div>
    </footer>
</body>
</html>