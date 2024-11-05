// Open the modal for a specific category
function openModal(category) {
    const modal = document.getElementById('myModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');

    // Set the modal title based on the category
    modalTitle.textContent = `View ${category.charAt(0).toUpperCase() + category.slice(1)}`;

    // Fetch files for the selected category
    fetch('http://localhost/PHP/fetch-files.php?category_id=' + getCategoryId(category))
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(files => {
        modalBody.innerHTML = ''; // Clear previous content
        if (files.length > 0) {
            const table = document.createElement('table');
            table.classList.add('file-table');

            // Create table header
            const header = document.createElement('tr');
            header.innerHTML = `
                <th>File Name</th>
                <th>Uploaded On</th>
                <th>Actions</th>
            `;
            table.appendChild(header);

            // Create table rows
            files.forEach(file => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><a href="${file.file_path}" target="_blank">${file.title}</a></td>
                    <td>${new Date(file.submission_date).toLocaleString()}</td>
                    <td><button class="action-button">Download</button></td>
                `;
                table.appendChild(row);
            });

            modalBody.appendChild(table);
        } else {
            modalBody.innerHTML = '<p>No files uploaded in this category.</p>';
        }
    })
    .catch(error => {
        console.error('Error fetching files:', error);
        modalBody.innerHTML = `<p>Error loading files: ${error.message}. Please try again later.</p>`;
    });
    modal.style.display = 'block';
}

// Close the modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Get category ID based on category name
function getCategoryId(category) {
    switch (category) {
        case 'quizzes': return 2;
        case 'homework': return 1;
        case 'projects': return 3;
        default: return 0;
    }
}

// Open Settings Modal
function openSettingsModal() {
    document.getElementById("settingsModal").style.display = "block";
}

// Open Upload Modal
function openUploadModal() {
    document.getElementById("uploadModal").style.display = "block";
}

// Open Create Class Modal
function openCreateClassModal() {
    document.getElementById('createClassModal').style.display = 'block';
}

function closeCreateClassModal() {
    document.getElementById('createClassModal').style.display = 'none';
}

document.getElementById('createClassForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    const formData = new FormData(this);

    fetch('create_class.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Handle success (e.g., close modal, update UI)
            closeCreateClassModal();
            alert('Class created successfully!');
            // Optionally, refresh the list of classes or update the UI
        } else {
            // Display errors
            document.getElementById('formFeedback').textContent = data.error || 'An error occurred.';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('formFeedback').textContent = 'An error occurred. Please try again.';
    });
});

// Close the modal when clicking outside of it
window.onclick = function(event) {
    const modals = ['myModal', 'settingsModal', 'uploadModal', 'createClassModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
};

function openManageClassesModal() {
    fetchClasses();
    document.getElementById('manageClassesModal').style.display = 'block';
}

function closeManageClassesModal() {
    document.getElementById('manageClassesModal').style.display = 'none';
}

function fetchClasses() {
    fetch('fetch_classes.php')
        .then(response => response.json())
        .then(data => {
            const classesList = document.getElementById('classesList');
            classesList.innerHTML = ''; // Clear previous content

            if (data.success) {
                data.classes.forEach(classItem => {
                    const classDiv = document.createElement('div');
                    classDiv.classList.add('class-item');
                    classDiv.innerHTML = `
                        <h3>${classItem.class_name}</h3>
                        <button onclick="showStudentsSection(${classItem.class_id}, '${classItem.class_name}')">Manage Students</button>
                        <button onclick="editClass(${classItem.class_id})">Edit</button>
                        <button class="delete" onclick="deleteClass(${classItem.class_id})">Delete</button>
                    `;
                    classesList.appendChild(classDiv);
                });
            } else {
                classesList.innerHTML = `<p>Error: ${data.error}</p>`;
            }
        })
        .catch(error => {
            console.error('Error fetching classes:', error);
            document.getElementById('classesList').innerHTML = '<p>Error loading classes. Please try again later.</p>';
        });
}

function fetchClasses() {
    fetch('fetch_classes.php')
        .then(response => response.json())
        .then(data => {
            const classesList = document.getElementById('classesList');
            classesList.innerHTML = ''; // Clear previous content

            if (data.success) {
                data.classes.forEach(classItem => {
                    const classDiv = document.createElement('div');
                    classDiv.classList.add('class-item');
                    classDiv.innerHTML = `
                        <h3>${classItem.class_name}</h3>
                        <button onclick="showStudentsSection(${classItem.class_id}, '${classItem.class_name}')">Manage Students</button>
                        <button onclick="editClass(${classItem.class_id})">Edit</button>
                        <button class="delete" onclick="deleteClass(${classItem.class_id})">Delete</button>
                    `;
                    classesList.appendChild(classDiv);
                });
            } else {
                classesList.innerHTML = `<p>Error: ${data.error}</p>`;
            }
        })
        .catch(error => {
            console.error('Error fetching classes:', error);
            document.getElementById('classesList').innerHTML = '<p>Error loading classes. Please try again later.</p>';
        });
}

function showStudentsSection(classId, className) {
    console.log('Showing students for class:', className, 'with ID:', classId); // Debugging: Log class info
    document.getElementById('classTitle').textContent = className;
    document.getElementById('addStudentClassId').value = classId;
    fetchRegisteredStudents(classId);
    document.getElementById('studentsSection').style.display = 'block';
}

function fetchRegisteredStudents(classId) {
    console.log('Fetching students for class ID:', classId); // Debugging: Log fetch attempt
    fetch(`fetch_registered_students.php?class_id=${classId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data); // Debugging: Log received data
            const studentsTableBody = document.getElementById('studentsTable').querySelector('tbody');
            studentsTableBody.innerHTML = ''; // Clear previous content

            if (data.success) {
                if (data.students.length === 0) {
                    studentsTableBody.innerHTML = '<tr><td colspan="3">No students enrolled.</td></tr>';
                } else {
                    data.students.forEach(student => {
                        const studentRow = document.createElement('tr');
                        studentRow.innerHTML = `
                            <td><input type="checkbox" value="${student.user_id}"></td>
                            <td>${student.username}</td>
                            <td>${student.name}</td>
                        `;
                        studentsTableBody.appendChild(studentRow);
                    });
                }
            } else {
                studentsTableBody.innerHTML = `<tr><td colspan="3">Error: ${data.error}</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error fetching registered students:', error);
            document.getElementById('studentsTable').querySelector('tbody').innerHTML = '<tr><td colspan="3">Error loading students. Please try again later.</td></tr>';
        });
}

function addSelectedStudents() {
    const selectedStudentIds = Array.from(document.querySelectorAll('#studentsTable tbody input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.value);

    if (selectedStudentIds.length === 0) {
        alert('No students selected.');
        return;
    }

    const classId = document.getElementById('addStudentClassId').value;

    fetch('add_students_to_class.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ class_id: classId, student_ids: selectedStudentIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Students added successfully.');
            fetchRegisteredStudents(classId); // Refresh the student list for the class
        } else {
            alert('Error adding students: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error adding students:', error);
        alert('An error occurred while adding students. Please try again.');
    });
}

function closeManageClassesModal() {
    document.getElementById('manageClassesModal').style.display = 'none';
}

function closeAddStudentModal() {
    document.getElementById('addStudentModal').style.display = 'none';
}