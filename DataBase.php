<?php
class DataBase
{
    private $pdo; // переменная для храненения состояния объекта PDO
    private static $instance; // переменная для хранения экземпляра класса

    private $host; // имя хоста
    private $username; // имя пользователя
    private $password;//пароль пользователя
    private $dbname;//имя базы данных
    private $connected = false;// переменная для хранения состояния подключения
   public function __construct($host,$dbname,$username,$password)
   {
    // сохранение переменных с свойства класса
   $this->host = $host;
   $this->dbname = $dbname;
   $this->password = $password;
   $this->username = $username;

   $this->connect(); // вызов метода подключения
   
    
   }

   public function connect() // метод подключения к бд
   {
    try
    {
        $dtb = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4"; // строка подключения

        $this->pdo = new PDO($dtb,$this->username,$this->password,[ // обработка ошибок
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        $this->connected = true; // флаг успешного подключения
    }
    
    catch(PDOException $e)
    { // при ошибке выбрасываем исключение
        throw new Exception("Ошибка подключения к бд" . $e->getMessage());
    }
   }

   public static function getInstance($host,$dbname,$username,$password)
   { // функция проверки создания экземпляра класса
    if(self::$instance == null) 
        {
            self::$instance = new self($host,$dbname,$username,$password);
        }
        return self::$instance;
   }
    public function getConnection()
    {
        return $this->pdo;
    }

    public function isConnected() // функция проверки подключения
    {
        return $this->connected;
    }

    public function close()
    {
        $this->connected = false;
        $this->pdo = null;
    }

    public function quote($string)
    {
        return $this->pdo->quote($string);
    }

    public function query($sql,$params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function queryOne($sql,$params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function execute($sql,$params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function delete($table,$where,$params = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }
    public function update($table,$data,$where,$params = [])
    {
        $set = [];
        foreach($data as $field => $value)
            {
                $set[] = "{$field} = ?";
            }

        $sql = "UPDATE {$table} SET " . implode(', ',$set) . " WHERE {$where}";
        $aparams = array_merge(array_values($data),$params);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($aparams);
        return $stmt->rowCount();
    }
    public function insert($table,$data)
    {
        $fields = array_keys($data);
        $values = array_fill(0, count($fields), '?');
        $sql = "INSERT INTO $table (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $values) . ")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));

        return $this->pdo->lastInsertId();
    }
}



?>

