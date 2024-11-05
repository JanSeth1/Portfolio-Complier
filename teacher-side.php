<?php
session_start();
include 'db.php'; 

// Check if the user is logged in and has the role of a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'teacher') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id']; 
$query = "SELECT username FROM Users WHERE user_id = ? AND role = 'teacher'";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // If no user is found, redirect to login or an error page
    header("Location: index.php");
    exit;
}

$username = $user['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Teacher Portal</title>
    <link rel="stylesheet" href="teacher-css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <div class="brand">
        <h2>Teacher Portal</h2>
    </div>
    <ul id="sidebarMenu">
        <li><a href="teacher_dashboard.php"><i class="fas fa-home"></i>Dashboard Home</a></li>
        <li><a href="#" onclick="openManageClassesModal()"><i class="fas fa-chalkboard-teacher"></i>Manage Classes</a></li>
        <li><a href="view_submissions.php"><i class="fas fa-folder-open"></i>View Submissions</a></li>
        <li><a href="settings.php"><i class="fas fa-cog"></i>Settings</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
    </ul>
</div>

<!-- Manage Classes Modal
<div id="manageClassesModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeManageClassesModal()">&times;</span>
        <h2>Manage Your Classes</h2>
        <div id="classesList">
            Classes will be dynamically loaded here -->
        <!-- </div>
    </div>
</div> -->

<!-- Manage Classes Modal -->
<div id="manageClassesModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeManageClassesModal()">&times;</span>
        <h2>Manage Your Classes</h2>
        <div id="classesList">
            <!-- Classes will be dynamically loaded here -->
        </div>
        <div id="studentsSection" style="display: none;">
            <h3>Manage Students for <span id="classTitle"></span></h3>
            <input type="hidden" id="addStudentClassId">
            <table id="studentsTable">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Username</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Registered students will be dynamically loaded here -->
                </tbody>
            </table>
            <button class="primary-button" onclick="addSelectedStudents()">Add Selected Students</button>
        </div>
    </div>
</div>

<!-- Add Student Form Modal -->
<div id="addStudentModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAddStudentModal()">&times;</span>
        <h2>Add Student to <span id="addStudentClassTitle"></span></h2>
        <form id="addStudentForm">
            <input type="hidden" id="addStudentClassId" name="class_id">
            <label for="studentUsername">Student Username:</label>
            <input type="text" id="studentUsername" name="username" required>
            <button type="submit" class="primary-button">Add Student</button>
        </form>
    </div>
</div>
<!-- Main Content -->
<div class="main-content">
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    </header>

    <section class="dashboard-overview">
        <h2>Dashboard Overview</h2>
        <div class="summary">
            <div class="card" onclick="openManageClassesModal()">
                <h3>Total Classes</h3>
                <p>3 active</p>
            </div>
            <div class="card" onclick="location.href='view_submissions.php'">
                <h3>Pending Submissions</h3>
                <p>12 to review</p>
            </div>
        </div>
    </section>

    <section class="quick-actions">
        <h2>Quick Actions</h2>
        <ul>
            <li><button type="button" onclick="openCreateClassModal()">Create New Class</button></li>
            <li><button onclick="location.href='view_submissions.php'">Review Submissions</button></li>
        </ul>
    </section>
</div>

<!-- Create Class Modal -->
<div id="createClassModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeCreateClassModal()">&times;</span>
        <h2>Create New Class</h2>
        <form id="createClassForm">
            <div class="form-group">
                <label for="className">Class Name:</label>
                <input type="text" id="className" name="class_name" required>
            </div>
            <div class="form-group">
                <label for="classDescription">Description:</label>
                <textarea id="classDescription" name="class_description" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="classCode">Class Code:</label>
                <input type="text" id="classCode" name="class_code" required>
            </div>
            <button type="submit" class="submit-button">Create Class</button>
        </form>
        <div id="formFeedback" style="color: red;"></div>
    </div>
</div>

<!-- Button to open the modal -->
<button type="button" onclick="openCreateClassModal()">Create New Class</button>

<footer>
    <p>&copy; 2024 Teacher Portal</p>
</footer>

<script src="teacher-side-script.js"></script>
</body>
</html>