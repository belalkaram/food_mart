// Simple client-side validation for login form

document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('.glass-card');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');

  form.addEventListener('submit', function (e) {
    let valid = true;
    let messages = [];

    // Email validation
    const emailValue = emailInput.value.trim();
    if (!emailValue) {
      valid = false;
      messages.push('Email is required.');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
      valid = false;
      messages.push('Please enter a valid email address.');
    }

    // Password validation
    const passwordValue = passwordInput.value;
    if (!passwordValue) {
      valid = false;
      messages.push('Password is required.');
    } else if (passwordValue.length < 6) {
      valid = false;
      messages.push('Password must be at least 6 characters.');
    }

        // Show errors if not valid
        if (!valid) {
          e.preventDefault();
          alert(messages.join('\n'));
        }
      });
    });