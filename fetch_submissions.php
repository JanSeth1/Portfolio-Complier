<?php
session_start();
include 'db.php';  // Ensure this file correctly initializes the $conn object

header('Content-Type: application/json');

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if class_id is provided
if (!isset($data['class_id'])) {
    echo json_encode(['success' => false, 'error' => 'Class ID not provided']);
    exit;
}

$class_id = $data['class_id'];

// Prepare the SQL query
$query = "SELECT Student_Work.*, Users.username, Categories.category_name 
          FROM Student_Work 
          JOIN Users ON Student_Work.student_id = Users.user_id
          JOIN Categories ON Student_Work.category_id = Categories.category_id
          WHERE Student_Work.class_id = ?";  // Ensure the correct column name in WHERE clause

$stmt = $conn->prepare($query);

// Check if the prepare was successful
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

// Bind parameters and execute the statement
$stmt->bind_param("i", $class_id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

// Fetch results
$result = $stmt->get_result();
$submissions = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $submissions[] = [
            'work_id' => $row['work_id'],
            'username' => $row['username'],
            'title' => $row['title'],
            'description' => $row['description'],
            'submission_date' => $row['submission_date'],
            'status' => $row['status'],  // Assuming 'status' is a column in your Student_Work table
            'category_name' => $row['category_name']
        ];
    }
    echo json_encode(['success' => true, 'submissions' => $submissions]);
} else {
    echo json_encode(['success' => false, 'error' => 'Fetch failed: ' . $stmt->error]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>