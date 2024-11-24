<?php
// Database connection (make sure it's correctly included or initialized)
include('../config/db.php');
include('admin_sidebar.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $capacity = $_POST['capacity'];  // Event capacity
    $booking_dateline = $_POST['booking_dateline'];  // Booking deadline
    $cost = $_POST['cost'];  // Cost per spot (new field)

    // Handle file upload
    $targetDir = "../assets/images/";
    $imageName = basename($_FILES["event_image"]["name"]);
    $targetFilePath = $targetDir . $imageName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Check if image file is a valid image type
    $check = getimagesize($_FILES["event_image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
        $error = "File is not an image.";
    }

    // Check if the file already exists
    if (file_exists($targetFilePath)) {
        $error = "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Allow only specific file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $error = isset($error) ? $error : "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["event_image"]["tmp_name"], $targetFilePath)) {
            // Insert event data into the database including the new fields
            $sql = "INSERT INTO events (name, date, location, description, event_image, capacity, booking_dateline, cost) 
                    VALUES ('$name', '$date', '$location', '$description', '$imageName', '$capacity', '$booking_dateline', '$cost')";
            if ($conn->query($sql) === TRUE) {
                $success = "Event added successfully!";
                header('location: event_management.php');
            } else {
                $error = "Error: " . $conn->error;
            }
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <!-- External Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 800px;
            margin-top: 50px;
        }

        .form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-container .form-label {
            font-weight: bold;
        }

        .form-container .form-control {
            border-radius: 5px;
        }

        .form-container .btn {
            width: 100%;
            padding: 12px;
            font-size: 1.1rem;
            border-radius: 5px;
        }

        .form-container .alert {
            margin-bottom: 20px;
        }

        .form-container a {
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Add Event</h2>

        <!-- Display success or error messages -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="add_event.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Event Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" required>
            </div>
            <div class="mb-3">
                <label for="capacity" class="form-label">Event Capacity</label>
                <input type="number" class="form-control" id="capacity" name="capacity" required>
            </div>
            <div class="mb-3">
                <label for="cost" class="form-label">Cost Per Spot (Ksh)</label>
                <input type="number" class="form-control" id="cost" name="cost" required>
            </div>
            <div class="mb-3">
                <label for="booking_dateline" class="form-label">Booking Dateline</label>
                <input type="date" class="form-control" id="booking_dateline" name="booking_dateline" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="event_image" class="form-label">Event Image</label>
                <input type="file" class="form-control" id="event_image" name="event_image" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Event</button>
        </form>

        <a href="event_management.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</div>

<!-- External Bootstrap JS -->
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
