// AutoNexus Login Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
  // Get form elements
  const loginForm = document.getElementById('loginForm');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const forgotPasswordLink = document.querySelector('.forgot-password');
  const signupLink = document.querySelector('.signup-btn');

  const togglePasswordBtn = document.getElementById('togglePassword');

  if (togglePasswordBtn && passwordInput) {
      togglePasswordBtn.addEventListener('click', () => {
          const isHidden = passwordInput.type === 'password';

          passwordInput.type = isHidden ? 'text' : 'password';

          togglePasswordBtn.classList.toggle('showing', isHidden);

          togglePasswordBtn.setAttribute(
              'aria-label',
              isHidden ? 'Hide password' : 'Show password'
          );
      });
  }


  // Validation helpers
  function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }
  function validatePassword(password) {
    return password.length >= 6; // client-side minimum; server enforces real rules
  }
  function showValidationError(input, message) {
    const existingError = input.parentNode.querySelector('.error-message');
    if (existingError) existingError.remove();

    const errorElement = document.createElement('div');
    errorElement.className = 'error-message';
    errorElement.textContent = message;
    errorElement.style.cssText = `
      color: #e53e3e;
      font-size: 0.875rem;
      margin-top: 0.25rem;
      animation: fadeIn 0.3s ease;
    `;
    input.parentNode.appendChild(errorElement);
    input.style.borderColor = '#e53e3e';
  }
  function clearValidationError(input) {
    const existingError = input.parentNode.querySelector('.error-message');
    if (existingError) existingError.remove();
    input.style.borderColor = '#e2e8f0';
  }

  // Real-time validation
  emailInput.addEventListener('blur', function() {
    if (this.value && !validateEmail(this.value)) {
      showValidationError(this, 'Please enter a valid email address');
    } else {
      clearValidationError(this);
    }
  });
  passwordInput.addEventListener('blur', function() {
    if (this.value && !validatePassword(this.value)) {
      showValidationError(this, 'Password must be at least 6 characters long');
    } else {
      clearValidationError(this);
    }
  });
  emailInput.addEventListener('input', function() {
    if (this.parentNode.querySelector('.error-message')) clearValidationError(this);
  });
  passwordInput.addEventListener('input', function() {
    if (this.parentNode.querySelector('.error-message')) clearValidationError(this);
  });

  // Form submission 

  loginForm.addEventListener('submit', function(e) {

    clearValidationError(emailInput);
    clearValidationError(passwordInput);

    const email = emailInput.value.trim();
    const password = passwordInput.value;
    let isValid = true;

    // Validate email
    if (!email) {
      showValidationError(emailInput, 'Email is required');
      isValid = false;
    } else if (!validateEmail(email)) {
      showValidationError(emailInput, 'Please enter a valid email address');
      isValid = false;
    }

    // Validate password
    if (!password) {
      showValidationError(passwordInput, 'Password is required');
      isValid = false;
    } else if (!validatePassword(password)) {
      showValidationError(passwordInput, 'Password must be at least 6 characters long');
      isValid = false;
    }

    // If invalid, stop the native submit; otherwise let it POST to db_login.php
    if (!isValid) {
      e.preventDefault();
    }
  });

  // Forgot password
  if (forgotPasswordLink) {
    forgotPasswordLink.addEventListener('click', function(e) {
      e.preventDefault();
      const email = emailInput.value.trim();

      if (!email) {
        showMessage('Please enter your email address first', 'error');
        emailInput.focus();
        return;
      }
      if (!validateEmail(email)) {
        showMessage('Please enter a valid email address', 'error');
        emailInput.focus();
        return;
      }
      // Simulate password reset action
      showMessage(`Password reset link sent to ${email}`, 'success');
    });
  }




  function showMessage(message, type) {
    const existingMessage = document.querySelector('.form-message');
    if (existingMessage) existingMessage.remove();

    const messageElement = document.createElement('div');
    messageElement.className = 'form-message';
    messageElement.textContent = message;

    const isSuccess = type === 'success';
    messageElement.style.cssText = `
      padding: 0.75rem 1rem;
      margin-bottom: 1rem;
      border-radius: 8px;
      font-size: 0.875rem;
      font-weight: 500;
      text-align: center;
      background-color: ${isSuccess ? '#f0fff4' : '#fed7d7'};
      color: ${isSuccess ? '#22543d' : '#c53030'};
      border: 1px solid ${isSuccess ? '#9ae6b4' : '#feb2b2'};
      animation: slideDown 0.3s ease;
    `;
    loginForm.insertBefore(messageElement, loginForm.firstChild);

    setTimeout(() => {
      if (messageElement.parentNode) messageElement.remove();
    }, 5000);
  }

  // --- Image loading error handling (unchanged) ---
  const images = document.querySelectorAll('img');
  images.forEach(img => {
    img.addEventListener('error', function() {
      console.warn(`Failed to load image: ${this.src}`);
      if (this.alt.includes('Logo')) {
        this.style.display = 'none';
        const textLogo = document.createElement('div');
        textLogo.innerHTML = '<span style="color: #e53e3e; font-weight: bold; font-size: 18px; border: 2px solid #e53e3e; padding: 8px 12px; border-radius: 8px;">AutoNexus</span>';
        this.parentNode.insertBefore(textLogo, this);
      } else if (this.alt.includes('Car')) {
        this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAwIiBoZWlnaHQ9IjYwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjY2NjIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIyNCIgZmlsbD0iIzY2NiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkNhciBJbWFnZSBQbGFjZWhvbGRlcjwvdGV4dD48L3N2Zz4=';
      }
    });
  });

  // --- Animations & UX niceties  ---
  const style = document.createElement('style');
  style.textContent = `
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  `;
  document.head.appendChild(style);

  [emailInput, passwordInput].forEach(input => {
    input.addEventListener('focus', function() {
      this.style.transition = 'all 0.2s ease';
    });
  });

  // Press Enter anywhere to submit the form natively
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && document.activeElement.tagName !== 'BUTTON') {
      // Let the native submit happen via the form's default behavior
      // but make sure validation runs by manually dispatching submit:
      loginForm.dispatchEvent(new Event('submit', { cancelable: true }));
    }
  });

  console.log('🚗 AutoNexus Login Page initialized (native submit).');
});
