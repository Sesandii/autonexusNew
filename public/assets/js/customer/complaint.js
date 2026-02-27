/**
 * AutoNexus – Customer Complaint Form JavaScript
 * ===============================================
 * 
 * FILE LOCATION: public/assets/js/customer/complaint.js
 * LOADED IN VIEW: app/views/customer/complaint/index.php
 * 
 * Handles:
 * - Auto-filling vehicle details when vehicle is selected
 * - Form validation
 * - User interaction enhancements
 */

document.addEventListener('DOMContentLoaded', function() {
  
  // =============================
  // VEHICLE SELECTION HANDLER
  // =============================
  const vehicleSelect = document.getElementById('vehicle');
  const vehicleNumberInput = document.getElementById('vehicleNumber');
  const vehicleModelInput = document.getElementById('vehicleModel');
  
  if (vehicleSelect) {
    vehicleSelect.addEventListener('change', function() {
      const selectedOption = this.options[this.selectedIndex];
      
      if (selectedOption && selectedOption.value) {
        // Extract data attributes
        const vehicleNumber = selectedOption.getAttribute('data-number') || '';
        const vehicleModel = selectedOption.getAttribute('data-model') || '';
        
        // Fill in the readonly fields
        vehicleNumberInput.value = vehicleNumber;
        vehicleModelInput.value = vehicleModel;
      } else {
        // Clear fields if no vehicle selected
        vehicleNumberInput.value = '';
        vehicleModelInput.value = '';
        vehicleNumberInput.placeholder = 'Select a vehicle above';
        vehicleModelInput.placeholder = 'Select a vehicle above';
      }
    });
  }
  
  // =============================
  // FORM VALIDATION
  // =============================
  const complaintForm = document.querySelector('.form-container');
  
  if (complaintForm) {
    complaintForm.addEventListener('submit', function(e) {
      const description = document.getElementById('description');
      
      // Check minimum length for description
      if (description && description.value.trim().length < 20) {
        e.preventDefault();
        alert('Please provide a detailed complaint description (minimum 20 characters).');
        description.focus();
        return false;
      }
      
      // Check if vehicle is selected
      if (vehicleSelect && !vehicleSelect.value) {
        e.preventDefault();
        alert('Please select a vehicle for this complaint.');
        vehicleSelect.focus();
        return false;
      }
      
      // Check if date is not in the future
      const incidentDate = document.getElementById('incidentDate');
      if (incidentDate && incidentDate.value) {
        const selectedDate = new Date(incidentDate.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate > today) {
          e.preventDefault();
          alert('Incident date cannot be in the future.');
          incidentDate.focus();
          return false;
        }
      }
      
      // All validations passed
      return true;
    });
  }
  
  // =============================
  // CHARACTER COUNTER FOR TEXTAREA
  // =============================
  const descriptionTextarea = document.getElementById('description');
  
  if (descriptionTextarea) {
    // Create counter element
    const counterDiv = document.createElement('span');
    counterDiv.className = 'form-hint char-counter';
    counterDiv.style.display = 'block';
    counterDiv.style.marginTop = '4px';
    
    // Insert after textarea
    descriptionTextarea.parentNode.appendChild(counterDiv);
    
    // Update counter
    function updateCounter() {
      const length = descriptionTextarea.value.length;
      const minLength = 20;
      
      if (length < minLength) {
        counterDiv.textContent = `${length} / ${minLength} characters (minimum)`;
        counterDiv.style.color = '#ef4444';
      } else {
        counterDiv.textContent = `${length} characters`;
        counterDiv.style.color = '#10b981';
      }
    }
    
    // Listen to input events
    descriptionTextarea.addEventListener('input', updateCounter);
    descriptionTextarea.addEventListener('change', updateCounter);
    
    // Initial update
    updateCounter();
  }
  
  // =============================
  // DATE PICKER MAX DATE
  // =============================
  const incidentDateInput = document.getElementById('incidentDate');
  
  if (incidentDateInput) {
    // Set default to today if empty
    if (!incidentDateInput.value) {
      const today = new Date().toISOString().split('T')[0];
      incidentDateInput.value = today;
    }
  }
  
  // =============================
  // AUTO-FOCUS FIRST EMPTY FIELD
  // =============================
  const firstSelect = document.querySelector('select:not([disabled])');
  if (firstSelect && !firstSelect.value) {
    firstSelect.focus();
  }
  
});
