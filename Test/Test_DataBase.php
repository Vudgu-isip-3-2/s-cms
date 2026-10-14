<?php
require_once 'Database.php';

$host = 'localhost';
$dbname = 'dumproject';
$username = 'root';
$password = 'root';

$db = DataBase::getInstance($host,$dbname,$username,$password);
try
{
    $id = 1;
     if($db->isConnected())
        {
            echo("Успешное подключение к БД: ".$db->isConnected()."\n"."(0 - успешно / 1 - ошибочно)\n");
        }
    else
        {
            echo("Ошибка подключения к БД\n");
        }
    $selected = $db->queryOne("SELECT * FROM `users` WHERE id = ?",[$id]);
    echo("Полученные данные: ");
    print_r($selected);
   
}
    catch(PDOException $e)
    {
        echo("Ошибка! ".$e->getMessage());
    }
?>