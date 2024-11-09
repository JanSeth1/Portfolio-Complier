<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$class_name = $_POST['class_name'] ?? null;
$teacher_id = $_SESSION['user_id'];

if (!$class_name) {
    echo json_encode(['success' => false, 'error' => 'Class name is required']);
    exit;
}

// Insert new class into the database
$query = "INSERT INTO Classes (class_name, teacher_id) VALUES (?, ?)";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("si", $class_name, $teacher_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to create class']);
}

$stmt->close();
$conn->close();
?>