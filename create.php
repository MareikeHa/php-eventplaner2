<?php

    include "./logic.php";

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Eventplaner</title>
</head>
<body>
    <header>     
        <h1>Eventplaner</h1>
    </header>
   <div class="createEvent">
        <form method="POST">
            <input type="text" placeholder="Event-Titel" class="eventTitle" name="title">
            <textarea name="content" class="content" cols="30" rows="10"></textarea>
            <input type="date" name="date">
            <button class="addEvent" name="new_post">Event hinzufügen</button>
        </form>
   </div>

</body>
</html>