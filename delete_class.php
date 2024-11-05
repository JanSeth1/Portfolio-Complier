<?php
session_start();
include 'db.php'; 

header('Content-Type: application/json');

// Check if the user is logged in and has the role of a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$class_id = $data['class_id'] ?? null;

if (!$class_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid class ID.']);
    exit;
}

$query = "DELETE FROM Classes WHERE class_id = ? AND teacher_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $class_id, $user_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error executing query.']);
}

$stmt->close();
$conn->close();
?>