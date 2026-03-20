<?php

/**
 * Класс Router
 * Простой прототип обработчика адресной строки.
 * Получает параметры из URL и позволяет их вывести.
 */
class Router
{
    /**
     * Массив параметров из адресной строки
     */
    private array $params = [];

    /**
     * Конструктор класса
     * Вызывается автоматически при создании объекта
     */
    public function __construct()
    {
        // При создании объекта сразу разбираем адресную строку
        $this->parseUrl();
    }

    /**
     * Метод разбора URL
     * Получает параметры из глобального массива $_GET
     */
    private function parseUrl(): void
    {
        // $_GET содержит все параметры после знака ?
        // Например: site.com?name=Ivan&age=20
        $this->params = $_GET;
    }

    /**
     * Метод возвращает массив параметров
     * Можно использовать в других частях программы
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Метод выводит параметры в браузер
     */
    public function render(): void
    {
        // Проверяем, есть ли параметры
        if (empty($this->params)) {
            echo "Параметры не переданы";
            return;
        }

        // Заголовок вывода
        echo "<h2>Параметры из адресной строки</h2>";

        echo "<ul>";

        // Перебираем все параметры
        foreach ($this->params as $key => $value) {

            // htmlspecialchars защищает от HTML-инъекций
            $key = htmlspecialchars($key);
            $value = htmlspecialchars($value);

            // Выводим параметр
            echo "<li>$key = $value</li>";
        }

        echo "</ul>";
    }
}