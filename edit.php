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
   <div class="editEvent">
        <?php foreach($query as $q){ ?>
            <form method="POST">
                <input type="text" hidden value='<?php echo $q['eventid']?>' name="eventid">
                <input type="text" placeholder="Event-Titel" class="eventTitle" name="title" value="<?php echo $q['title']?>">
                <textarea name="content" class="content" cols="30" rows="10"><?php echo $q['content']?></textarea>
                <input type="date" name="date" value="<?php echo $q['date']?>">
                <button class="update" name="update">Aktualisieren</button>
            </form>
        <?php } ?>    
   </div>

</body>
</html>