<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to login if not logged in
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO registrations (user_id, event_id) VALUES ('$user_id', '$event_id')";
    if ($conn->query($sql) === TRUE) {
        echo "Successfully registered for the event!";
    } else {
        echo "Error: " . $conn->error;
    }
}

$event_id = $_GET['event_id'];
$sql = "SELECT * FROM events WHERE id = $event_id";
$result = $conn->query($sql);
$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration</title>
</head>
<body>
    <h2>Register for Event: <?php echo $event['name']; ?></h2>
    <form action="event_registration.php" method="POST">
        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
        <button type="submit">Register</button>
    </form>
</body>
</html>
