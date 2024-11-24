<?php
session_start();
require_once '../config/db.php'; // Include your database connection
include('admin_sidebar.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if user is not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Prepare SQL query to fetch user data
$sql = "SELECT * FROM users WHERE user_id = ?";

// Check if the connection is successful
if ($conn === false) {
    die('Database connection failed: ' . mysqli_connect_error());
}

$stmt = $conn->prepare($sql);

// Check if prepare was successful
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}

// Bind the user ID parameter to the SQL query
$stmt->bind_param("i", $user_id);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Fetch the user data
$user = $result->fetch_assoc();

// Check if user data is found
if (!$user) {
    echo "Error: User not found.";
    exit();
}

// Handle the email update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_email'])) {
    // Collect and sanitize the new email input
    $new_email = trim($_POST['email']);

    // Check if email is valid
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Prepare SQL query to update the email
        $update_sql = "UPDATE users SET email = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);

        if ($update_stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }

        // Bind parameters and execute the query
        $update_stmt->bind_param("si", $new_email, $user_id);
        $update_stmt->execute();

        // Check if the update was successful
        if ($update_stmt->affected_rows > 0) {
            $success_message = "Email updated successfully!";
            $user['email'] = $new_email; // Update the local user data
        } else {
            $error_message = "Error updating email. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .profile-container h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }
        .profile-container p {
            font-size: 1.1rem;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2 class="text-center">Profile Details</h2>

    <!-- Display success or error messages -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>

        <!-- Editable Email Field -->
        <div class="mb-3">
            <label for="email" class="form-label"><strong>Email:</strong></label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <!-- Role Display -->
        <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>

        <!-- Update Button -->
        <button type="submit" name="update_email" class="btn btn-primary w-100">Update Email</button>
    </form>

    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
