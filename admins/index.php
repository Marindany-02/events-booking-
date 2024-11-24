<?php
session_start();
include('../config/db.php');

// Handle event search
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Fetch events from the database based on the search query
$sql = "SELECT * FROM events WHERE name LIKE '%$search%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <!-- Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .event-card {
            display: flex;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .event-card img {
            width: 200px;
            height: 150px;
            object-fit: cover;
        }
        .event-card .card-body {
            padding: 15px;
            flex: 1;
        }
        .event-card .card-body h5 {
            margin-bottom: 10px;
        }
        .event-card .card-body p {
            margin-bottom: 10px;
        }
        .event-card .card-body a {
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Event Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home Page</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">Back to Dashboard</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Event Search -->
    <section class="container mt-5">
        <form action="index.php" method="GET" class="d-flex justify-content-center">
            <input type="text" name="search" value="<?php echo $search; ?>" class="form-control w-50" placeholder="Search events">
            <button type="submit" class="btn btn-primary ms-2">Search</button>
        </form>
    </section>

    <!-- Available Events -->
    <section class="container mt-5">
        <h2 class="text-center mb-4">Available Events</h2>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='event-card'>";
                
                // Display event image if available
                if (!empty($row['event_image'])) {
                    echo "<img src='../assets/images/" . htmlspecialchars($row['event_image']) . "' alt='" . htmlspecialchars($row['name']) . "'>";
                } else {
                    echo "<img src='../assets/images/default_event_image.jpg' alt='Event Image'>";
                }

                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($row['name']) . "</h5>";
                echo "<p class='card-text'><strong>Date:</strong> " . date('F j, Y', strtotime($row['date'])) . "<br><strong>Location:</strong> " . htmlspecialchars($row['location']) . "</p>";
                echo "<p class='card-text'>" . substr($row['description'], 0, 150) . "...</p>";
                echo "<a href='../login.php' class='btn btn-primary'>Register</a>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p class='text-center'>No events found.</p>";
        }
        ?>
    </section>

    <!-- Bootstrap JS (Optional, for interactivity like collapse) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybR6D1v3gH2uOdCO6lR8kPFZ76Gsp65jJxuV1RP0Dtr+y5M9/2" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0v8FqE5fScp5g7g5k3e5eZ2yk7v6jl1W+v/JuZ61w5y+I9pi" crossorigin="anonymous"></script>

</body>
</html>
