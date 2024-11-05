<?php
// fetch_registered_students.php

header('Content-Type: application/json');

// Include the database connection
include 'db.php';

// Query to fetch registered students from the Users table
$query = "SELECT user_id, username FROM Users WHERE role = 'student'";
$result = $conn->query($query);

if ($result) {
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    echo json_encode(['success' => true, 'students' => $students]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to fetch students: ' . $conn->error]);
}

$conn->close(); // Close the connection
?>