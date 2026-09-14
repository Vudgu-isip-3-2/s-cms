<?php
namespace Lib;

use DataBase;
use Config;

class DbFactory
{
    private static ?DataBase $instance = null;

    public static function get(): DataBase
    {
        if (self::$instance === null) {
            // Создаём Config и грузим .env из корня проекта
            $config = new Config(__DIR__ . '/../.env');

            self::$instance = DataBase::getInstance(
                $config->get('db.host', 'db'),
                $config->get('db.name', 'cms'),
                $config->get('db.user', 'root'),
                $config->get('db.pass', 'root')
            );
        }
        return self::$instance;
    }
}