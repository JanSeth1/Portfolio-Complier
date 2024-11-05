<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Assuming you have stored the student ID in the session
$student_id = $_SESSION['user_id']; // Replace with actual session variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $upload_dir = 'uploads/';

    // Debugging: Output the IDs being used
    echo "Student ID: $student_id, Category ID: $category_id<br>";

    // Check if file was uploaded without errors
    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] == 0) {
        $file_name = basename($_FILES['fileToUpload']['name']);
        $file_path = $upload_dir . $file_name;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $file_path)) {
            // Prepare the SQL statement
            $query = "INSERT INTO Student_Work (student_id, category_id, title, description, file_path, submission_date) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);

            if ($stmt) {
                $stmt->bind_param("iisss", $student_id, $category_id, $title, $description, $file_path);
                if ($stmt->execute()) {
                    echo "File uploaded and record saved successfully.";
                } else {
                    echo "Error executing query: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Prepare failed: " . $conn->error;
            }
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "Error: " . $_FILES['fileToUpload']['error'];
    }
}

$conn->close();
?>