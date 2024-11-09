<?php
session_start();
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    if ($password !== $confirm_password) {
        header("Location: index.php?error=Passwords do not match");
        exit;
    }

    $sql = "INSERT INTO Users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        header("Location: index.php?success=Account created successfully");
    } else {
        header("Location: index.php?error=Registration failed");
    }

    $stmt->close();
    $conn->close();
}
?>