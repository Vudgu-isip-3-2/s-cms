<?php
namespace Lib\Theme;

class ThemeManager {
    private $currentTheme;
    private $themesConfig;
    private $themesPath;
    private $assetsUrl;
    private $configFile;
    
    public function __construct($configFile = null) {
        // Определение путей
        $this->themesPath = __DIR__ . '/themes/';
        $this->assetsUrl = '/assets/themes/';
        $this->configFile = $configFile ?: __DIR__ . '/theme_config.php';
        
        // Загрузка конфигурации
        $this->loadThemesConfig();
        
        // Инициализация текущей темы из конфига
        $this->initTheme();
    }
    
    /**
     * Загрузка конфигурации тем из внешнего файла
     */
    private function loadThemesConfig() {
        if (file_exists($this->configFile)) {
            // Загружаем конфигурацию из файла
            $config = require $this->configFile;
            $this->themesConfig = $config['themes'] ?? [];
            $this->currentTheme = $config['active_theme'] ?? null;
        } else {
            // Конфигурация по умолчанию
            $this->themesConfig = [
                'light' => [
                    'name' => 'Светлая тема',
                    'enabled' => true,
                    'css' => 'light.css',
                    'file' => 'light.php',
                    'description' => 'Классическая светлая тема'
                ]
            ];
            $this->currentTheme = 'light';
        }
    }
    
    /**
     * Инициализация текущей темы (только из конфига)
     */
    private function initTheme() {
        // Проверяем, существует ли указанная в конфиге тема
        if (!$this->isThemeValid($this->currentTheme)) {
            // Если тема не валидна, берем первую доступную
            $this->currentTheme = $this->getFirstAvailableTheme();
        }
        
        // Дополнительно проверяем, включена ли тема
        if (!$this->isThemeEnabled($this->currentTheme)) {
            $this->currentTheme = $this->getFirstAvailableTheme();
        }
        
        $_SESSION['current_theme'] = $this->currentTheme;
    }
    
    /**
     * Проверка существования темы
     */
    private function isThemeValid($themeId) {
        return isset($this->themesConfig[$themeId]);
    }
    
    /**
     * Проверка, включена ли тема
     */
    private function isThemeEnabled($themeId) {
        return isset($this->themesConfig[$themeId]) && 
               ($this->themesConfig[$themeId]['enabled'] ?? true);
    }
    
    /**
     * Получение первой доступной (включенной) темы
     */
    private function getFirstAvailableTheme() {
        foreach ($this->themesConfig as $id => $theme) {
            if ($theme['enabled'] ?? true) {
                return $id;
            }
        }
        return array_key_first($this->themesConfig);
    }
    
    /**
     * Получение текущей темы
     */
    public function getCurrentTheme() {
        return $this->currentTheme;
    }
    
    /**
     * Получение информации о текущей теме
     */
    public function getCurrentThemeInfo() {
        return $this->themesConfig[$this->currentTheme] ?? null;
    }
    
    /**
     * Получение всех доступных тем (только для информации)
     */
    public function getAllThemes() {
        $themes = [];
        foreach ($this->themesConfig as $id => $theme) {
            $themes[$id] = $theme;
            $themes[$id]['id'] = $id;
            $themes[$id]['active'] = ($id === $this->currentTheme);
        }
        return $themes;
    }
    
    /**
     * Получение пути к файлу темы
     */
    private function getThemeFile() {
        $themeInfo = $this->getCurrentThemeInfo();
        if ($themeInfo && isset($themeInfo['file'])) {
            return $this->themesPath . $themeInfo['file'];
        }
        return $this->themesPath . 'light.php';
    }
    
    /**
     * Получение URL CSS темы
     */
    private function getThemeCssUrl() {
        $themeInfo = $this->getCurrentThemeInfo();
        if ($themeInfo && isset($themeInfo['css'])) {
            return $this->assetsUrl . $themeInfo['css'];
        }
        return $this->assetsUrl . 'light.css';
    }
    
    /**
     * Рендеринг страницы с текущей темой
     */
    public function render($content = null, $title = null) {
        $themeFile = $this->getThemeFile();
        
        if (!file_exists($themeFile)) {
            return "<div style='padding: 20px; background: #f00; color: #fff;'>
                        Ошибка: Файл темы не найден: {$themeFile}
                    </div>";
        }
        
        // Подготовка переменных для шаблона
        $themeManager = $this;
        $currentTheme = $this->currentTheme;
        $themeInfo = $this->getCurrentThemeInfo();
        $themeCssUrl = $this->getThemeCssUrl();
        $pageTitle = $title ?? 'Мой сайт';
        $pageContent = $content;
        
        // Буферизация вывода
        ob_start();
        include $themeFile;
        return ob_get_clean();
    }
    
    /**
     * Получение настроек темы
     */
    public function getThemeSettings($key = null) {
        $themeInfo = $this->getCurrentThemeInfo();
        $settings = $themeInfo['settings'] ?? [];
        
        if ($key) {
            return $settings[$key] ?? null;
        }
        return $settings;
    }
    
    /**
     * Проверка, разрешено ли переключение тем пользователем
     * Всегда возвращает false, так как переключение только в конфиге
     */
    public function allowUserSwitch() {
        return false; // Пользователь не может переключать темы
    }
}
?>
