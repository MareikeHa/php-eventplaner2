<?php

include "./config.php";


// Don't display server errors 
ini_set("display_errors", "on");
error_reporting(E_ALL);


// Get data to display on index page
$sql = "SELECT * FROM events";
$query = mysqli_query($conn, $sql);

// Create a new post
if (isset($_REQUEST['new_post'])) {
    $title = $_REQUEST['title'];
    $content = $_REQUEST['content'];

    $sql = "INSERT INTO events(title, content) VALUES('$title', '$content')";
    mysqli_query($conn, $sql);

    echo $sql;

    header("Location: index.php?info=added");
    exit();
}

// Get post data based on id
if (isset($_REQUEST['eventid'])) {
    $eventid = $_REQUEST['eventid'];
    $sql = "SELECT * FROM events WHERE eventid = $eventid";
    $query = mysqli_query($conn, $sql);
}

// Delete a post
if (isset($_REQUEST['delete'])) {
    $eventid = $_REQUEST['eventid'];

    $sql_delete_registrations = "DELETE FROM eventregistrations WHERE event_id = $eventid";
    if (!mysqli_query($conn, $sql_delete_registrations)) {
        echo "Error deleting eventregistrations: " . mysqli_error($conn);
    }

    $sql = "DELETE FROM events WHERE eventid = $eventid";
    mysqli_query($conn, $sql);

    header("Location: index.php");
    exit();
}

// Update a post
if (isset($_REQUEST['update'])) {
    $eventid = $_REQUEST['eventid'];
    $title = $_REQUEST['title'];
    $content = $_REQUEST['content'];
    $date = $_REQUEST['date'];

    $sql = "UPDATE events SET title = '$title', content = '$content', date = '$date' WHERE eventid = $eventid";
    mysqli_query($conn, $sql);

    header("Location: index.php");
    exit();
}



function checkUserRegistration($eventid, $userid)
{
    global $conn;

    // Überprüfe, ob $userId und $eventId nicht leer sind
    if (!empty($userid) && !empty($eventid)) {
        // Setze $eventId explizit
        $eventid = (int) $eventid;

        $stmt = mysqli_prepare($conn, "SELECT * FROM eventregistrations WHERE event_id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $eventid, $userid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $row_count = mysqli_stmt_num_rows($stmt);

        mysqli_stmt_close($stmt);

        return $row_count > 0;
    }

    return false; // oder handle den Fall, wenn $userId oder $eventId leer sind
}

function registerUserForEvent($eventid, $userid)
{
    global $conn;

    // Überprüfe zuerst, ob der Benutzer bereits für das Event angemeldet ist
    if (!checkUserRegistration($eventid, $userid)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO eventregistrations (event_id, user_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ii", $eventid, $userid);

        $response = array(); // Initialisiere ein leeres Array für die Antwort

        if (mysqli_stmt_execute($stmt)) {
            $response['success'] = true;
            $response['isRegistered'] = true;
            $response['message'] = "Erfolgreich in die Datenbank eingefügt.";
        } else {
            $response['error'] = "Fehler beim Einfügen in die Datenbank: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        $response['success'] = false;
        $response['isRegistered'] = true; // Benutzer ist bereits angemeldet
        $response['error'] = "Benutzer ist bereits für das Event angemeldet.";
    }

    // Sende die Antwort als JSON zurück
    header('Content-Type: application/json');
    echo json_encode($response);
}

function unregisterUserFromEvent($eventid, $userid)
{
    global $conn;

    // Überprüfe zuerst, ob der Benutzer für das Event angemeldet ist
    if (checkUserRegistration($eventid, $userid)) {
        $stmt = mysqli_prepare($conn, "DELETE FROM eventregistrations WHERE event_id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $eventid, $userid);

        $response = array(); // Initialisiere ein leeres Array für die Antwort

        if (mysqli_stmt_execute($stmt)) {
            $response['success'] = true;
            $response['isRegistered'] = false;
            $response['message'] = "Erfolgreich von der Veranstaltung abgemeldet.";
        } else {
            $response['success'] = false;
            $response['isRegistered'] = true;
            $response['error'] = "Fehler beim Löschen aus der Datenbank: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        $response['success'] = false;
        $response['isRegistered'] = false; // Benutzer ist nicht angemeldet
        $response['error'] = "Benutzer ist nicht für das Event angemeldet.";
    }

    // Sende die Antwort als JSON zurück
    header('Content-Type: application/json');
    echo json_encode($response);
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug-Ausgabe nach der Bedingung
    echo "Entered POST section.";

    // Hole den JSON-Rohinhalt
    $json_data = file_get_contents("php://input");

    echo "JSON Data: " . $json_data;
    // Dekodiere den JSON-Inhalt
    $data = json_decode($json_data);

    if (isset($data->eventid, $data->userid, $_GET['action'])) {
        $eventid = $data->eventid;
        $userid = $data->userid;

        // Debug-Ausgabe
        echo "Action: " . $_GET['action'] . ", EventID: " . $eventid . ", UserID: " . $userid;

        // Je nach Aktion (Anmeldung oder Abmeldung) die entsprechende Funktion aufrufen
        switch ($_GET['action']) {
            case 'register':
                registerUserForEvent($eventid, $userid);
                break;
                case 'unregister':
                    unregisterUserFromEvent($eventid, $userid);
                    break;
            // Weitere Aktionen hier hinzufügen, wenn erforderlich
        }
    } else {
        // Debug-Ausgabe, falls erforderliche Daten fehlen
        echo "Missing required data in POST request.";
    }
}

?>