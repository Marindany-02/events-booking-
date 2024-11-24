<?php
include('../config/db.php');
include('header_footer.php');
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $sql = "SELECT * FROM events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
    } else {
        echo "Event not found.";
        exit;
    }
} else {
    header("Location: admin_dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Event</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
        }
        .event-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .event-image-container {
            border: 5px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }
        .event-image {
            width: 100%;
            height: 550px;
            border-radius: 5px;
        }
        .download-btn, .update-btn, .delete-btn {
            margin-top: 10px;
        }
        .event-info {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container event-container">
    <div class="row">
        <!-- Image Column -->
        <div class="col-md-6">
            <div class="event-image-container">
                <!-- Download Button -->
                <a href="../assets/images/<?php echo htmlspecialchars($event['event_image']); ?>" download class="btn btn-outline-primary mb-3">Download Image</a>
                <img src="../assets/images/<?php echo htmlspecialchars($event['event_image']); ?>" class="event-image" alt="Event Image">
            </div>
        </div>

        <!-- Information Column -->
        <div class="col-md-6">
            <h2 class="mb-4"><?php echo htmlspecialchars($event['name']); ?></h2>
            
            <div class="event-info">
                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['date'])); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                <p><strong>Capacity:</strong> <?php echo nl2br(htmlspecialchars($event['capacity'])); ?></p>
                <p><strong>Cost Per Spot:</strong> <?php echo nl2br(htmlspecialchars($event['cost'])); ?></p>
                <p><strong>Booking Dateline:</strong> <?php echo nl2br(htmlspecialchars($event['booking_dateline'])); ?></p>
                <p><strong>Description:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
            </div>

            <a href="user_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
