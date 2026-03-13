<?php

class View {
    private $data = [];
    private $viewDir = __DIR__ . '/../views/';
    
    public function set($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }
    
    public function render($view) {
        extract($this->data);
        include $this->viewDir . $view . '.html';
    }
}