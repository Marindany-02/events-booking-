<?php
// Database connection
include('../config/db.php');
include('admin_sidebar.php');

// Initialize search query
$search_query = '';

// Handle search functionality
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Fetch bookings and payment details with optional search query
$sql = "SELECT b.*, e.name AS event_name, u.username, u.email
        FROM bookings b 
        LEFT JOIN events e ON b.event_id = e.id
        LEFT JOIN users u ON b.user_id = u.user_id
        WHERE b.approval_status LIKE ? 
        OR e.name LIKE ? 
        OR u.username LIKE ? 
        OR u.email LIKE ?
        ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($sql);

// Check if the statement was prepared correctly
if ($stmt === false) {
    echo "Error in SQL query preparation: " . $conn->error;
    exit;
}

// Bind the search parameters to prevent SQL injection
$search_term = "%" . $search_query . "%";
$stmt->bind_param("ssss", $search_term, $search_term, $search_term, $search_term);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Error handling in case of query failure
if ($result === false) {
    echo "Error in SQL execution: " . $conn->error;
    exit;
}

// Handle status change (approve/reject/pending)
if (isset($_GET['action'], $_GET['booking_id'])) {
    $action = $_GET['action'];
    $booking_id = intval($_GET['booking_id']); // Ensure booking_id is an integer

    if ($action === 'approve') {
        $update_sql = "UPDATE bookings SET approval_status = 'approved' WHERE id = ?";
    } elseif ($action === 'reject') {
        $update_sql = "UPDATE bookings SET approval_status = 'rejected', num_seats = 0 WHERE id = ?";
    } elseif ($action === 'pending') {
        $update_sql = "UPDATE bookings SET approval_status = 'pending' WHERE id = ?";
    } else {
        echo "Invalid action.";
        exit;
    }

    $update_stmt = $conn->prepare($update_sql);
    if ($update_stmt === false) {
        echo "Error in SQL query preparation: " . $conn->error;
        exit;
    }

    $update_stmt->bind_param("i", $booking_id);
    if ($update_stmt->execute()) {
        header("Location: check_payment.php"); // Redirect to refresh the page
        exit;
    } else {
        echo "Error updating booking status: " . $conn->error;
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Payment</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Sidebar styling */
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

<!-- Sidebar -->
<div class="sidebar">
    <?php include('admin_sidebar.php'); ?>
</div>

<!-- Main Content -->
<div class="content">
    <h2 class="text-center mb-4">Check Payments</h2>

    <!-- Search Bar -->
    <form class="mb-4" action="check_payment.php" method="get">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by booking, event name, or user" value="<?php echo htmlspecialchars($search_query); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <!-- Bookings Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>User</th>
                <th>Event</th>
                <th>Seats</th>
                <th>Amount Paid</th>
                <th>Mpesa Code</th>
                <th>Payment Status</th>
                <th>Booking Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['num_seats']); ?></td>
                        <td><?php echo number_format($row['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['mpesa_code']); ?></td>
                        <td><?php echo htmlspecialchars($row['approval_status']);?>
                        </td>
                        <td><?php echo date('F j, Y', strtotime($row['booking_date'])); ?></td>
                        <td>
                            <?php if ($row['approval_status'] == 'pending'): ?>
                                <a href="check_payment.php?action=approve&booking_id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                                <a href="check_payment.php?action=reject&booking_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                                <a href="check_payment.php?action=pending&booking_id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Set Pending</a>
                            <?php elseif ($row['approval_status'] == 'approved'): ?>
                                <a href="check_payment.php?action=pending&booking_id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Set Pending</a>
                                <a href="check_payment.php?action=reject&booking_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                            <?php elseif ($row['approval_status'] == 'rejected'): ?>
                                <a href="check_payment.php?action=pending&booking_id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Set Pending</a>
                                <a href="check_payment.php?action=approve&booking_id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No bookings found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2024 Event Management. All rights reserved.</p>
</footer>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
