<?php
include 'db.php';
header('Content-Type: application/json');

$work_id = $_GET['work_id'] ?? '';

if (!$work_id) {
    echo json_encode(['success' => false, 'error' => 'Work ID is required']);
    exit;
}

$query = "SELECT * FROM Student_Work WHERE work_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $work_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'submission' => $row]);
} else {
    echo json_encode(['success' => false, 'error' => 'No submission found']);
}
$stmt->close();
$conn->close();
?>