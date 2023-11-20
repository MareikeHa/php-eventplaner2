
<?php 
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=eventplanner', 'root', '');
 
if(isset($_GET['login'])) {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $result = $statement->execute(array('email' => $email));
    $user = $statement->fetch();
        
    //Überprüfung des Passworts
    if ($user !== false && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['userid'] = $user['userid'];
        header('Location: index.php');
        exit();
    } else {
        $errorMessage = "E-Mail oder Passwort war ungültig<br>";
    }
}
?>
<!DOCTYPE html> 
<html> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>    
</head> 
<body>
 
<?php 
if(isset($errorMessage)) {
    echo $errorMessage;
}
?>
<header>
    <h1>Login</h1>
</header>
<form action="?login=1" method="post" class="loginForm">
    E-Mail:<br>
    <input type="email" size="40" maxlength="250" name="email"><br><br>

    Username:<br>
    <input type="text" size="40" maxlength="250" name="username"><br><br>
 
    Dein Passwort:<br>
    <input type="password" size="40"  maxlength="250" name="password"><br>
 
    <input type="submit" value="Abschicken">
</form> 

<p class=registration> Sie haben noch kein Konto? <a href="registration.php">Registrieren</a> </p>
</body>
</html>