<?php
// Database connection
include('../config/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id']; // Replace with your session user ID
    $username = $_POST['username'];
    $email = $_POST['email'];

    $sql = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssi", $username, $email, $user_id);
        if ($stmt->execute()) {
            // Redirect with success message
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Profile updated successfully!'];
        } else {
            // Redirect with error message
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Failed to update profile. Please try again.'];
        }
        $stmt->close();
    } else {
        $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Database error: ' . $conn->error];
    }
    $conn->close();

    header("Location: user_dashboard.php");
    exit();
}
?>
