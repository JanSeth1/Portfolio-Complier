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
function closeModal() {
    document.getElementById('myModal').style.display = 'none';
}

// Get category ID based on category name
function getCategoryId(category) {
    switch (category) {
        case 'quizzes': return 3;
        case 'homework': return 1;
        case 'projects': return 4;
        default: return 0;
    }
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById("myModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
};

// Open Settings Modal
function openSettingsModal() {
    const settingsModal = document.getElementById("settingsModal");
    settingsModal.style.display = "block";
}

// Close Settings Modal
function closeSettingsModal() {
    const settingsModal = document.getElementById("settingsModal");
    settingsModal.style.display = "none";
}

// Close the modal when clicking outside of it
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
}

function openSettingsModal() {
        document.getElementById("settingsModal").style.display = "block";
    }

    function closeSettingsModal() {
        document.getElementById("settingsModal").style.display = "none";
    }

    window.onclick = function(event) {
        // ... your existing onclick function ...
        const settingsModal = document.getElementById("settingsModal");
        if (event.target === settingsModal) {
            settingsModal.style.display = "none";
        }
    };

    // Open Upload Modal
function openUploadModal() {
    const uploadModal = document.getElementById("uploadModal");
    uploadModal.style.display = "block";
}

// Close Upload Modal
function closeUploadModal() {
    const uploadModal = document.getElementById("uploadModal");
    uploadModal.style.display = "none";
}

// function openModal(category) {
//     const modal = document.getElementById('myModal');
//     const modalTitle = document.getElementById('modalTitle');
//     const modalBody = document.getElementById('modalBody');

//     // Set the modal title based on the category
//     modalTitle.textContent = `View ${category.charAt(0).toUpperCase() + category.slice(1)}`;

//     // Fetch files for the selected category
//     fetch(`fetch_files.php?category_id=${getCategoryId(category)}`)
//         .then(response => {
//             if (!response.ok) {
//                 throw new Error(`HTTP error! status: ${response.status}`);
//             }
//             return response.json();
//         })
//         .then(files => {
//             modalBody.innerHTML = ''; // Clear previous content
//             if (files.length > 0) {
//                 files.forEach(file => {
//                     const fileElement = document.createElement('div');
//                     fileElement.innerHTML = `
//                         <h4>${file.title}</h4>
//                         <p>Uploaded on: ${new Date(file.submission_date).toLocaleString()}</p>
//                         <a href="${file.file_path}" target="_blank">Download</a>
//                     `;
//                     modalBody.appendChild(fileElement);
//                 });
//             } else {
//                 modalBody.innerHTML = '<p>No files uploaded in this category.</p>';
//             }
//         })
//         .catch(error => {
//             console.error('Error fetching files:', error);
//             modalBody.innerHTML = `<p>Error loading files: ${error.message}. Please try again later.</p>`;
//         });

//     modal.style.display = 'block';
// }

// // Close the modal
// function closeModal() {
//     document.getElementById('myModal').style.display = 'none';
// }

// // Get category ID based on category name
// function getCategoryId(category) {
//     switch (category) {
//         case 'quizzes': return 2;
//         case 'homework': return 1;
//         case 'projects': return 3;
//         default: return 0;
//     }
// }

// // Close the modal when clicking outside of it
// window.onclick = function(event) {
//     const modal = document.getElementById("myModal");
//     if (event.target === modal) {
//         modal.style.display = "none";
//     }
// };