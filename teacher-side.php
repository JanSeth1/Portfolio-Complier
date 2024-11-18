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
        <li><a href="javascript:void(0);" onclick="openReviewSubmissionsModal()"><i class="fas fa-folder-open"></i>View Submissions</a></li>
        <li><a href="javascript:void(0);" onclick="openSettingsModal()"><i class="fas fa-cog"></i>Settings</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
    </ul>
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeAddStudentModal()">&times;</span>
        <h2>Add Students to Class</h2>
        <input type="hidden" id="addStudentClassId" name="class_id">
        <form id="addStudentForm">
            <div class="form-group">
                <label for="studentUsername">Enter Student Usernames (comma-separated):</label>
                <input type="text" id="studentUsername" name="student_usernames" required>
            </div>
            <button type="submit" class="submit-button">Add Students</button>
        </form>
        <div id="addStudentFeedback" style="color: red;"></div>
        <h3>Available Students</h3>
        <table id="availableStudentsTable">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="availableStudentsList">
                <tr>
                    <td colspan="3">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Students Modal -->
<div id="studentsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal('studentsModal')">&times;</span>
        <h2>Students in Class</h2>
        <table id="studentsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentsList">
                <!-- Student rows will be dynamically inserted here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Manage Classes Modal -->
<div id="manageClassesModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeManageClassesModal()">&times;</span>
        <h2>Manage Your Classes</h2>
        <div id="classesList">
            <!-- Classes will be dynamically loaded here -->
        </div>
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
            <div class="card" onclick="openReviewSubmissionsModal()">
                <h3>Pending Submissions</h3>
                <p>12 to review</p>
            </div>
        </div>
    </section>

    <section class="quick-actions">
        <h2>Quick Actions</h2>
        <ul>
            <li><button type="button" onclick="openCreateClassModal()">Create New Class</button></li>
            <li><button id="reviewSubmissionsButton" type="button" onclick="openReviewSubmissionsModal()">Review Submissions</button></li>
        </ul>
    </section>
</div>

<!-- Review Submissions Modal -->
<div id="reviewSubmissionsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeReviewSubmissionsModal()">&times;</span>
        <h2>Review Submissions</h2>
        
        <!-- Dropdown for class selection -->
        <label for="classFilter">Select Class:</label>
        <select id="classFilter" onchange="fetchSubmissions()">
            <!-- Options will be dynamically populated -->
        </select>

        <!-- Tabs for filtering submissions -->
        <div class="tabs">
            <button class="tablinks" onclick="filterSubmissions('Pending')">Pending</button>
            <button class="tablinks" onclick="filterSubmissions('Approved')">Approved</button>
        </div>

        <table id="submissionsTable">
            <thead>
            <tr>
                <th>Username</th>
                <th>Title</th>
                <th>Description</th>
                <th>Submission Date</th>
                <th>Status</th> <!-- Header for status -->
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="submissionsList">
                <!-- Submission rows will be dynamically inserted here -->
            </tbody>
        </table>
    </div>
</div>


<!-- Detailed Review Modal -->
<div id="detailedReviewModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeDetailedReviewModal()">&times;</span>
        <h2>Submission Details</h2>
        <div id="submissionDetails">
            <!-- Details will be loaded here -->
        </div>
        
    </div>
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

<!-- Settings Modal -->
<div id="settingsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeSettingsModal()">&times;</span>
        <h2>Account Settings</h2>
        <form action="settings.php" method="post">
            <div class="form-container">
                <div class="form-fields">
                    <div class="form-group">
                        <label for="username">Your Name:</label>
                        <div class="input-container">
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                            <a href="#" class="change-link" onclick="toggleUsernameEdit()">Change</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <div class="input-container">
                            <input type="password" id="password" name="password" value="********" readonly>
                            <a href="#" class="change-link" onclick="togglePasswordEdit()">Change</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <a href="#" class="delete-account" onclick="deleteAccount()">Delete Your Account</a>
                <p>You will receive an email to confirm your decision. Please note, that all boards you have created will be permanently erased.</p>
            </div>

            <div class="actions">
                <button type="button" class="cancel" onclick="closeSettingsModal()">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<footer>
    <p>&copy; 2024 Teacher Portal</p>
</footer>

<script 
    src="teacher-side-script.js">
    document.getElementById('classFilter').addEventListener('change', fetchSubmissions);
</script>
</body>
</html>