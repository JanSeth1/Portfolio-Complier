<?php
header('Content-Type: application/json');

include 'db.php'; // Include your database connection

if (!isset($_GET['class_id'])) {
    echo json_encode(['success' => false, 'error' => 'Class ID is required.']);
    exit;
}

$class_id = intval($_GET['class_id']);

$query = "
    SELECT 
        u.user_id, 
        u.username, 
        u.role
    FROM 
        Users u
    JOIN 
        Students_Classes sc ON u.user_id = sc.student_id
    WHERE 
        sc.class_id = ? AND u.role = 'student'
";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    echo json_encode(['success' => true, 'students' => $students]);
} else {
    echo json_encode(['success' => true, 'students' => []]);
}

$stmt->close();
$conn->close();
?>