<?php
session_start();
// Database connection (make sure it's correctly included or initialized)
include('../config/db.php');
include('header_footer.php');

// Query to fetch events with total capacity, booked spots, and registration deadline
$sql = "SELECT 
            e.id, e.name, e.description, e.date, e.location, e.cost, e.capacity, booking_dateline, e.event_image,
            (e.capacity - COALESCE(SUM(b.num_seats), 0)) AS available_capacity
        FROM 
            events e
        LEFT JOIN 
            bookings b ON e.id = b.event_id
        GROUP BY 
            e.id";
$result = $conn->query($sql);

// Add error handling to check for query failure
if ($result === false) {
    echo "Error in SQL query: " . $conn->error;
    exit; // Stop execution if there's an error
}

$flash_message = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']); // Remove the message after fetching

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- External Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- For human icon -->
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 60px; /* Space for the fixed header */
            padding-bottom: 60px; /* Space for the fixed footer */
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 15px;
            background-color: #343a40;
            color: white;
            text-align: center;
        }

        .dashboard-container {
            margin: 20px auto;
            max-width: 98%;
            padding-top: 20px;
        }

        .event-card {
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
             height: 150px;  /* Reduce the height */
             object-fit: cover;  /* Ensures the image covers the area without distortion */
             width: 100%;  /* Ensure the image takes up the full width of the card */
        }

        .card-body {
            padding: 1opx;
        }

        .btn-register {
            margin-top: 10px;
        }

        .content-wrapper {
            max-height: calc(100vh - 120px);
            overflow-y: auto;
        }

        .profile-icon {
            font-size: 20px;
            margin-right: 5px;
        }

        .navbar-nav .nav-item {
            margin-left: 15px;
        }

        .flash-message {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
            color: #fff;
            opacity: 1;
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }

        .flash-message.success { background-color: #28a745; }
        .flash-message.error { background-color: #dc3545; }

        .fade-out { opacity: 0 !important; }

        .search-bar {
            margin-bottom: 20px;
        }

    </style>
</head>
<body>
<?php if ($flash_message): ?>
        <div class="flash-message <?= htmlspecialchars($flash_message['type']) ?>">
            <?= htmlspecialchars($flash_message['message']) ?>
        </div>
    <?php endif; ?>

<!-- Main Content Section -->
<div class="container dashboard-container content-wrapper">
    <h2 class="text-center mb-4">Available Events</h2>

    <!-- Search Bar -->
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <input type="text" id="searchInput" class="form-control search-bar" placeholder="Search Events by Name or Description">
        </div>
    </div>

    <!-- Error Message -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Event List -->
    <div class="row" id="eventList">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-3 event-item">
                    <div class="card event-card">
                        <img src="../assets/images/<?php echo htmlspecialchars($row['event_image']); ?>" class="card-img-top" alt="Event Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <li class="card-text"><?php echo htmlspecialchars(substr($row['description'], 0, 100)); ?>...</></li>
                            <li class="card-text"><strong>Date:</strong> <?php echo date('F j, Y', strtotime($row['date'])); ?></li>
                            <li class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></li>
                            <li class="card-text"><strong>Capacity:</strong> <?php echo $row['capacity']; ?></li>
                            <li class="card-text" ><strong>Cost Per Spot (ksh):</strong> ksh <?php echo $row['cost']; ?></l> </li>
                            <li class="card-text"><strong>Available Spots:</strong> <?php echo max($row['available_capacity'], 0); ?></li>
                            <li class="card-text"><strong>Booking Deadline:</strong> <?php echo date('F j, Y', strtotime($row['booking_dateline'])); ?></li>
                            </ul>

                            <!-- Register Button -->
                            <a href="book_event.php?event_id=<?php echo $row['id']; ?>" class="btn btn-success btn-register">Book</a>
                            <a href="view_event.php?event_id=<?php echo $row['id']; ?>" class="btn btn-primary btn-register">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">No events available at the moment.</div>
        <?php endif; ?>
    </div>
</div>

<!-- External Bootstrap JS -->
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script>
    // Fade out the flash message after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const flashMessage = document.querySelector('.flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.classList.add('fade-out');
            }, 5000); // 5 seconds
            setTimeout(() => {
                flashMessage.remove();
            }, 5500); // Wait for fade-out to complete before removal
        }
    });

    // Search function for filtering events by name or description
    document.getElementById('searchInput').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        let eventItems = document.querySelectorAll('.event-item');

        eventItems.forEach(function(item) {
            let title = item.querySelector('.card-title').textContent.toLowerCase();
            let description = item.querySelector('.card-text').textContent.toLowerCase();

            if (title.includes(filter) || description.includes(filter)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>
