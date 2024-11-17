<?php
header('Content-Type: application/json');
include 'db.php'; // Ensure your DB connection settings are correct

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the input data
$data = json_decode(file_get_contents('php://input'), true);
$class_id = $data['class_id'];
$student_usernames = $data['student_usernames']; // Assuming an array of usernames is sent

// Validate input
if (!is_array($student_usernames) || empty($class_id)) {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    exit;
}

// Start a transaction
$conn->begin_transaction();

try {
    foreach ($student_usernames as $username) {
        // Trim whitespace and check if username is not empty
        $username = trim($username);
        if (empty($username)) {
            continue; // Skip empty usernames
        }

        // Get student ID from username
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? AND role = 'student'");
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            throw new Exception("Student username not found: $username");
        }
        
        $student_id = $result->fetch_assoc()['user_id'];
        $stmt->close();

        // Check if the student is already in the class
        $stmt = $conn->prepare("SELECT * FROM students_classes WHERE student_id = ? AND class_id = ?");
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        $stmt->bind_param("ii", $student_id, $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("Student $username is already enrolled in this class.");
        }
        $stmt->close();

        // Insert student into class
        $stmt = $conn->prepare("INSERT INTO students_classes (student_id, class_id) VALUES (?, ?)");
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }
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
    error_log($e->getMessage()); // Log the error for debugging
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// Close the database connection
$conn->close();
?>