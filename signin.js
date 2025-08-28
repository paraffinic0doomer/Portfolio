document.addEventListener('DOMContentLoaded', function() {
    const signinToggle = document.getElementById('signin-toggle');
    const signupToggle = document.getElementById('signup-toggle');
    const signinForm = document.getElementById('signin-form');
    const signupForm = document.getElementById('signup-form');
    const formTitle = document.getElementById('form-title');
    const formSubtitle = document.getElementById('form-subtitle');

    // Check for URL parameters (success/error messages)
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const success = urlParams.get('success');
    
    if (error) {
        showMessage(error, 'error');
    } else if (success) {
        showMessage(success, 'success');
    }

    // Function to show messages
    function showMessage(message, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.textContent = message;
        messageDiv.style.cssText = `
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: 6px;
            font-size: 14px;
            ${type === 'error' ? 'background: #fed7d7; color: #c53030; border: 1px solid #feb2b2;' : 'background: #c6f6d5; color: #2f855a; border: 1px solid #9ae6b4;'}
        `;
        
        const card = document.querySelector('.signin-card');
        const header = document.querySelector('.signin-header');
        card.insertBefore(messageDiv, header.nextSibling);
        
        // Auto-remove message after 5 seconds
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }

    // Switch to Sign In
    signinToggle.addEventListener('click', function() {
        signinToggle.classList.add('active');
        signupToggle.classList.remove('active');
        signinForm.style.display = 'block';
        signupForm.style.display = 'none';
        formTitle.textContent = 'Admin Panel';
        formSubtitle.textContent = 'Sign in to manage your portfolio';
    });

    // Switch to Sign Up
    signupToggle.addEventListener('click', function() {
        signupToggle.classList.add('active');
        signinToggle.classList.remove('active');
        signupForm.style.display = 'block';
        signinForm.style.display = 'none';
        formTitle.textContent = 'Create Account';
        formSubtitle.textContent = 'Sign up to access admin panel';
    });

    // Email verification functionality
    const sendCodeBtn = document.getElementById('send-code-btn');
    const verificationCodeGroup = document.getElementById('verification-code-group');
    const verificationCodeInput = document.getElementById('verification-code');
    const signupSubmitBtn = document.getElementById('signup-submit-btn');
    const signupEmail = document.getElementById('signup-email');
    
    // Handle Send Code button click
    sendCodeBtn.addEventListener('click', function() {
        const email = signupEmail.value.trim();
        
        if (!email) {
            showMessage('Please enter your email address first!', 'error');
            signupEmail.focus();
            return;
        }
        
        if (!email.includes('@')) {
            showMessage('Please enter a valid email address!', 'error');
            signupEmail.focus();
            return;
        }
        
        // Simulate sending code (in real implementation, this would call backend)
        sendCodeBtn.textContent = 'Sending...';
        sendCodeBtn.disabled = true;
        
        setTimeout(() => {
            verificationCodeGroup.style.display = 'block';
            sendCodeBtn.textContent = 'Code Sent';
            showMessage('Verification code sent to your email!', 'success');
            verificationCodeInput.focus();
        }, 1500);
    });
    
    // Handle verification code input
    verificationCodeInput.addEventListener('input', function() {
        const code = this.value.trim();
        
        // Enable submit button when 6-digit code is entered
        if (code.length === 6) {
            signupSubmitBtn.disabled = false;
        } else {
            signupSubmitBtn.disabled = true;
        }
    });

    // Password confirmation validation for signup
    const signupPassword = document.getElementById('signup-password');
    const confirmPassword = document.getElementById('signup-confirm-password');
    
    signupForm.addEventListener('submit', function(e) {
        let hasError = false;
        
        // Check if passwords match
        if (signupPassword.value !== confirmPassword.value) {
            e.preventDefault();
            showMessage('Passwords do not match!', 'error');
            confirmPassword.focus();
            hasError = true;
        }
        
        // Check if verification code is entered
        if (!hasError && verificationCodeInput.value.trim().length !== 6) {
            e.preventDefault();
            showMessage('Please enter the 6-digit verification code!', 'error');
            verificationCodeInput.focus();
            hasError = true;
        }
    });
});
