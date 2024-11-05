<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit;
}

$class_id = $_GET['class_id'];
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

// Fetch students for the class
$query = "SELECT u.user_id, u.username FROM Users u
          JOIN Students_Classes sc ON u.user_id = sc.student_id
          WHERE sc.class_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode(['success' => true, 'students' => $students]);

$stmt->close();
$conn->close();
?>