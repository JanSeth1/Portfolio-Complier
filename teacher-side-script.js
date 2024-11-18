// Global array to store currently enrolled students
let currentStudents = [];

// Document ready function to initialize event listeners and initial data fetch
document.addEventListener('DOMContentLoaded', function() {
    populateClassFilter();
    initializeEventListeners();
});

// Initialize all event listeners
function initializeEventListeners() {
    document.getElementById('reviewSubmissionsButton').addEventListener('click', fetchSubmissions);
    document.getElementById('addStudentForm').addEventListener('submit', handleAddStudentFormSubmission);
    window.onclick = handleModalOutsideClick;
}

// Function to handle modal outside click
function handleModalOutsideClick(event) {
    const modals = ['manageClassesModal', 'settingsModal', 'createClassModal', 'studentsModal', 'addStudentModal', 'reviewSubmissionsModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

// Function to handle the Add Student form submission
function handleAddStudentFormSubmission(event) {
    event.preventDefault(); // Prevent the default form submission

    const classId = document.getElementById('addStudentClassId').value;
    const studentUsernames = document.getElementById('studentUsername').value.trim().split(',').map(username => username.trim());

    if (studentUsernames.length === 0 || studentUsernames.some(username => username === '')) {
        alert('Please enter at least one valid username.');
        return;
    }

    addStudentsToClass(classId, studentUsernames);
}

// Function to add students to a class
function addStudentsToClass(classId, studentUsernames) {
    console.log('Submitting students:', studentUsernames, 'for class ID:', classId);

    fetch('add_students_to_class.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ class_id: classId, student_usernames: studentUsernames })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Students added successfully!');
            closeAddStudentModal();
            showStudentsSection(classId, ''); // Refresh the list of students
        } else {
            throw new Error(data.error || 'Failed to add students');
        }
    })
    .catch(error => {
        console.error('Error adding students:', error);
        alert('An error occurred while adding students: ' + error.message);
    });
}

// Function to open the Add Student Modal
function openAddStudentModal(classId) {
    console.log('Opening Add Student Modal for class ID:', classId);
    document.getElementById('addStudentModal').style.display = 'block';
    document.getElementById('addStudentModal').style.zIndex = '1030'; // Ensure it's on top of everything
    document.getElementById('studentsModal').style.zIndex = '1020'; // Lower than add student modal
    document.getElementById('manageClassesModal').style.zIndex = '1010'; // Lower than students modal
    document.getElementById('addStudentClassId').value = classId; // Set the class ID when opening the modal
    console.log('Class ID set in modal:', document.getElementById('addStudentClassId').value); // Check if it's set

    // Fetch available students who are not registered in this class
    fetchAvailableStudents(classId);
}

// Function to close the Add Student Modal
function closeAddStudentModal() {
    document.getElementById('addStudentModal').style.display = 'none';
}

// Function to open the Manage Classes Modal
function openManageClassesModal() {
    fetchClasses();
    document.getElementById('manageClassesModal').style.display = 'block';
    document.getElementById('manageClassesModal').style.zIndex = '1010'; // Lower than add student modal
    document.getElementById('studentsModal').style.zIndex = '1005'; // Lower than manage classes modal
    document.getElementById('addStudentModal').style.zIndex = '1005'; // Ensure other modals are lower
}

// Function to close the Manage Classes Modal
function closeManageClassesModal() {
    document.getElementById('manageClassesModal').style.display = 'none';
}

