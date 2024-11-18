<?php
include 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$work_id = $data['work_id'] ?? '';
$status = $data['status'] ?? '';

if (!$work_id || !$status) {
    echo json_encode(['success' => false, 'error' => 'Work ID and status are required']);
    exit;
}

$query = "UPDATE Student_Work SET status = ? WHERE work_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $status, $work_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update status']);
}
$stmt->close();
$conn->close();
?>