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
   <div class="viewEvent">
        <?php foreach($query as $q){?>
            <div>
                <h2><?php echo $q['title'];?></h1>
                    <form method="POST">
                        <p class="date">Am:
                                <?php echo date('d.m.Y', strtotime($q['date'])); ?>
                            </p>
                            <p class="content"><?php echo $q['content'];?></p>
                        
                        <button class="delete" name="delete">Löschen</button>
                    </form>
                    <button class="edit" name="edit"><a href="edit.php?eventid=<?php echo $q['eventid']?>" >Bearbeiten</a></button>
                    
            </div>
        <?php } ?>    
        <button class="home"> <a href="index.php">Startseite</a></button>
   </div>

</body>
</html>