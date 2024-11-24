<?php
// Database connection
include('../config/db.php');
include('admin_sidebar.php');

// Fetch all users with role 'admin' from the users table
$admins_result = $conn->query("SELECT * FROM users WHERE role = 'admin'");

// Add a new admin (form submission handling)
if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = 'admin';  // Set the role as 'admin' for this page
    
    // Check if email already exists
    $email_check_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = $conn->query($email_check_query);
    if ($result->num_rows > 0) {
        $error_message = "Email is already in use.";
    } else {
        // Hash the password before storing it in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL query
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing query: " . $conn->error);
        }

        // Bind parameters (s = string)
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

        if (!$stmt->execute()) {
            die("Error executing query: " . $stmt->error);
        } else {
            $success_message = "Admin created successfully.";
            // Refresh the admins list after adding a new one
            $admins_result = $conn->query("SELECT * FROM users WHERE role = 'admin'");
        }
    }
}
//updating admin
if (isset($_POST['update_admin'])) {
    $user_id = $_POST['user_id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Hash password only if it's changed
    $hashed_password = empty($password) ? '' : password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL update query
    if (empty($hashed_password)) {
        // If password is empty, do not update it
        $update_sql = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $username, $email, $user_id);
    } else {
        // If password is provided, update it
        $update_sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $username, $email, $hashed_password, $user_id);
    }

    if ($update_stmt->execute()) {
        $success_message = "Admin updated successfully.";
        // Refresh the admins list after updating
        $admins_result = $conn->query("SELECT * FROM users WHERE role = 'admin'");
    } else {
        $error_message = "Failed to update admin.";
    }
}
// Delete admin functionality
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']); // Ensure that the ID is an integer
    $delete_sql = "DELETE FROM users WHERE user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id); // Bind as an integer

    if ($delete_stmt->execute()) {
        $success_message = "Admin deleted successfully.";
        // Refresh the admins list after deletion
        $admins_result = $conn->query("SELECT * FROM users WHERE role = 'admin'");
    } else {
        $error_message = "Failed to delete admin.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        /* Add margin-left to content to ensure it doesn't overlap the sidebar */
        .content {
            margin-left: 250px; /* Adjust this value according to your sidebar width */
            padding: 20px;
        }
        /* Adjust layout for smaller screens */
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 10px;
            }
        }

        /* Optional: Add fade-out effect */
        .fade-out {
            transition: opacity 1s ease-out;
        }
    </style>
</head>
<body>
<div class="content">
    <h2>Admin Settings</h2>

    <!-- Display success or error message -->
    <?php if (isset($success_message)): ?>
        <div id="success-message" class="alert alert-success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div id="error-message" class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Displaying list of admins -->
    <table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Date Added</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($admin = $admins_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $admin['user_id']; ?></td>
                <td><?php echo htmlspecialchars($admin['username']); ?></td>
                <td><?php echo htmlspecialchars($admin['email']); ?></td>
                <td><?php echo htmlspecialchars($admin['created_at']); ?></td>
                <td>
                    <!-- Update button triggers modal -->
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $admin['user_id']; ?>">
                        Update
                    </button>
                    <!-- Delete button -->
                    <a href="?delete_id=<?php echo $admin['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this admin?')">Delete</a>
                </td>
            </tr>

            <!-- Update Modal for each admin -->
            <div class="modal fade" id="updateModal<?php echo $admin['user_id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel<?php echo $admin['user_id']; ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateModalLabel<?php echo $admin['user_id']; ?>">Update Admin</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="admin_settings.php" method="POST">
                                <input type="hidden" name="user_id" value="<?php echo $admin['user_id']; ?>">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Name</label>
                                    <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password (Leave blank to keep current password)</label>
                                    <input type="password" name="password" id="password" class="form-control">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success" name="update_admin">Update Admin</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </tbody>
</table>

    <!-- Button to add a new admin -->
    <h3>Add New Admin</h3>
    <form action="admin_settings.php" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Name</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success" name="submit">Add Admin</button>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="../assets/js/bootstrap.bundle.min.js"></script>

<script>
    // JavaScript to hide the success or error messages after 5 seconds
    window.onload = function() {
        var successMessage = document.getElementById('success-message');
        var errorMessage = document.getElementById('error-message');
        
        if (successMessage) {
            setTimeout(function() {
                successMessage.classList.add('fade-out');
                setTimeout(function() { successMessage.style.display = 'none'; }, 1000);
            }, 5000); // Hide after 5 seconds
        }

        if (errorMessage) {
            setTimeout(function() {
                errorMessage.classList.add('fade-out');
                setTimeout(function() { errorMessage.style.display = 'none'; }, 1000);
            }, 5000); // Hide after 5 seconds
        }
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
