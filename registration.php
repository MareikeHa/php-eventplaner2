<?php 
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=eventplanner', 'root', '');
?>
<!DOCTYPE html> 
<html> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Registrierung</title>     
</head> 
<body>
 
<?php
$showFormular = true; //Variable ob das Registrierungsformular anezeigt werden soll
 
if(isset($_GET['register'])) {
    $error = false;
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
  
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Bitte eine gültige E-Mail-Adresse eingeben<br>';
        $error = true;
    }
    if(strlen($username) == 0) {
        echo 'Bitte ein Username angeben<br>';
        $error = true;
    }     
    if(strlen($password) == 0) {
        echo 'Bitte ein Passwort angeben<br>';
        $error = true;
    }
    if($password != $password2) {
        echo 'Die Passwörter müssen übereinstimmen<br>';
        $error = true;
    }
    
    //Überprüfe, dass die E-Mail-Adresse noch nicht registriert wurde
    if(!$error) { 
        $statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $result = $statement->execute(array('email' => $email));
        $user = $statement->fetch();
        
        if($user !== false) {
            echo 'Diese E-Mail-Adresse ist bereits vergeben<br>';
            $error = true;
        }    
    }
    
    //Keine Fehler, wir können den Nutzer registrieren
    if(!$error) {    
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $statement = $pdo->prepare("INSERT INTO users (email, username, password) VALUES (:email, :username, :password)");
        $result = $statement->execute(array('email' => $email, 'username' => $username, 'password' => $password_hash));
        
        if($result) {        
            echo 'Du wurdest erfolgreich registriert. <a href="login.php">Zum Login</a>';
            $showFormular = false;
        } else {
            echo 'Beim Abspeichern ist leider ein Fehler aufgetreten<br>';
        }
    } 
}
 
if($showFormular) {
?>
<header>
    <h1>Registrieren</h1>
</header>
<form action="?register=1" method="post" class="registrationForm">
    E-Mail:<br>
    <input type="email" size="40" maxlength="250" name="email"><br><br>

    Username:<br>
    <input  type="text" size="40" maxlength="250" name="username"><br><br>

    Dein Passwort:<br>
    <input type="password" size="40"  maxlength="250" name="password"><br>
 
    Passwort wiederholen:<br>
    <input type="password" size="40" maxlength="250" name="password2"><br><br>
 
    <input type="submit" value="Abschicken">
</form>
<p class=login> Sie haben bereits ein Konto? <a href="login.php">Login</a> </p>
<?php
}
?>
 
</body>
</html>