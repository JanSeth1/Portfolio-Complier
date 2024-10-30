<?php
session_start();
include 'db.php'; // Include the database connection

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['login'])) {
    $entered_username = $_POST['username'];
    $entered_password = $_POST['password'];

    // Prepare and execute the SQL statement
    $sql = "SELECT user_id, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $entered_username);
    $stmt->execute();
    $stmt->bind_result($user_id, $stored_password);
    $stmt->fetch();

    // Compare the plain text password
    if ($entered_password === $stored_password) {
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $entered_username;

        // Close the statement and connection
        $stmt->close();
        $conn->close();

        // Redirect to the dashboard page
        header("Location: dashboard.php");
        exit;
    } else {
        // Close the statement and connection
        $stmt->close();
        $conn->close();

        // If login fails, redirect back to the homepage with an error message
        header("Location: index.php?error=Invalid credentials");
        exit;
    }
}
?>