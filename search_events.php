<?php
include('config/db.php');

$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sql = "SELECT * FROM events WHERE name LIKE '%$search%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Events</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <form action="search_events.php" method="GET">
        <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Search events">
        <button type="submit">Search</button>
    </form>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='event'>";
            echo "<h3>" . $row['name'] . "</h3>";
            echo "<p>Date: " . $row['date'] . "</p>";
            echo "<p>Location: " . $row['location'] . "</p>";
            echo "<a href='event_registration.php?event_id=" . $row['id'] . "'>Register</a>";
            echo "</div><br>";
        }
    } else {
        echo "No events found.";
    }
    ?>
</body>
</html>
