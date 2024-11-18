<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch classes the student is enrolled in
$query = "SELECT c.class_id, c.class_name FROM Classes c
          JOIN Students_Classes sc ON c.class_id = sc.class_id
          WHERE sc.student_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}



$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'classes' => $classes]);
?>