<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$class_id = $data['class_id'];
$student_id = $data['student_id'];
$teacher_id = $_SESSION['user_id'];

// Verify that the teacher is managing the class
$verifyQuery = "SELECT * FROM Classes WHERE class_id = ? AND teacher_id = ?";
$verifyStmt = $conn->prepare($verifyQuery);
$verifyStmt->bind_param("ii", $class_id, $teacher_id);
$verifyStmt->execute();
$verifyResult = $verifyStmt->get_result();

if ($verifyResult->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'You do not have permission to manage this class.']);
    exit;
}

// Add student to the class
$query = "INSERT INTO Students_Classes (student_id, class_id) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $student_id, $class_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to add student.']);
}

$stmt->close();
$conn->close();
?>