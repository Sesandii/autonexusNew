document.addEventListener('DOMContentLoaded', function() {
  const fileInput = document.getElementById('image-upload');
  const previewGrid = document.getElementById('preview-grid');
  
  // Modal Elements
  const deleteModal = document.getElementById('deleteModal');
  const confirmBtn = document.getElementById('confirmDelete');
  const cancelBtn = document.getElementById('cancelDelete');
  
  let tempElementToDelete = null; // Variable to store the element temporarily

  // Function to show/hide modal
  const toggleModal = (show) => {
      deleteModal.style.display = show ? 'flex' : 'none';
  };

  // When "Cancel" is clicked
  cancelBtn.onclick = () => toggleModal(false);

  // When "Confirm Delete" is clicked
  confirmBtn.onclick = () => {
      if (tempElementToDelete) {
          tempElementToDelete.remove();
          tempElementToDelete = null;
      }
      toggleModal(false);
  };

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
              img.onclick = () => openViewer(e.target.result); // Your existing viewer function

              const deleteBtn = document.createElement('button');
              deleteBtn.innerText = 'Delete';
              deleteBtn.type = 'button';
              deleteBtn.classList.add('delete-btn-text');
              
              // INSTEAD OF REMOVING, OPEN THE MODAL
              deleteBtn.onclick = function() {
                  tempElementToDelete = container; // Remember this container
                  toggleModal(true);               // Show confirmation
              };

              container.appendChild(img);
              container.appendChild(deleteBtn);
              previewGrid.appendChild(container);
          };
          reader.readAsDataURL(file);
      });
  });
});

function saveDraft() {
  alert("Draft saved successfully!");
}

function submitReport() {
  alert("Final report submitted successfully!");
}