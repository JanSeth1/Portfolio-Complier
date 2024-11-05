<?php
// add_students_to_class.php

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

$class_id = $data['class_id'];
$student_ids = $data['student_ids'];

// Include the database connection
include 'db.php';

// Debugging: Output the class_id
error_log("Class ID received: " . $class_id);

// Check if the class_id exists
$class_check_query = "SELECT class_id FROM Classes WHERE class_id = ?";
$class_stmt = $conn->prepare($class_check_query);
$class_stmt->bind_param("i", $class_id);
$class_stmt->execute();
$class_stmt->store_result();

if ($class_stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => "Class ID $class_id does not exist"]);
    $class_stmt->close();
    $conn->close();
    exit;
}

$class_stmt->close();

// Proceed to add students to the class
foreach ($student_ids as $student_id) {
    $stmt = $conn->prepare("INSERT INTO Students_Classes (student_id, class_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $student_id, $class_id);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Failed to add student: ' . $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
}

echo json_encode(['success' => true]);
$conn->close(); // Close the connection
?>