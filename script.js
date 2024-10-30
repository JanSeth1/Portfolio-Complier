function openModal(category) {
    document.getElementById("modalTitle").innerText = `Uploaded ${category.charAt(0).toUpperCase() + category.slice(1)}`;
    fetchOverview(category);
    const modal = document.getElementById("myModal");
    modal.style.display = "block";
}

function fetchOverview(category) {
    fetch(`fetch_data.php?category=${category}`)
        .then(response => response.json())
        .then(data => {
            const modalBody = document.getElementById("modalBody");
            modalBody.innerHTML = ''; // Clear previous content
            if (data.length === 0) {
                modalBody.innerHTML = `<p>No uploads found in this category.</p>`;
            } else {
                data.forEach(item => {
                    const link = document.createElement('a');
                    link.href = `uploads/${item.filename}`; // Update with the correct file path
                    link.innerText = item.filename; // Display filename
                    link.target = '_blank'; // Open in a new tab
                    link.classList.add('upload-link'); // Optional: add a class for styling
                    modalBody.appendChild(link);
                    modalBody.appendChild(document.createElement('br')); // Line break
                });
            }
        })
        .catch(error => console.error('Error fetching overview data:', error));
}

function closeModal() {
    const modal = document.getElementById("myModal");
    modal.style.display = "none";
}

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