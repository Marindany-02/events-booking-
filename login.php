<?php
session_start();
require_once 'config/db.php'; // Include your database connection here

$flash_message = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']); // Remove the message after fetchi

if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    echo '<div id="logout-message" class="alert alert-success" role="alert">
            You have successfully logged out.
          </div>';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize user input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if inputs are not empty
    if (!empty($username) && !empty($password)) {
        // Prepare the SQL query to check user credentials and fetch role
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Check if the user exists and the password matches
        if ($user && password_verify($password, $user['password'])) {
            // Set the session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['username']; // Optionally store the username
            $_SESSION['role'] = $user['role']; // Store user role

            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Login Successful !, Welcome, ' . $user['username'] . '!'];

            // Redirect based on the role
            if ($user['role'] == 'admin') {
                header("Location: admins/admin_dashboard.php");
            } elseif ($user['role'] == 'user') {
                header("Location: users/user_dashboard.php");
            } else {
                // Redirect to a default page or show an error if the role is unrecognized
                header("Location: index.php");
            }
            exit();
        } else {
            // If the username or password is incorrect
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Please fill in both fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Local Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 450px;
            margin: 80px auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
            font-size: 1.75rem;
            margin-bottom: 30px;
        }
        .alert {
            margin-bottom: 20px;
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
    </style>
</head>
<body>
<?php if ($flash_message): ?>
        <div class="flash-message <?= htmlspecialchars($flash_message['type']) ?>">
            <?= htmlspecialchars($flash_message['message']) ?>
        </div>
    <?php endif; ?>
  <!-- Fixed Header -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Event Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="user_dashboard.php">Home</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="my_events.php">My Events</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                                <i class="fas fa-user"></i> Profile
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="login-container">
        <h2 class="text-center">Login to Your Account</h2>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <!-- Username Field -->
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <!-- Password Field -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>

            <!-- Register Link -->
            <div class="text-center mt-3">
                <p>Don't have an account? <a href="users/register.php">Register here</a></p>
            </div>
        </form>
    </div>

    <!-- Local Bootstrap JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hide the success message after 4 seconds
        setTimeout(function() {
            var message = document.getElementById('logout-message');
            if (message) {
                message.style.display = 'none';
            }
        }, 4000); // 4000 milliseconds = 4 seconds
    </script>
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
    </script>
    <div class="footer">
        <p>&copy; 2024 Event Management. All rights reserved.</p>
    </div>
</body>
</html>
