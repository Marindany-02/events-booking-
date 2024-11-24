<?php
session_start();
include('../config/db.php'); // Include the database connection
include('header_footer.php'); 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if event_id is passed
if (!isset($_GET['event_id'])) {
    echo "<div class='alert alert-danger text-center mt-4'>No event selected. Please go back and select an event.</div>";
    exit();
}

// Get event ID from URL
$event_id = intval($_GET['event_id']);
$user_id = $_SESSION['user_id'];

// Fetch the event details including capacity and cost_per_spot
$sql = "SELECT name, capacity, (capacity - COALESCE(SUM(b.num_seats), 0)) AS available_capacity, booking_dateline, cost
        FROM events e
        LEFT JOIN bookings b ON e.id = b.event_id
        WHERE e.id = ?
        GROUP BY e.id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    echo "<div class='alert alert-danger text-center mt-4'>Event not found. Please go back and try again.</div>";
    exit();
}

// Check if the event has available spots and is within the booking deadline
$current_date = date('Y-m-d');
if ($event['available_capacity'] <= 0) {
    echo "<div class='alert alert-warning text-center mt-4'>This event is fully booked. Please check for other events.</div>";
    exit();
} elseif ($current_date > $event['booking_dateline']) {
    echo "<div class='alert alert-warning text-center mt-4'>The booking deadline for this event has passed. Please check other available events.</div>";
    exit();
}

// Check if the user has already booked for this event
$check_booking_sql = "SELECT * FROM bookings WHERE event_id = ? AND user_id = ?";
$check_booking_stmt = $conn->prepare($check_booking_sql);
$check_booking_stmt->bind_param("ii", $event_id, $user_id);
$check_booking_stmt->execute();
$existing_booking = $check_booking_stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['num_seats'], $_POST['mpesa_code'], $_POST['amount'])) {
        $num_seats = intval($_POST['num_seats']);
        $mpesa_code = $_POST['mpesa_code'];
        $amount = floatval($_POST['amount']);
        $total_cost = $num_seats * $event['cost']; // Calculate total cost for the booking

        // Validate input fields
        if ($num_seats <= 0) {
            $error_message = "Invalid number of seats. Please enter a positive number.";
        } elseif ($num_seats > $event['available_capacity']) {
            $error_message = "Not enough available seats for your request. Please select up to " . $event['available_capacity'] . " seat(s).";
        } elseif (empty($mpesa_code)) {
            $error_message = "Please enter a valid M-Pesa code.";
        } elseif ($amount < $total_cost) {
            $error_message = "The amount entered is insufficient. Please pay at least KES " . number_format($total_cost, 2) . " for $num_seats seat(s).";
        } else {
            // Proceed with booking
            $booking_sql = "INSERT INTO bookings (event_id, user_id, num_seats, mpesa_code, amount) VALUES (?, ?, ?, ?, ?)";
            $booking_stmt = $conn->prepare($booking_sql);
            $booking_stmt->bind_param("iiisd", $event_id, $user_id, $num_seats, $mpesa_code, $amount);

            if ($booking_stmt->execute()) {
                $success_message = "Booking successful! You have reserved $num_seats seat(s). Your payment of KES $amount is confirmed.";
            } else {
                $error_message = "An error occurred while processing your booking. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Event</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
        }

        .booking-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <h2 class="text-center">Book Your Spot for <?php echo htmlspecialchars($event['name']); ?></h2>
        <p class="text-center"><strong>Available Seats:</strong> <?php echo $event['capacity']; ?></p>
        <p class="text-center"><strong>Cost Per Spot:</strong> KES <?php echo number_format($event['cost'], 2); ?></p>
        <p class="text-center"><strong>Booking Deadline:</strong> <?php echo date('F j, Y', strtotime($event['booking_dateline'])); ?></p>

        <!-- Display error or success message -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php elseif (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Display the booking form if the user hasn't already booked -->
        <?php if (!$existing_booking): ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="num_seats">Number of Seats:</label>
                    <input type="number" class="form-control" id="num_seats" name="num_seats" min="1" max="<?php echo $event['capacity']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="mpesa_code">Enter M-Pesa Code Paybill 809109:</label>
                    <input type="text" class="form-control" id="mpesa_code" name="mpesa_code" required>
                </div>
                <div class="form-group">
                    <label for="amount">Enter Amount (Minimum: KES <?php echo number_format($event['cost'], 2); ?> per seat):</label>
                    <input type="number" class="form-control" id="amount" name="amount" min="1" required>
                </div>
                <button type="submit" class="btn btn-success mt-3 w-100">Book Now</button>
                <a href="user_dashboard.php" class="btn btn-secondary mt-3 w-100">Back to Dashboard</a>
            </form>
        <?php else: ?>
            <div class="alert alert-info text-center mt-4">
                <strong>You have already booked this event!</strong>
            </div>
            <a href="user_dashboard.php" class="btn btn-secondary mt-3 w-100">Back to Dashboard</a>
        <?php endif; ?>
    </div>
</body>
</html>
