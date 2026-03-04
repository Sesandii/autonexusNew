// AutoNexus - Rate Service Interaction
document.addEventListener("DOMContentLoaded", () => {
  
  // ========================================
  // APPOINTMENT SELECTION & AUTO-FILL
  // ========================================
  const appointmentSelect = document.getElementById("appointment");
  const serviceDetails = document.getElementById("serviceDetails");
  const ratingSection = document.getElementById("ratingSection");
  const feedbackSection = document.getElementById("feedbackSection");
  const submitSection = document.getElementById("submitSection");
  
  const vehicleDisplay = document.getElementById("vehicleDisplay");
  const licensePlateDisplay = document.getElementById("licensePlateDisplay");
  const serviceDisplay = document.getElementById("serviceDisplay");
  const dateDisplay = document.getElementById("dateDisplay");

  if (appointmentSelect) {
    appointmentSelect.addEventListener("change", (e) => {
      const selectedOption = e.target.options[e.target.selectedIndex];
      
      if (selectedOption.value) {
        // Get data attributes
        const vehicle = selectedOption.dataset.model || "N/A";
        const licensePlate = selectedOption.dataset.vehicle || "N/A";
        const service = selectedOption.dataset.service || "N/A";
        const date = selectedOption.dataset.date || "";
        const time = selectedOption.dataset.time || "";
        
        // Format date
        let formattedDate = date;
        if (date) {
          const dateObj = new Date(date);
          formattedDate = dateObj.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
          });
          if (time) {
            formattedDate += ` at ${time}`;
          }
        }
        
        // Update displays
        vehicleDisplay.textContent = vehicle;
        licensePlateDisplay.textContent = licensePlate;
        serviceDisplay.textContent = service;
        dateDisplay.textContent = formattedDate;
        
        // Show sections progressively with smooth animation
        showSection(serviceDetails);
        setTimeout(() => showSection(ratingSection), 200);
        setTimeout(() => showSection(feedbackSection), 400);
        setTimeout(() => showSection(submitSection), 600);
      } else {
        // Hide all sections if no selection
        hideSection(serviceDetails);
        hideSection(ratingSection);
        hideSection(feedbackSection);
        hideSection(submitSection);
      }
    });
  }

  function showSection(element) {
    if (element) {
      element.style.display = "block";
      element.style.opacity = "0";
      element.style.transform = "translateY(10px)";
      
      setTimeout(() => {
        element.style.transition = "all 0.4s ease";
        element.style.opacity = "1";
        element.style.transform = "translateY(0)";
      }, 10);
    }
  }

  function hideSection(element) {
    if (element) {
      element.style.display = "none";
    }
  }

  // ========================================
  // STAR RATING SYSTEM
  // ========================================
  const starsContainer = document.getElementById("ratingStars");
  const ratingInput = document.getElementById("ratingInput");
  const ratingText = document.getElementById("ratingText");
  const ratingError = document.getElementById("ratingError");

  const ratingLabels = {
    0: "Select a rating",
    1: "⭐ Poor - Very dissatisfied",
    2: "⭐⭐ Fair - Could be better",
    3: "⭐⭐⭐ Good - Satisfied",
    4: "⭐⭐⭐⭐ Very Good - Highly satisfied",
    5: "⭐⭐⭐⭐⭐ Excellent - Extremely satisfied"
  };

  if (starsContainer && ratingInput) {
    // Create 5 stars
    for (let i = 1; i <= 5; i++) {
      const star = document.createElement("i");
      star.classList.add("fa-solid", "fa-star");
      star.dataset.value = String(i);
      starsContainer.appendChild(star);
    }

    const stars = Array.from(starsContainer.querySelectorAll("i"));
    let currentRating = Number(ratingInput.value) || 0;

    // Helper: color stars up to "rating"
    function paintStars(rating) {
      stars.forEach((star) => {
        const value = Number(star.dataset.value);
        star.classList.toggle("active", value <= rating);
      });
      
      // Update text
      if (ratingText) {
        ratingText.textContent = ratingLabels[rating] || "Select a rating";
        if (rating > 0) {
          ratingText.classList.add("selected");
        } else {
          ratingText.classList.remove("selected");
        }
      }
    }

    // Initial paint
    paintStars(currentRating);

    // Hover preview
    stars.forEach((star) => {
      star.addEventListener("mouseenter", () => {
        const hoverValue = Number(star.dataset.value);
        paintStars(hoverValue);
      });

      star.addEventListener("click", () => {
        currentRating = Number(star.dataset.value);
        ratingInput.value = String(currentRating);
        paintStars(currentRating);
        
        // Clear validation error
        if (ratingError) {
          ratingError.classList.remove("show");
        }
      });
    });

    // When mouse leaves → restore saved rating
    starsContainer.addEventListener("mouseleave", () => {
      paintStars(currentRating);
    });
  }

  // ========================================
  // CHARACTER COUNTER
  // ========================================
  const feedbackTextarea = document.getElementById("feedback");
  const charCount = document.getElementById("charCount");
  const charCounter = document.querySelector(".char-counter");

  if (feedbackTextarea && charCount) {
    feedbackTextarea.addEventListener("input", () => {
      const length = feedbackTextarea.value.length;
      charCount.textContent = length;
      
      // Warn when approaching limit
      if (length > 450 && charCounter) {
        charCounter.classList.add("warning");
      } else if (charCounter) {
        charCounter.classList.remove("warning");
      }
      
      // Enforce max length
      if (length > 500) {
        feedbackTextarea.value = feedbackTextarea.value.substring(0, 500);
        charCount.textContent = "500";
      }
    });
  }

  // ========================================
  // FORM VALIDATION
  // ========================================
  const form = document.getElementById("ratingForm");
  const submitBtn = document.getElementById("submitBtn");

  if (form) {
    form.addEventListener("submit", (e) => {
      let isValid = true;

      // Check if appointment is selected
      if (!appointmentSelect || !appointmentSelect.value) {
        alert("Please select a service to rate.");
        e.preventDefault();
        return false;
      }

      // Check if rating is selected
      const rating = Number(ratingInput.value);
      if (rating < 1 || rating > 5) {
        if (ratingError) {
          ratingError.textContent = "Please select a rating between 1 and 5 stars.";
          ratingError.classList.add("show");
        }
        isValid = false;
      }

      if (!isValid) {
        e.preventDefault();
        
        // Scroll to rating section
        if (ratingSection) {
          ratingSection.scrollIntoView({ behavior: "smooth", block: "center" });
        }
        return false;
      }

      // Disable submit button to prevent double submission
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
      }
    });
  }

  // ========================================
  // SMOOTH SCROLL FOR VALIDATION ERRORS
  // ========================================
  if (ratingError && ratingError.textContent) {
    ratingError.classList.add("show");
    if (ratingSection) {
      setTimeout(() => {
        ratingSection.scrollIntoView({ behavior: "smooth", block: "center" });
      }, 100);
    }
  }
});
