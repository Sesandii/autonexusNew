document.addEventListener('DOMContentLoaded', function() {
    const currentMileageInput = document.getElementById('current_mileage');
    const intervalInput = document.getElementById('service_interval');
    const nextDueDisplay = document.getElementById('next_service_display');
    const hiddenNextDue = document.getElementById('next_service_due_val');

    function calculateNextService() {
        const current = parseInt(currentMileageInput.value);
        const interval = parseInt(intervalInput.value);

        if (!isNaN(current) && !isNaN(interval)) {
            const nextDue = current + interval;
            nextDueDisplay.textContent = nextDue.toLocaleString() + " km";
            if(hiddenNextDue) hiddenNextDue.value = nextDue;
            nextDueDisplay.style.borderColor = "#10b981"; 
            nextDueDisplay.style.color = "#003366";
        } else {
            nextDueDisplay.textContent = "- km";
            if(hiddenNextDue) hiddenNextDue.value = "";
            nextDueDisplay.style.borderColor = "#cbd5e1";
        }
    }

    if (currentMileageInput && intervalInput) {
        currentMileageInput.addEventListener('input', calculateNextService);
        intervalInput.addEventListener('input', calculateNextService);
        calculateNextService();
    }

    const fileInput = document.getElementById('image-upload');
    const previewGrid = document.getElementById('preview-grid');
    const deleteModal = document.getElementById('deleteModal');
    const confirmBtn = document.getElementById('confirmDelete');
    const cancelBtn = document.getElementById('cancelDelete');
    
    let tempElementToDelete = null;

    const toggleModal = (show) => {
        if (deleteModal) deleteModal.style.display = show ? 'flex' : 'none';
    };

    if (cancelBtn) cancelBtn.onclick = () => toggleModal(false);

    if (confirmBtn) {
        confirmBtn.onclick = () => {
            if (tempElementToDelete) {
                tempElementToDelete.remove();
                tempElementToDelete = null;
            }
            toggleModal(false);
        };
    }

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const files = Array.from(this.files);

            files.forEach((file) => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.createElement('div');
                    container.classList.add('preview-item');

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.onclick = () => openViewer(e.target.result); 

                    const deleteBtn = document.createElement('button');
                    deleteBtn.innerText = 'Delete';
                    deleteBtn.type = 'button';
                    deleteBtn.classList.add('delete-btn-text');
                    
                    deleteBtn.onclick = function() {
                        tempElementToDelete = container;
                        toggleModal(true);
                    };

                    container.appendChild(img);
                    container.appendChild(deleteBtn);
                    previewGrid.appendChild(container);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});

function openViewer(src) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImg');
    if (modal && modalImg) {
        modal.style.display = "block";
        modalImg.src = src;
    }
}

const closeBtn = document.querySelector('.close-modal');
if (closeBtn) {
    closeBtn.onclick = function() {
        document.getElementById('imageModal').style.display = "none";
    }
}