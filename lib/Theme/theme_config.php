<?php
// Конфигурация тем оформления
return [
    /**
     * Активная тема
     */
    'active_theme' => 'light',  // Меняйте здесь для переключения темы
    /**
     * Список доступных тем
     */
    'themes' => [
        'light' => [
            'name' => 'Светлая тема',
            'description' => 'Светлая тема для работы',
            'version' => '1.0.0',
            'author' => 'Admin',
            'enabled' => true,        // Включена/отключена
            'css' => 'light.css',
            'file' => 'light.php',
            'settings' => [
                'container_width' => '1200px',
                'primary_color' => '#667eea',
                'secondary_color' => '#764ba2',
                'font_family' => 'Segoe UI, Arial, sans-serif',
                'border_radius' => '8px'
            ]
        ]
    ]
];
?>
