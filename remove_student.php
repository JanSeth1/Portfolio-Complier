<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$student_id = $data['student_id'] ?? null;
$class_id = $data['class_id'] ?? null;

if (!$student_id || !$class_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid student or class ID']);
    exit;
}

// Remove the student from the class
$query = "DELETE FROM Students_Classes WHERE student_id = ? AND class_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $student_id, $class_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to remove student or student not found in class']);
}

$stmt->close();
$conn->close();
?>