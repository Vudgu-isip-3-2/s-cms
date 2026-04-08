<?php
/**
 * Main Class - Главный класс приложения
 */
class Main
{
    private $config;
    private $database;
    private $router;
    private $loadedClasses = [];
    
    public function __construct()
    {
        ErrorHandler::register(true, __DIR__ . '/../env/logs/errors.log');
        
        $this->loadConfiguration();
        $this->initializeRouter();
        $this->initializeDatabase();
        $this->displayLoadedClasses();
    }
    
    private function loadConfiguration(): void
    {
        try {
            $this->config = new Config();
            $this->loadedClasses['Config'] = 'работает';
        } catch (Exception $e) {
            $this->loadedClasses['Config'] = 'ошибка';
        }
    }
    
    private function initializeRouter(): void
    {
        try {
            $this->router = new Router();
            $this->loadedClasses['Router'] = 'работает';
        } catch (Exception $e) {
            $this->loadedClasses['Router'] = 'ошибка';
        }
    }
    
    private function initializeDatabase(): void
    {
        try {
            $host = 'mysql';
            $dbname = 's-cms';
            $username = 's-cms';
            $password = 'secret';
            
            $this->database = DataBase::getInstance($host, $dbname, $username, $password);
            
            if ($this->database->isConnected()) {
                $this->loadedClasses['Database'] = 'подключена';
            } else {
                $this->loadedClasses['Database'] = 'не подключена';
            }
        } catch (Exception $e) {
            $this->loadedClasses['Database'] = 'ошибка';
        }
    }
    
    private function displayLoadedClasses(): void
    {
        echo "Подключенные классы:<br>";
        
        foreach ($this->loadedClasses as $className => $status) {
            echo "$className: $status<br>";
        }
        
        echo "";
    }
}