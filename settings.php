<?php
// Include the database connection file
include('db.php');

// Ensure the user is logged in and has a valid session
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect if the user is not logged in
    header("Location: login.php");
    exit;
}

// Get the current user data
$userId = $_SESSION['user_id'];
$sql = "SELECT username, password FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId); // Bind the user ID parameter
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST["username"];
    $newPassword = $_POST["password"];
    
    // If a new password is provided, use it as is (without hashing)
    if (!empty($newPassword)) {
        $sql = "UPDATE users SET username = ?, password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $newUsername, $newPassword, $userId);
        $stmt->execute();
    } else {
        // If no password change, only update the username
        $sql = "UPDATE users SET username = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newUsername, $userId);
        $stmt->execute();
    }

    // Success message (can redirect to a confirmation page)
    echo "<script>alert('Settings updated successfully');</script>";
}
?>