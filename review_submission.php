<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the role of a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$submission_id = $data['submission_id'];
$feedback = $data['feedback'];

// Update the submission with feedback
$query = "UPDATE Submissions SET feedback = ?, reviewed_at = NOW() WHERE submission_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $feedback, $submission_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update submission']);
}
?>