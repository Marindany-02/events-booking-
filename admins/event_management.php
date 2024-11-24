<?php
// Database connection (make sure it's correctly included or initialized)
include('../config/db.php');
include('admin_sidebar.php');

// Fetch all events by event_id (fetching all events as an example)
$sql = "SELECT * FROM events"; // This fetches all events
$result = $conn->query($sql);

// Error handling in case of query failure
if ($result === false) {
    echo "Error in SQL query: " . $conn->error;
    exit;
}

// Handle deletion of events if a delete request is made
if (isset($_GET['delete_event_id'])) {
    $delete_event_id = $_GET['delete_event_id'];
    $sql = "DELETE FROM events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_event_id);

    if ($stmt->execute()) {
        header("Location: event_management.php");
        exit;
    } else {
        $error = "Error deleting event: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
       /* Sidebar styling */
/* Ensure the body takes the full height of the viewport */
html, body {
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
}

/* Sidebar styling */
.sidebar {
    width: 250px; /* Adjust as needed */
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    background-color: #f8f9fa;
    padding-top: 20px;
    z-index: 1000; /* Ensure the sidebar is on top */
    overflow-y: auto; /* Allows scrolling if content overflows */
}

/* Main content styling */
.content {
    margin-left: 250px; /* Same as sidebar width */
    padding: 20px;
    position: relative;
    flex-grow: 1; /* Ensures content takes up remaining space */
}

/* Adjust for smaller screens */
@media (max-width: 768px) {
    .content {
        margin-left: 0;
        padding: 10px;
    }
}

/* Style the event images to ensure uniform size */
.event-card img {
    width: 100%;
    height: 200px; 
    object-fit: cover;
    border-radius: 8px;
}

/* Footer styling */
footer {
    background-color: #343a40;
    color: #ffffff;
    padding: 20px 0;
    text-align: center;
    position: relative;
    width: 100%;
    margin-top: auto; /* Pushes the footer to the bottom */
}

    </style>
</head>
<body>

<!-- Main Content Section -->
<!-- Sidebar -->
<div class="sidebar">
    <?php include('admin_sidebar.php'); ?>
</div>

<!-- Main content -->
<div class="content">
    <h2 class="text-center mb-4">Event Management</h2>
    
    <!-- Add Event Button -->
    <div class="text-right mb-3">
        <a href="add_event.php" class="btn btn-success">Add New Event</a>
        <a href="check_payment.php" class="btn btn-success">Check Events Payment</a>
    </div>
   
   

    <!-- Display any errors -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Event List -->
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card event-card">
                        <img src="../assets/images/<?php echo htmlspecialchars($row['event_image']); ?>" class="card-img-top" alt="Event Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($row['description'], 0, 60)); ?>...</p>
                            <p class="card-text"><strong>Date:</strong> <?php echo date('F j, Y', strtotime($row['date'])); ?></p>
                            <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                            <a href="view_event.php?event_id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">View Details</a>
                            <a href="update_event.php?event_id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="event_management.php?delete_event_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">No events found. Please add some events.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2024 Event Management. All rights reserved.</p>
</footer>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>