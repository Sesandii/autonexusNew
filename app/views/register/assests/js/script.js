/* AutoNexus - Register form client-side validation */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form.form');
    if (!form) return;

    // Keep our own messages; disable native bubbles
    form.setAttribute('novalidate', 'novalidate');

    // Minimal styles for error messages (inject once)
    if (!document.getElementById('reg-validate-style')) {
      const style = document.createElement('style');
      style.id = 'reg-validate-style';
      style.textContent = `
        .field.invalid input { border-color: #e53e3e !important; }
        .field.valid   input { border-color: #38a169 !important; }
        .error-text {
          color:#e53e3e; font-size:.85rem; margin-top:.35rem;
          line-height:1.2; animation: fadeIn .15s ease;
        }
        @keyframes fadeIn { from{opacity:0;transform:translateY(-2px)} to{opacity:1;transform:none} }
      `;
      document.head.appendChild(style);
    }

    // Inputs
    const els = {
      first_name: form.querySelector('input[name="first_name"]'),
      last_name: form.querySelector('input[name="last_name"]'),
      email: form.querySelector('input[name="email"]'),
      phone: form.querySelector('input[name="phone"]'),
      alt_phone: form.querySelector('input[name="alt_phone"]'),
      street: form.querySelector('input[name="street"]'),
      city: form.querySelector('input[name="city"]'),
      state: form.querySelector('input[name="state"]'),
      username: form.querySelector('input[name="username"]'),
      password: form.querySelector('input[name="password"]'),
      confirm_password: form.querySelector('input[name="confirm_password"]')
    };

    // Helpers
    function showError(input, msg) {
      const field = input.closest('.field') || input.parentElement;
      field.classList.add('invalid');
      field.classList.remove('valid');

      let err = field.querySelector('.error-text');
      if (!err) {
        err = document.createElement('div');
        err.className = 'error-text';
        field.appendChild(err);
      }
      err.textContent = msg;
    }

    function clearError(input) {
      const field = input.closest('.field') || input.parentElement;
      field.classList.remove('invalid');
      const err = field.querySelector('.error-text');
      if (err) err.remove();

      const required = input.hasAttribute('required');
      if ((required && input.value.trim() !== '') || !required) {
        field.classList.add('valid');
      } else {
        field.classList.remove('valid');
      }
    }

    // Rules
    const nameRe = /^[A-Za-z][A-Za-z\s'.-]{1,49}$/; // 2–50, letters + space/.'-
    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    const phoneRe = /^[+()\s\-.\d]{7,20}$/;        // digits + common symbols
    const usernameRe = /^[A-Za-z0-9_.]{3,30}$/;

    function vFirstName() {
      const v = els.first_name.value.trim();
      if (!v) return 'First name is required';
      if (!nameRe.test(v)) return 'Use 2–50 letters (A–Z), spaces, (.\'-)';
      return '';
    }

    function vLastName() {
      const v = els.last_name.value.trim();
      if (!v) return 'Last name is required';
      if (!nameRe.test(v)) return 'Use 2–50 letters (A–Z), spaces, (.\'-)';
      return '';
    }

    function vEmail() {
      const v = els.email.value.trim();
      if (!v) return 'Email is required';
      if (v.length > 254) return 'Email is too long';
      if (!emailRe.test(v)) return 'Enter a valid email address';
      return '';
    }

    function vPhone() {
        const v = els.phone.value.trim();
        if (!v) return 'Phone number is required';
        if (!/^\d{10}$/.test(v)) return 'Phone number must be exactly 10 digits';
        return '';
    }

    function vAltPhone() {
      const v = els.alt_phone.value.trim();
      if (!v) return '';
      if (!phoneRe.test(v)) return 'Alternate phone is invalid';
      return '';
    }

    function vStreet() {
      const v = els.street.value.trim();
      if (v.length > 120) return 'Street is too long (max 120)';
      return '';
    }

    function vCity() {
      const v = els.city.value.trim();
      if (v.length > 100) return 'City is too long (max 100)';
      return '';
    }

    function vState() {
      const v = els.state.value.trim();
      if (v.length > 100) return 'State is too long (max 100)';
      return '';
    }

    function vUsername() {
      const v = els.username.value.trim();
      if (!v) return ''; // optional
      if (!usernameRe.test(v)) return '3–30 chars: letters, numbers, _ or .';
      return '';
    }

    function vPassword() {
      const v = els.password.value;
      if (!v) return 'Password is required';
      if (v.length < 6) return 'Use at least 6 characters';
      const lacks = [];
      if (!/[a-z]/.test(v)) lacks.push('lowercase');
      if (!/[A-Z]/.test(v)) lacks.push('uppercase');
      if (!/\d/.test(v))     lacks.push('number');
      if (!/[^A-Za-z0-9]/.test(v)) lacks.push('symbol');
      if (lacks.length) return 'Include ' + lacks.join(', ');
      return '';
    }

    function vConfirm() {
      const a = els.password.value;
      const b = els.confirm_password.value;
      if (!b) return 'Please confirm your password';
      if (a !== b) return 'Passwords do not match';
      return '';
    }

    const validators = [
      [els.first_name, vFirstName],
      [els.last_name,  vLastName],
      [els.email,      vEmail],
      [els.phone,      vPhone],
      [els.alt_phone,  vAltPhone],
      [els.street,     vStreet],
      [els.city,       vCity],
      [els.state,      vState],
      [els.username,   vUsername],
      [els.password,   vPassword],
      [els.confirm_password, vConfirm],
    ];

    // Live validation
    validators.forEach(([input, fn]) => {
      if (!input) return;

      input.addEventListener('blur', () => {
        const msg = fn();
        if (msg) showError(input, msg);
        else clearError(input);
      });

      input.addEventListener('input', () => {
        if (input === els.password || input === els.confirm_password) {
          const msg = (input === els.password ? vPassword() : vConfirm());
          if (msg) showError(input, msg); else clearError(input);
        } else {
          clearError(input);
        }
      });
    });

    // Submit gate
    form.addEventListener('submit', function (e) {
      let firstInvalid = null;

      validators.forEach(([input, fn]) => {
        if (!input) return;
        const msg = fn();
        if (msg) {
          showError(input, msg);
          if (!firstInvalid) firstInvalid = input;
        } else {
          clearError(input);
        }
      });

      // Trim common text fields before submit
      ['first_name','last_name','email','phone','alt_phone','street','city','state','username']
        .forEach((n) => { if (els[n]) els[n].value = els[n].value.trim(); });

      if (firstInvalid) {
        e.preventDefault();
        firstInvalid.focus();
      }
    });
  });
})();
