<?php
session_start();
include('../config/db.php'); // Include the database connection
include('header_footer.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch all bookings for the user
$sql = "SELECT e.name, e.booking_dateline, b.num_seats, b.mpesa_code, b.amount, b.approval_status
        FROM bookings b
        JOIN events e ON b.event_id = e.id
        WHERE b.user_id = ?
        ORDER BY e.booking_dateline DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Store bookings in an array
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $no_bookings = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
        }

        .container {
            margin-top: 0px;
        }

        .table-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .alert {
            margin-bottom: 20px;
        }

    </style>
</head>
<body>

    <div class="container">
        <div class="table-container">
            <h2 class="text-center">My Booked Events</h2>

            <!-- Display message if no bookings found -->
            <?php if (isset($no_bookings)): ?>
                <div class="alert alert-info text-center">
                    You have not booked any events yet.
                </div>
            <?php else: ?>
                <!-- Display the bookings in a table -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Seats Booked</th>
                            <th>M-Pesa Code</th>
                            <th>Amount Paid</th>
                            <th>Booking Deadline</th>
                            <th>Approval Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['name']); ?></td>
                                <td><?php echo $booking['num_seats']; ?> seat(s)</td>
                                <td><?php echo htmlspecialchars($booking['mpesa_code']); ?></td>
                                <td>KES <?php echo number_format($booking['amount'], 2); ?></td>
                                <td><?php echo date('F j, Y', strtotime($booking['booking_dateline'])); ?></td>
                                <td>
                                    <?php 
                                        if ($booking['approval_status'] == 'pending') {
                                            echo '<span class="text-warning">Pending</span>';
                                        } elseif ($booking['approval_status'] == 'approved') {
                                            echo '<span class="text-success">Approved</span>';
                                        } elseif ($booking['approval_status'] == 'rejected') {
                                            echo '<span class="text-danger">Rejected</span>';
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <a href="user_dashboard.php" class="btn btn-secondary mt-3 w-100">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
