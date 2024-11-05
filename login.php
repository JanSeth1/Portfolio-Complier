<?php
session_start();
include 'db.php'; 

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['login'])) {
    $entered_username = $_POST['username'];
    $entered_password = $_POST['password'];

    // Update the SQL query to also select the role
    $sql = "SELECT user_id, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $entered_username);
    $stmt->execute();
    $stmt->bind_result($user_id, $stored_password, $role);
    $stmt->fetch();

    // Verify the password
    if ($entered_password === $stored_password) {
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $entered_username;
        $_SESSION['role'] = $role; // Store the role in the session

        $stmt->close();
        $conn->close();

        // Redirect based on the user's role
        if ($role === 'teacher') {
            header("Location: teacher-side.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        $stmt->close();
        $conn->close();

        header("Location: index.php?error=Invalid credentials");
        exit;
    }
}