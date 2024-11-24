<?php
// Include the sidebar
include('admin_sidebar.php');
session_start(); // Ensure the session is started at the top of the file
$flash_message = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']); // Remove the message after fetching

// Database connection (make sure it's correctly included or initialized)
include('../config/db.php');

// Fetch the number of users from the database
$sql_users = "SELECT COUNT(*) as user_count FROM users WHERE role='user'";
$result_users = $conn->query($sql_users);
$user_count = 0;

if ($result_users && $row = $result_users->fetch_assoc()) {
    $user_count = $row['user_count'];
}

// Fetch total events, upcoming events, and available spot
// Fetch total events, upcoming events, and available spots
$sql_events = "SELECT 
                    COUNT(*) AS total_events, 
                    COUNT(CASE WHEN date >= NOW() THEN 1 END) AS upcoming_events,
                    SUM(capacity) - COALESCE(SUM(b.num_seats), 0) AS available_spots
                FROM events e
                LEFT JOIN bookings b ON e.id = b.event_id";
$result_events = $conn->query($sql_events);
if ($result_events && $row = $result_events->fetch_assoc()) {
    $total_events = $row['total_events'] ?? 0;
    $upcoming_events = $row['upcoming_events'] ?? 0;
    $available_spots = $row['available_spots'] ?? 0;
} else {
    // Handle query failure
    echo "Error: " . $conn->error;
}
// Fetch booking stats
$sql_bookings = "SELECT 
                    COUNT(*) as total_bookings,
                    COUNT(DISTINCT event_id) as full_capacity_events,
                    COUNT(CASE WHEN approval_status = 'pending' THEN 1 END) as pending_bookings
                 FROM bookings";
$result_bookings = $conn->query($sql_bookings);
if ($result_bookings && $row = $result_bookings->fetch_assoc()) {
    $total_bookings = $row['total_bookings'] ?? 0;
    $full_capacity_events = $row['full_capacity_events'] ?? 0;
    $pending_bookings = $row['pending_bookings'] ?? 0;
} else {
    // Handle query failure
    echo "Error: " . $conn->error;
}

$sql_recent_bookings = "SELECT * FROM bookings ORDER BY booking_date DESC LIMIT 5";
$result_recent_bookings = $conn->query($sql_recent_bookings);

$sql_recent_events = "SELECT * FROM events ORDER BY date DESC LIMIT 2";
$result_recent_events = $conn->query($sql_recent_events);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons for sidebar icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Basic styling for the sidebar */
        .sidebar {
            width: 250px; /* Sidebar width */
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            padding-top: 20px;
            z-index: 1000; /* Ensure the sidebar is on top */
            overflow-y: auto;
        }

        .nav-link {
            color: white;
            font-size: 16px;
        }

        .nav-link:hover {
            color: blue;
        }

        /* Adjust the content to fit alongside the sidebar */
        .content {
            margin-left: 250px;
            padding: 20px;
        }

        /* Footer positioning */
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 15px 0;
        }

        /* Circle for user count */
        .user-count-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #007bff;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 36px;
            font-weight: bold;
            margin: 20px auto;
        }

        .flash-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            padding: 15px 20px;
            border-radius: 5px;
            font-size: 16px;
            opacity: 1;
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }
        .flash-message.hidden {
            opacity: 0;
            visibility: hidden;
        }

        /* Widget styles */
        .widget {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .widget h4 {
            font-size: 18px;
            margin-bottom: 15px;
        }

        .widget p {
            font-size: 24px;
            font-weight: bold;
        }

        /* Media queries for responsive layout */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                height: auto;
                width: 100%;
                box-shadow: none;
            }

            .content {
                margin-left: 0;
            }

            footer {
                position: static;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>

    <?php if ($flash_message): ?>
        <div class="flash-message alert alert-<?= htmlspecialchars($flash_message['type']) ?> text-center">
            <?= htmlspecialchars($flash_message['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Main content area -->
    <div class="content">
        <h1>Welcome to the Admin Dashboard</h1>
        <p>Here you can manage all aspects of your platform.</p>

        <!-- Row for the Dashboard Widgets -->
        <div class="row">
            <!-- User Count Widget -->
            <div class="col-md-3">
                <div class="widget">
                    <h4>Total System Users</h4>
                    <div class="user-count-circle">
                        <p><?php echo $user_count; ?></p>
                    </div>
                </div>
            </div>

            <!-- Event Stats Widget -->
            <div class="col-md-3">
    <div class="widget">
        <h4>Event Statistics</h4>
        <p>Total Events: <?php echo $total_events; ?></p>
        <p>Upcoming Events: <?php echo $upcoming_events; ?></p>
        <p>Available Spots: <?php echo $available_spots; ?></p>
    </div>
</div>

<!-- Booking Stats Widget -->
<div class="col-md-3">
    <div class="widget">
        <h4>Booking Statistics</h4>
        <p>Total Bookings: <?php echo $total_bookings; ?></p>
        <p>Full Capacity Events: <?php echo $full_capacity_events; ?></p>
        <p>Pending Bookings: <?php echo $pending_bookings; ?></p>
    </div>
</div>

            <!-- Recent Activities Widget -->
            <div class="col-md-3">
                <div class="widget">
                    <h4>Recent Bookings</h4>
                    <ul>
                        <?php while ($booking = $result_recent_bookings->fetch_assoc()): ?>
                            <li>Booking ID: <?php echo $booking['id']; ?> on <?php echo $booking['booking_date']; ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Recent Events -->
        <h2 class="mt-4">Recent Events</h2>
        <div class="row">
            <?php while ($event = $result_recent_events->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($event['description'], 0, 100)); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Event Management System. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fade out the flash message after 4 seconds
        document.addEventListener('DOMContentLoaded', function () {
            const flashMessage = document.querySelector('.flash-message');
            if (flashMessage) {
                setTimeout(() => {
                    flashMessage.classList.add('hidden');
                }, 4000); // 4 seconds
            }
        });
    </script>
</body>
</html>
