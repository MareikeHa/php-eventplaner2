<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

//Abfrage der Nutzer ID vom Login
$username = $_SESSION['username'];
$userid = $_SESSION['userid'];
?>
<?php
require_once("./logic.php");
require_once("./config.php");
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
        <button class="logout"> <a href="logout.php">Logout</a> </button>
    </header>

    <div class="user-greeting">
        <h3>Hallo
            <?php echo $username; ?>!
        </h3>

    </div>
    <div class="overviewEvents">
        <!-- Display any info -->
        <?php if (isset($_REQUEST['info'])) { ?>
            <?php if ($_REQUEST['info'] == "added") { ?>
                <div class="alert-success" role="alert">
                    Das Event wurde erfolgreich hinzugefügt
                </div>
            <?php } ?>
        <?php } ?>
        <div class="newEvent">
            <button> <a href="create.php">+ Neues Event anlegen</a></button>
        </div>
        <!-- Display posts from database -->
        <div class="events">
            <?php foreach ($query as $q) { ?>
                <div class="eventTable">
                    <div class="event-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo $q['title']; ?>
                            </h5>
                            <p class="card-text">
                                <?php echo substr($q['content'], 0, 50); ?>...
                            </p>
                            <p class="card-date">Am:
                                <?php echo date('d.m.Y', strtotime($q['date'])); ?>
                            </p>
                            <button class="moreInformations"> <a
                                    href="view.php?eventid=<?php echo (int) $q['eventid'] ?>">Mehr
                                    Informationen<span class="text-danger">&rarr;</span></a></button>
                            <?php
                            $isRegistered = checkUserRegistration($q['eventid'], $userid);
                            echo "Event ID: " . $q['eventid'] . ", User ID: " . $userid . ", isRegistered: " . $isRegistered . "<br>";
                            ?>
                            <button id="registerButton<?php echo $q['eventid']; ?>"
                                onclick="<?php echo $isRegistered ? 'unregisterForEvent' : 'registerForEvent'; ?>(<?php echo $q['eventid']; ?>, <?php echo $userid; ?>)">
                                <?php echo $isRegistered ? 'Abmelden' : 'Anmelden'; ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Create a new Post button -->

    </div>

    <script>
        function registerForEvent(eventid, userid) {
            // AJAX-Anfrage für die Anmeldung
            fetch('logic.php?action=register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    eventid: eventid,
                    userid: userid,
                }),
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(data => {
                    console.log(data);

                    //console.log("Response from server:", data);
                    //console.log(typeof data.isRegistered);
                    if (data.isRegistered !== null) {
                        console.log(data.isRegistered)
                        console.log("Benutzer ist bereits angemeldet");
                        const button = document.getElementById('registerButton' + eventid);
                        //console.log(button);
                        //console.error("Button not found or isRegistered is not a boolean");
                        if (button) {
                            button.innerText = data.isRegistered = 1 ? 'Abmelden' : 'Anmelden';
                            button.classList.toggle('registered', data.isRegistered);
                        }
                    } else {
                        console.error("Fehler bei der Anmeldung:", data.error || "Unbekannter Fehler");
                    }
                })

                .catch(error => console.error('Error:', error));

        }

        function unregisterForEvent(eventid, userid) {
            // AJAX-Anfrage für die Abmeldung
            fetch('logic.php?action=unregister', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    eventid: eventid,
                    userid: userid,
                }),
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(data => {
                    const button = document.getElementById('registerButton' + eventid);
                    if (button) {
                        button.innerText = 'Anmelden';
                        button.classList.remove('registered');
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>


</body>

</html>