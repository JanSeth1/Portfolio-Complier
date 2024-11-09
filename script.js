document.addEventListener('DOMContentLoaded', function() {
    fetchClasses();
});

let selectedCategory = null;

// Function to open the modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
    } else {
        console.error('Modal not found:', modalId);
    }
}

// Function to close the modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    } else {
        console.error('Modal not found:', modalId);
    }
}

// Function to select a category and set "All Classes" as default
function selectCategory(category) {
    selectedCategory = category;
    console.log('Category selected:', selectedCategory);

    // Set "All Classes" as the default selection
    const classSelect = document.getElementById('filterClass');
    classSelect.value = ''; // Assuming '' is used for "All Classes"

    console.log('Default class set to: All Classes');
    alert('Category selected: ' + category + '. Default class set to: All Classes.');

    // Open the modal after setting the default class
    openModal('viewUploadsModal');
}

// Function to fetch enrolled classes
function fetchClasses() {
    fetch('fetch_classes.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const classSelect = document.getElementById('filterClass');
                classSelect.innerHTML = '<option value="">All Classes</option>'; // Default option for all classes

                data.classes.forEach(cls => {
                    const option = document.createElement('option');
                    option.value = cls.class_id;
                    option.textContent = cls.class_name;
                    classSelect.appendChild(option);
                });
            } else {
                console.error('Error fetching classes:', data.error);
            }
        })
        .catch(error => {
            console.error('Error fetching classes:', error);
        });
}

function fetchUploads() {
    if (!selectedCategory) {
        console.error('No category selected');
        alert('Please select a category by clicking on a card.');
        return;
    }

    const classId = document.getElementById('filterClass').value;
    if (!classId) {
        console.error('No class selected');
        alert('Please select a class from the dropdown.');
        return;
    }

    if (!userId) {
        console.error('No user ID available');
        return;
    }

    const categoryMap = {
        'quizzes': 3,
        'homework': 1,
        'projects': 4,
    };

    const categoryId = categoryMap[selectedCategory];
    if (categoryId === undefined) {
        console.error('Invalid category:', selectedCategory);
        return;
    }

    let url = `fetch-files.php?class_id=${classId}&category_id=${categoryId}&user_id=${userId}`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(files => {
            console.log('Files fetched:', files);
            const uploadsList = document.getElementById('uploadsList');
            uploadsList.innerHTML = ''; // Clear previous content
            if (files.length > 0) {
                const table = document.createElement('table');
                table.classList.add('file-table');

                // Create the table header
                const header = document.createElement('tr');
                header.innerHTML = `
                    <th>Class</th>
                    <th>File Name</th>
                    <th>Uploaded On</th>
                    <th>Status</th>
                    <th>Actions</th>
                `;
                table.appendChild(header);

                // Populate table rows with files data
                files.forEach(file => {
                    const row = document.createElement('tr');

                    // Define a status class based on the file status
                    let statusClass = '';
                    switch (file.status) {
                        case 'submitted': statusClass = 'status-submitted'; break;
                        case 'reviewed': statusClass = 'status-reviewed'; break;
                        case 'approved': statusClass = 'status-approved'; break;
                        case 'rejected': statusClass = 'status-rejected'; break;
                        default: statusClass = '';
                    }

                    // Populate the row with file details, including the class name and download button
                    row.innerHTML = `
                    <td>${file.course_name}</td>  <!-- Changed from class_name to course_name -->
                    <td><a href="${file.file_path}" target="_blank">${file.title}</a></td>
                    <td>${new Date(file.submission_date).toLocaleString()}</td>
                    <td><span class="status-label ${statusClass}">${file.status.charAt(0).toUpperCase() + file.status.slice(1)}</span></td>
                    <td><a href="${file.file_path}" download class="download-btn">Download</a></td>
                `;
                
                    table.appendChild(row);
                });

                uploadsList.appendChild(table);
            } else {
                uploadsList.innerHTML = '<p>No files uploaded in this category.</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching files:', error);
            const uploadsList = document.getElementById('uploadsList');
            uploadsList.innerHTML = `<p>Error loading files: ${error.message}. Please try again later.</p>`;
        });
}

// Call this function when the page loads
document.addEventListener('DOMContentLoaded', fetchClasses);


function updateStatus(workId, newStatus) {
    fetch('update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ work_id: workId, status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status updated successfully');
        } else {
            console.error('Error updating status:', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}


function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
    } else {
        console.error('Modal not found:', modalId);
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById("viewUploadsModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
};

window.onclick = function(event) {
    const uploadModal = document.getElementById("uploadModal");
    const modal = document.getElementById("myModal");
    const settingsModal = document.getElementById("settingsModal");
    if (event.target === uploadModal) {
        uploadModal.style.display = "none";
    }
    if (event.target === modal) {
        modal.style.display = "none";
    }
    if (event.target === settingsModal) {
        settingsModal.style.display = "none";
    }
};

const usernameInput = document.getElementById("username");
    const currentValue = usernameInput.value;
    if (usernameInput.readOnly) {
        usernameInput.readOnly = false;
        usernameInput.focus();
        usernameInput.value = ''; // Clear the value to allow the user to enter a new one
    } else {
        usernameInput.readOnly = true;
        usernameInput.value = currentValue; // Restore the original value
    }


// Toggle the readonly attribute to allow the user to edit the password
function togglePasswordEdit() {
    const passwordInput = document.getElementById("password");
    const currentValue = passwordInput.value;
    if (passwordInput.readOnly) {
        passwordInput.readOnly = false;
        passwordInput.focus();
        passwordInput.value = ''; // Clear the value to allow the user to enter a new one
    } else {
        passwordInput.readOnly = true;
        passwordInput.value = currentValue; // Restore the original value
    }
}

// Open the settings modal
function openSettingsModal() {
    document.getElementById("settingsModal").style.display = "block";
}

// Close the settings modal
function closeSettingsModal() {
    document.getElementById("settingsModal").style.display = "none";
}

// Function to delete the user account (Optional)
function deleteAccount() {
    if (confirm('Are you sure you want to delete your account?')) {
        // You can handle the deletion process here, for example by sending an AJAX request
        alert("Account deletion functionality not implemented.");
    }
}

function openUploadModal() {
    const uploadModal = document.getElementById("uploadModal");
    uploadModal.style.display = "block";
}

function closeUploadModal() {
    const uploadModal = document.getElementById("uploadModal");
    uploadModal.style.display = "none";
}
