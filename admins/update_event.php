<?php
// Include database connection
include('../config/db.php');
include('admin_sidebar.php');

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    
    // Fetch event details from the database
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
}

// Handle form submission for updating event details
if (isset($_POST['update_event'])) {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    // Update event details in the database
    $update_sql = "UPDATE events SET name = ?, date = ?, location = ?, description = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $name, $date, $location, $description, $event_id);

    if ($update_stmt->execute()) {
        header("Location: event_management.php"); // Redirect after successful update
        exit;
    } else {
        echo "Error updating event.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Event</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .event-form-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .event-form-container input, .event-form-container textarea {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="container event-form-container">
    <h2 class="text-center mb-4">Update Event</h2>

    <!-- Event Update Form -->
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Event Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($event['name']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="date" class="form-label">Event Date</label>
            <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($event['date']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>

        <button type="submit" name="update_event" class="btn btn-success w-100">Update Event</button>
    </form>

    <!-- Back to Dashboard Button -->
    <a href="event_management.php" class="btn btn-secondary mt-3 w-100">Back to Dashboard</a>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
