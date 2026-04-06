# Класс для работы с базами данных DataBase
## Данный класс необходим для работы с базовыми запросами к бд.
### Разберу подробнее

### Ниже представлены методы, необходимые для управления классом:

* **private $pdo** - переменная для храненения состояния объекта PDO
*  **private static $instance** - переменная для хранения единственного экземпляра класса (singletone паттерн)
*  **private $host;** - переменная для хранения имени хоста
*  **private $username;** - переменная для хранения имени пользователя
*  **private $password;** - переменная для хранения пароля пользователя
*  **private $dbname;** - переменная для хранения имени имени бд
*  **private $connected = false;** - переменная для хранения состояния подключения

```php
    private $pdo;
    private static $instance; 

    private $host;
    private $username;
    private $password;
    private $dbname;
    private $connected = false;
```

### Функция _construct, создающая объект при успешном подключении.
В качестве аргументов в функцию передаются: 
* **имя хоста**
* **имя бд**
* **имя пользователя**
* **пароль пользователя**<br>
В теле функции происходит сохранение переменных в свойства класса. После происходит вызов функции connect.
```php
public function __construct($host,$dbname,$username,$password)
   {

   $this->host = $host;
   $this->dbname = $dbname;
   $this->password = $password;
   $this->username = $username;

   $this->connect(); // вызов метода подключения
   
    
   }
```
## Функция connect 
### Данная функция осуществляет подключение к бд.
### В данной функции присутствуют 2 блока:
* try
* catch
Функции в блоке try пытается выполнить подключение к существующей бд, в блоке catch происходит обработка ошибок.<br>
### Блок try:
* **$$dtb = "mysql:host={$this->host};dbname={$this->dbname};** - создает DSN, определяющий сведения о бд.
* **$this->pdo = new PDO** - создается новый объект PDO, создающий базу для подключения.
  * **$this->username** - указывает на переменную username в данном экземпляре класса
  * **$this->dbname** - указывает на переменную dbname в данном экземпляре класса
  * **charset=utf8mb4"** - указывает на кодировку символов в формате UTF-8 <br>
  **PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION** - определяет способ обработки ошибок. Метод PDO::ERRMODE_EXCEPTION генерирует PDOException для вывода строки ошибки.
  **PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC** - определяет метод выборки по умолчанию.<br>
  **Метод PDO::FETCH_ASSOC** определяет вывод результате в виде ассоциативного массива,индексированный по имени столбца, как в наборе результатов<br>
  **PDO::ATTR_EMULATE_PREPARES => false** - в значении false именно использует штатный механизм СУБД для подготовки запроса и затем отдельным обращением передаёт данные для этого запроса.<br>
  **$this->connected = true** - устанавливет флаг успешного подключения
### Блок catch:
* PDOException $e - исключение, возникающее в результате ошибки подключения к бд, ему присваивается переменная 'e'.
* throw new Exceotion - при ошибке подключения, создается новый экземпляр исключения Exception 
* $e->getMessage() - здесь мы получаем сообщение об ошибке, от переменной $e, свяазнной с исключением Exception.

```php
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
```
## Функция  getInstance
### Данная функция осуществлет проверку наличия экземпляров класса


``` php
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
```
## Функция getConnection
### Данная функция возвращает объект PDO после подключения

``` php
   public function getConnection()
    {
        return $this->pdo;
    }
```


## Функция isConnected
### Данная функция возвращает состояние подключения

```php
 public function isConnected() // функция проверки подключения
    {
        return $this->connected;
    }
```
## Функция close
### Данная функция осуществляет остановку соединения
```php
public function close()
    {
        $this->connected = false;
        $this->pdo = null;
    }
```
## Функция quote
### Данная функция оборачивает строку, принятую в качестве аргумента в кавычки для дальнейшего безопасного встраивания в SQL-запрос. Возвращает результат преобразования строки при помощи quote.
```php
public function quote($string)
    {
        return $this->pdo->quote($string);
    }
```
## Функция query
### Данная функция осуществляет запросы к бд
```php
 public function query($sql,$params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
```

## 