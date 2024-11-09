// Array to store currently enrolled students
let currentStudents = [];

// Open the Manage Classes Modal
function openManageClassesModal() {
    fetchClasses();
    document.getElementById('manageClassesModal').style.display = 'block';
    document.getElementById('manageClassesModal').style.zIndex = '1010'; // Higher than other modals
    // Lower the z-index of other modals if they are open
    document.getElementById('studentsModal').style.zIndex = '1005';
    document.getElementById('addStudentModal').style.zIndex = '1005';
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
    document.getElementById('addStudentClassId').value = classId;  // Set the hidden input value
    openAddStudentModal(classId);  // Open the modal with the class ID

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

// Open the Add Student Modal
// Open the Add Student Modal with the class ID set
function openAddStudentModal(classId) {
    console.log('Opening Add Student Modal for class ID:', classId); 
    console.log('Class ID at openAddStudentModal:', classId);// Should log a number, not undefined
    document.getElementById('addStudentModal').style.display = 'block';
    document.getElementById('addStudentModal').style.zIndex = '1030'; // Ensure it's on top of everything
    document.getElementById('studentsModal').style.zIndex = '1020';
    document.getElementById('manageClassesModal').style.zIndex = '1010';
    document.getElementById('addStudentModal').style.display = 'block';
    document.getElementById('addStudentClassId').value = classId;
}
// Close the Add Student Modal
function closeAddStudentModal() {
    document.getElementById('addStudentModal').style.display = 'none';
}

// Handle Add Student Form Submission
document.getElementById('addStudentForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const studentUsernames = document.getElementById('studentUsername').value.trim().split(','); // Assuming usernames are comma-separated
    const classId = document.getElementById('addStudentClassId').value;
    console.log('Submitting students:', studentUsernames, 'for class ID:', classId); // Debugging output

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
            closeModal(); // Close the Add Student modal
            showStudentsSection(classId, ''); // Refresh the list of students
        } else {
            throw new Error(data.error || 'Failed to add students');
        }
    })
    .catch(error => {
        console.error('Error adding students:', error);
        alert('An error occurred. Please try again. ' + error.message);
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