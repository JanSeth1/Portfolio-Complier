// Array to store currently enrolled students
let currentStudents = [];

// Open the Add Student Modal
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

// Close the Add Student Modal
function closeAddStudentModal() {
    document.getElementById('addStudentModal').style.display = 'none';
}

// Function to add a student to the class
function addStudentToClass(studentId, classId) {
    console.log('Adding student with ID:', studentId, 'to class ID:', classId);
    
    // Show a loading indicator or disable the button
    const availableStudentsList = document.getElementById('availableStudentsList');
    availableStudentsList.innerHTML += '<tr><td colspan="3">Adding student...</td></tr>'; // Show adding indicator

    fetch('add_students_to_class.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ student_id: studentId, class_id: classId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Student added successfully!');
            fetchAvailableStudents(classId); // Refresh the available students list
            showStudentsSection(classId, ''); // Optionally refresh the enrolled students list
        } else {
            alert('Error adding student: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error adding student:', error);
        alert('Failed to add student. Please try again later.');
    });
}

// Open the Manage Classes Modal
function openManageClassesModal() {
    fetchClasses();
    document.getElementById('manageClassesModal').style.display = 'block';
    document.getElementById('manageClassesModal').style.zIndex = '1010'; // Lower than add student modal
    document.getElementById('studentsModal').style.zIndex = '1005'; // Lower than manage classes modal
    document.getElementById('addStudentModal').style.zIndex = '1005'; // Ensure other modals are lower
}

// Fetch available students who are not registered in the class
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

// Close the Manage Classes Modal
function closeManageClassesModal() {
    document.getElementById('manageClassesModal').style.display = 'none';
}

// Fetch Classes for the Teacher
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

// Open Create Class Modal
function openCreateClassModal() {
    document.getElementById('createClassModal').style.display = 'block';
}

// Close Create Class Modal
function closeCreateClassModal() {
    document.getElementById('createClassModal').style.display = 'none';
}

// Open Settings Modal
function openSettingsModal() {
    document.getElementById('settingsModal').style.display = 'block';
}

// Close Settings Modal
function closeSettingsModal() {
    document.getElementById('settingsModal').style.display = 'none';
}

// Show Students Section
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

function closeModal() {
    document.getElementById('studentsModal').style.display = 'none';
}

// Handle Add Student Form Submission
document.getElementById('addStudentForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Get the class ID from the hidden input field
    const classId = document.getElementById('addStudentClassId').value;

    // Get the student usernames from the input field
    const studentUsernames = document.getElementById('studentUsername').value.trim().split(',').map(username => username.trim()); // Trim whitespace

    // Validate input
    if (studentUsernames.length === 0 || studentUsernames.some(username => username === '')) {
        alert('Please enter at least one valid username.');
        return;
    }

    console.log('Submitting students:', studentUsernames, 'for class ID:', classId); // Debugging output

    // Fetch request to add students to the class
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
        console.log('Response from server:', data); // Log the response
        if (data.success) {
            alert('Students added successfully!');
            closeAddStudentModal(); // Close the Add Student modal
            showStudentsSection(classId, ''); // Refresh the list of students
        } else {
            throw new Error(data.error || 'Failed to add students');
        }
    })
    .catch(error => {
        console.error('Error adding students:', error);
        alert('An error occurred while adding students: ' + error.message);
    });
});

// Close the modal when clicking outside of it
window.onclick = function(event) {
    const modals = ['manageClassesModal', 'settingsModal', 'createClassModal', 'studentsModal', 'addStudentModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
};

function editStudent(studentId) {
    console.log('Editing student with ID:', studentId);
    // Implement logic to edit student details
    alert("Edit student functionality not implemented yet.");
}

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

function editClass(classId) {
    console.log('Editing class with ID:', classId);
    // Implement logic to edit class
}

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

function deleteAccount() {
    if (confirm('Are you sure you want to delete your account?')) {
        alert("Account deletion functionality not implemented.");
    }
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
            if (data.submissions.length > 0) {
                data.submissions.forEach(submission => {
                    const submissionRow = document.createElement('tr');
                    submissionRow.innerHTML = `
                        <td>${submission.username}</td>
                        <td>${submission.title}</td>
                        <td>${submission.description}</td>
                        <td>${new Date(submission.submission_date).toLocaleString()}</td>
                        <td>
                            <button onclick="reviewSubmission(${submission.work_id})">Review</button>
                        </td>
                    `;
                    submissionsList.appendChild(submissionRow);
                });
            } else {
                submissionsList.innerHTML = '<tr><td colspan="5">No submissions found.</td></tr>';
            }
        } else {
            submissionsList.innerHTML = `<tr><td colspan="5">Error: ${data.error}</td></tr>`;
        }
    } catch (error) {
        console.error('Error fetching submissions:', error);
        submissionsList.innerHTML = '<tr><td colspan="5">Failed to load submissions. Please try again later.</td></tr>';
    }
}

// Function to populate the class filter dropdown
async function populateClassFilter() {
    try {
        const response = await fetch('fetch_classes.php'); // Endpoint to get classes for the teacher
        const data = await response.json();
        const classFilter = document.getElementById('classFilter');
        classFilter.innerHTML = ''; // Clear previous options

        if (data.success) {
            data.classes.forEach(cls => {
                const option = document.createElement('option');
                option.value = cls.class_id;
                option.textContent = cls.class_name;
                classFilter.appendChild(option);
            });
        } else {
            console.error('Error fetching classes:', data.error);
        }
    } catch (error) {
        console.error('Error fetching classes:', error);
    }
}

// Call this function on page load to populate the class filter
populateClassFilter();

// Call this function when the Review Submissions button is clicked
document.getElementById('reviewSubmissionsButton').addEventListener('click', fetchSubmissions);

function reviewSubmission(submissionId) {
    const feedback = prompt("Enter your feedback for this submission:");
    if (feedback) {
        fetch('review_submission.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ submission_id: submissionId, feedback: feedback })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Feedback submitted successfully!');
                fetchSubmissions(); // Refresh the submissions list
            } else {
                alert('Error submitting feedback: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error submitting feedback:', error);
            alert('Failed to submit feedback. Please try again later.');
        });
    }
}

function openReviewSubmissionsModal() {
    document.getElementById('reviewSubmissionsModal').style.display = 'block';
    fetchSubmissions(); // Load submissions when opening the modal
}

function closeReviewSubmissionsModal() {
    document.getElementById('reviewSubmissionsModal').style.display = 'none';
}