<?php
/**
 * Класс Router - обновленная версия с поддержкой страниц
 */
class Router
{
    private array $params = [];

    public function __construct()
    {
        $this->parseUrl();
    }

    private function parseUrl(): void
    {
        // Собираем все параметры из URL
        $this->params = $_GET;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Главный метод маршрутизации
     * Решает, что показать пользователю
     */
    public function render(): void
    {
        // Если в URL есть параметр page_id (например, ?page_id=5)
        if (!empty($this->params['page_id'])) {
            $pageId = (int)$this->params['page_id'];
            
            // Подключаем шаблон одной страницы
            // Файл должен лежать по пути themes/default/post.php
            require_once __DIR__ . '/../themes/default/post.php';
            return; // Завершаем выполнение, чтобы не вывести ничего лишнего
        }

        // Если page_id нет — показываем главную страницу
        // Создайте файл themes/default/index.php, если его еще нет
        require_once __DIR__ . '/../themes/default/index.php';
    }
}