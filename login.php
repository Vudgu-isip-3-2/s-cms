<!-- <!DOCTYPE html> -->
<html>
<head>
<title>test-version</title>
<meta charset="utf-8">
</head>
<body>
<h2>Введите свои данные:</h2>
<form action="login.php" method="POST">
<p>Введите логин: <input type="text" name="login" /></p>
<p>Введите пароль: <input type="text" name="password" /></p>
<p>Подтверждение пароля: <input type="text" name="password" /></p>
<input type="submit" value="Отправить">
</form>
<?php
// $login = "не определен";
// $password = "не определен";


if(isset($_POST["login"])){
    $login = $_POST["login"];
     $password = $_POST["password"];
    $conn = new mysqli("localhost", "root", "", "cms");
    $sql = "SELECT * FROM user where log_in = '$login' and password = '$password'";
//-- /and password = $password";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        echo '<br><a href="profile.php?login=' . $login . '"> Перейти в мой профиль</a>';
        $session_hash = bin2hex(random_bytes(32)); 
    $user_id = $result->fetch_assoc()['idpolsovatel'];
    $expires_at = date('Y-m-d H:i:s', time() + 86400); // Через 1 день    
    $insert_sql = "INSERT INTO sessions (user_id, session_hash) 
        VALUES ($user_id, '$session_hash')";
     $conn->query($insert_sql);
    setcookie("session_token", $session_hash, time() + 86400, "/");
    echo "Токен: " . $session_hash . "<br><br>";
    }
}
if(isset($_POST["password"])){
  
    $age = $_POST["password"];
}?>
</body>
</html>