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
$class_id = $data['class_id'] ?? null;

if (!$class_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid class ID']);
    exit;
}

try {
    // Log the class_id and teacher_id for debugging
    $teacher_id = $_SESSION['user_id'];
    error_log("Attempting to delete class with ID: $class_id by teacher ID: $teacher_id");

    // Delete the class from the database
    $query = "DELETE FROM Classes WHERE class_id = ? AND teacher_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }

    $stmt->bind_param("ii", $class_id, $teacher_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Class not found or you do not have permission to delete it']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'An error occurred. Please try again later.']);
    error_log("Error deleting class: " . $e->getMessage());
}

$conn->close();
?>