// Function to fetch available students who are not registered in the class
function fetchAvailableStudents(classId) {
    const availableStudentsList = document.getElementById('availableStudentsList');
    availableStudentsList.innerHTML = '<tr><td colspan="3">Loading...</td></tr>'; // Show loading indicator

    fetch(`fetch_available_students.php?class_id=${classId}`)
        .then(response => response.json())
        .then(data => {
            availableStudentsList.innerHTML = ''; // Clear loading indicator
            if (data.success) {
                if (data.students.length > 0) {
                    data.students.forEach(student => {
                        const studentRow = document.createElement('tr');
                        studentRow.innerHTML = `
                            <td>${student.username}</td>
                            <td>${student.email}</td>
                            <td>
                                <button onclick="addStudentToClass(${student.user_id}, ${classId})">Add</button>
                            </td>
                        `;
                        availableStudentsList.appendChild(studentRow);
                    });
                } else {
                    availableStudentsList.innerHTML = '<tr><td colspan="3">No available students found.</td></tr>';
                }
            } else {
                availableStudentsList.innerHTML = `<tr><td colspan="3">Error: ${data.error}</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error fetching available students:', error);
            availableStudentsList.innerHTML = '<tr><td colspan="3">Failed to load available students. Please try again later.</td></tr>';
        });
}

// Function to fetch classes for the teacher
function fetchClasses() {
    fetch('fetch_teacher_classes.php')
        .then(response => response.json())
        .then(data => {
            const classesList = document.getElementById('classesList');
            classesList.innerHTML = ''; // Clear previous content

            if (data.success) {
                if (data.classes.length > 0) {
                    data.classes.forEach(classItem => {
                        const classDiv = document.createElement('div');
                        classDiv.classList.add('class-item');
                        classDiv.innerHTML = `
                            <h3>${classItem.class_name}</h3>
                            <button onclick="showStudentsSection(${classItem.class_id}, '${classItem.class_name}')">Manage Class</button>
                            <button onclick="openAddStudentModal(${classItem.class_id})">Add Students</button>
                            <button onclick="editClass(${classItem.class_id})">Edit</button>
                            <button class="delete" onclick="deleteClass(${classItem.class_id})">Delete</button>
                        `;
                        classesList.appendChild(classDiv);
                    });
                } else {
                    classesList.innerHTML = '<p>No classes found.</p>';
                }
            } else {
                classesList.innerHTML = `<p>Error: ${data.error}</p>`;
            }
        })
        .catch(error => {
            console.error('Error fetching classes:', error);
            classesList.innerHTML = '<p>Error loading classes. Please try again later.</p>';
        });
}

// Function to open the Create Class Modal
function openCreateClassModal() {
    document.getElementById('createClassModal').style.display = 'block';
}

// Function to close the Create Class Modal
function closeCreateClassModal() {
    document.getElementById('createClassModal').style.display = 'none';
}

// Function to open the Settings Modal
function openSettingsModal() {
    document.getElementById('settingsModal').style.display = 'block';
}

// Function to close the Settings Modal
function closeSettingsModal() {
    document.getElementById('settingsModal').style.display = 'none';
}

// Function to show the Students Section
function showStudentsSection(classId, className) {
    console.log('Received class ID:', classId);
    document.getElementById('studentsModal').style.display = 'block';
    document.getElementById('studentsModal').style.zIndex = '1020'; // Highest when shown
    document.getElementById('manageClassesModal').style.zIndex = '1010';
    document.getElementById('addStudentModal').style.zIndex = '1005';
    console.log('Showing students for class:', className, 'with ID:', classId);

    // Set the class ID in the hidden input field of the Add Student form
    document.getElementById('addStudentClassId').value = classId;

    const studentsList = document.getElementById('studentsList');
    studentsList.innerHTML = '<tr><td colspan="3">Loading...</td></tr>'; // Show loading indicator

    fetch(`fetch_students.php?class_id=${classId}`)
        .then(response => response.json())
        .then(data => {
            studentsList.innerHTML = ''; // Clear loading indicator
            if (data.success) {
                currentStudents = data.students.map(student => student.username); // Update the student list
                if (data.students.length > 0) {
                    data.students.forEach(student => {
                        const studentRow = document.createElement('tr');
                        studentRow.innerHTML = `
                            <td>${student.username}</td>
                            <td>${student.email}</td>
                            <td>
                                <button onclick="editStudent(${student.user_id})">Edit</button>
                                <button onclick="removeStudent(${student.user_id}, ${classId})">Remove</button>
                            </td>
                        `;
                        studentsList.appendChild(studentRow);
                    });
                } else {
                    studentsList.innerHTML = '<tr><td colspan="3">No students found in this class.</td></tr>';
                }
            } else {
                alert('Error fetching students: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error fetching students:', error);
            studentsList.innerHTML = '<tr><td colspan="3">Failed to load students. Please try again later.</td></tr>';
        });
}

// Function to close the modal
function closeModal() {
    document.getElementById('studentsModal').style.display = 'none';
}

// Function to edit a student
function editStudent(studentId) {
    console.log('Editing student with ID:', studentId);
    // Implement logic to edit student details
    alert("Edit student functionality not implemented yet.");
}

// Function to remove a student from a class
function removeStudent(studentId, classId) {
    if (confirm('Are you sure you want to remove this student from the class?')) {
        fetch('remove_student.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ student_id: studentId, class_id: classId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Student removed successfully.');
                showStudentsSection(classId, ''); // Refresh the list of students
            } else {
                alert('Error removing student: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error removing student:', error);
            alert('An error occurred while removing the student. Please try again.');
        });
    }
}

// Function to edit a class
function editClass(classId) {
    console.log('Editing class with ID:', classId);
    // Implement logic to edit class
}

// Function to delete a class
function deleteClass(classId) {
    if (confirm('Are you sure you want to delete this class?')) {
        fetch('delete_class.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ class_id: classId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Class deleted successfully.');
                fetchClasses(); // Refresh the list of classes
            } else {
                alert('Error deleting class: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error deleting class:', error);
            alert('An error occurred while deleting the class. Please try again.');
        });
    }
}

// Function to delete an account
function deleteAccount() {
    if (confirm('Are you sure you want to delete your account?')) {
        alert("Account deletion functionality not implemented.");
    }
}




document.addEventListener('DOMContentLoaded', function() {
    populateClassFilter();
    initializeEventListeners();
});

function initializeEventListeners() {
    document.getElementById('reviewSubmissionsButton').addEventListener('click', openReviewSubmissionsModal);
    document.getElementById('addStudentForm').addEventListener('submit', handleAddStudentFormSubmission);
    window.onclick = function(event) {
        handleModalOutsideClick(event);
    };
}

function handleModalOutsideClick(event) {
    const modals = ['manageClassesModal', 'settingsModal', 'createClassModal', 'studentsModal', 'addStudentModal', 'reviewSubmissionsModal', 'detailedReviewModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

async function fetchSubmissions() {
    const classFilter = document.getElementById('classFilter').value;
    try {
        const response = await fetch('fetch_submissions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ class_id: classFilter })
        });
        const data = await response.json();
        const submissionsList = document.getElementById('submissionsList');
        submissionsList.innerHTML = ''; // Clear previous content

        if (data.success) {
            data.submissions.forEach(submission => {
                const submissionRow = document.createElement('tr');
                submissionRow.innerHTML = `
                    <td>${submission.username}</td>
                    <td>${submission.title}</td>
                    <td>${submission.description}</td>
                    <td>${new Date(submission.submission_date).toLocaleString()}</td>
                    <td>${submission.status || 'Pending'}</td> <!-- Display the status, default to 'Pending' if not available -->
                    <td>
                        <button onclick="openDetailedReviewModal(${submission.work_id})">Review</button>
                    </td>
                `;
                submissionsList.appendChild(submissionRow);
            });
        } else {
            submissionsList.innerHTML = `<tr><td colspan="6">Error: ${data.error}</td></tr>`;
        }
    } catch (error) {
        console.error('Error fetching submissions:', error);
        submissionsList.innerHTML = '<tr><td colspan="6">Failed to load submissions. Please try again later.</td></tr>';
    }
}

async function populateClassFilter() {
    try {
        const response = await fetch('fetch_teacher_classes.php');
        const data = await response.json();

        const classFilter = document.getElementById('classFilter');
        classFilter.innerHTML = '';

        if (data.success) {
            data.classes.forEach(cls => {
                const option = document.createElement('option');
                option.value = cls.class_id;
                option.textContent = cls.class_name;
                classFilter.appendChild(option);
            });
        } else {
            const option = document.createElement('option');
            option.textContent = "No classes available";
            classFilter.appendChild(option);
        }
    } catch (error) {
        console.error('Error fetching classes:', error);
    }
}

function openReviewSubmissionsModal() {
    document.getElementById('reviewSubmissionsModal').style.display = 'block';
    fetchSubmissions(); // Load submissions when opening the modal
}

function closeReviewSubmissionsModal() {
    document.getElementById('reviewSubmissionsModal').style.display = 'none';
}

function openDetailedReviewModal(workId) {
    document.getElementById('detailedReviewModal').style.display = 'block';
    fetchSubmissionDetails(workId);
}

async function fetchSubmissionDetails(workId) {
    try {
        const response = await fetch(`fetch_submission_details.php?work_id=${workId}`);
        const data = await response.json();
        if (data.success) {
            document.getElementById('submissionDetails').innerHTML = `
                <p><strong>Title:</strong> ${data.submission.title}</p>
                <p><strong>Description:</strong> ${data.submission.description}</p>
                <p><strong>Submitted At:</strong> ${new Date(data.submission.submission_date).toLocaleString()}</p>
                <a href="${data.submission.file_path}" target="_blank">View File</a>
                <button onclick="updateStatus(${workId}, 'approved')">Approve</button>
                <button onclick="updateStatus(${workId}, 'rejected')">Reject</button>
            `;
        } else {
            document.getElementById('submissionDetails').innerHTML = 'Failed to load submission details.';
        }
    } catch (error) {
        console.error('Error fetching submission details:', error);
        document.getElementById('submissionDetails').innerHTML = 'Failed to load submission details.';
    }
}

async function updateStatus(workId, status) {
    try {
        const response = await fetch('update_submission_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ work_id: workId, status: status })
        });
        const data = await response.json();
        if (data.success) {
            alert('Status updated successfully!');
            closeDetailedReviewModal();
            fetchSubmissions(); // Refresh the list of submissions
        } else {
            alert('Failed to update status.');
        }
    } catch (error) {
        console.error('Error updating status:', error);
        alert('Failed to update status.');
    }
}

function closeDetailedReviewModal() {
    document.getElementById('detailedReviewModal').style.display = 'none';
}
