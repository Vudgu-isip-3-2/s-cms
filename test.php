    <?php
    requre_once("D:\github work\DataBase.php");
    $db = DataBase::getConnection();
    if(!$db)
        {
            die("Ошибка подключения к бд");
        }
    
    $query = "SELECT * FROM `categories`";
    $table = $db->query($query, array(10,1));
    echo "<pre>";
    var_dump($table);
    echo "</pre>";
    ?>