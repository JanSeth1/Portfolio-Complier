<?php
header('Content-Type: application/json');
include 'db.php'; // Ensure your DB connection settings are correct

$data = json_decode(file_get_contents('php://input'), true);
$class_id = $data['class_id'];
$student_usernames = $data['student_usernames']; // Assuming an array of usernames is sent

$conn->begin_transaction();

try {
    foreach ($student_usernames as $username) {
        // Get student ID from username
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ? AND role = 'student'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            throw new Exception("Student username not found: $username");
        }
        $student_id = $result->fetch_assoc()['user_id'];
        $stmt->close();

        // Insert student into class
        $stmt = $conn->prepare("INSERT INTO Students_Classes (student_id, class_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $student_id, $class_id);
        $stmt->execute();
        if ($stmt->affected_rows == 0) {
            throw new Exception("Failed to add student to class: $username");
        }
        $stmt->close();
    }
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>