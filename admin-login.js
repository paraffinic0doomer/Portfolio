// Admin Login JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const formTitle = document.getElementById('formTitle');
    const formSubtitle = document.getElementById('formSubtitle');
    const toggleLink = document.getElementById('toggleLink');
    const toggleText = document.getElementById('toggleText');
    
    // Login form elements
    const loginBtn = document.getElementById('loginBtn');
    const loginBtnText = loginBtn.querySelector('.btn-text');
    const loginLoadingSpinner = loginBtn.querySelector('.loading-spinner');
    const loginErrorMessage = document.getElementById('loginErrorMessage');
    const loginUsernameInput = document.getElementById('loginUsername');
    const loginPasswordInput = document.getElementById('loginPassword');
    const rememberCheckbox = document.getElementById('remember');
    
    // Signup form elements
    const signupBtn = document.getElementById('signupBtn');
    const signupBtnText = signupBtn.querySelector('.btn-text');
    const signupLoadingSpinner = signupBtn.querySelector('.loading-spinner');
    const signupErrorMessage = document.getElementById('signupErrorMessage');
    const signupUsernameInput = document.getElementById('signupUsername');
    const signupEmailInput = document.getElementById('signupEmail');
    const signupPasswordInput = document.getElementById('signupPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const agreeTermsCheckbox = document.getElementById('agreeTerms');

    let isLoginMode = true;

    // Toggle between login and signup forms
    function toggleForms() {
        isLoginMode = !isLoginMode;
        
        if (isLoginMode) {
            // Switch to login mode
            signupForm.classList.remove('active');
            setTimeout(() => {
                loginForm.classList.add('active');
                formTitle.textContent = 'Admin Portal';
                formSubtitle.textContent = 'Sign in to manage your portfolio';
                toggleText.innerHTML = 'Don\'t have an account? <a href="#" id="toggleLink">Sign up</a>';
            }, 200);
        } else {
            // Switch to signup mode
            loginForm.classList.remove('active');
            setTimeout(() => {
                signupForm.classList.add('active');
                formTitle.textContent = 'Create Account';
                formSubtitle.textContent = 'Join to manage your portfolio';
                toggleText.innerHTML = 'Already have an account? <a href="#" id="toggleLink">Sign in</a>';
            }, 200);
        }
        
        // Hide any error messages
        hideError(loginErrorMessage);
        hideError(signupErrorMessage);
        
        // Reset forms
        loginForm.reset();
        signupForm.reset();
        
        // Re-attach toggle event listener
        setTimeout(() => {
            document.getElementById('toggleLink').addEventListener('click', function(e) {
                e.preventDefault();
                toggleForms();
            });
        }, 250);
    }

    // Initial toggle event listener
    toggleLink.addEventListener('click', function(e) {
        e.preventDefault();
        toggleForms();
    });

    // Form validation for login
    function validateLoginForm() {
        const username = loginUsernameInput.value.trim();
        const password = loginPasswordInput.value.trim();
        
        if (username === '') {
            showError(loginErrorMessage, 'Please enter your username');
            loginUsernameInput.focus();
            return false;
        }
        
        if (password === '') {
            showError(loginErrorMessage, 'Please enter your password');
            loginPasswordInput.focus();
            return false;
        }
        
        if (password.length < 6) {
            showError(loginErrorMessage, 'Password must be at least 6 characters long');
            loginPasswordInput.focus();
            return false;
        }
        
        return true;
    }

    // Form validation for signup
    function validateSignupForm() {
        const username = signupUsernameInput.value.trim();
        const email = signupEmailInput.value.trim();
        const password = signupPasswordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();
        
        if (username === '') {
            showError(signupErrorMessage, 'Please enter a username');
            signupUsernameInput.focus();
            return false;
        }
        
        if (username.length < 3) {
            showError(signupErrorMessage, 'Username must be at least 3 characters long');
            signupUsernameInput.focus();
            return false;
        }
        
        if (email === '') {
            showError(signupErrorMessage, 'Please enter your email');
            signupEmailInput.focus();
            return false;
        }
        
        if (!isValidEmail(email)) {
            showError(signupErrorMessage, 'Please enter a valid email address');
            signupEmailInput.focus();
            return false;
        }
        
        if (password === '') {
            showError(signupErrorMessage, 'Please enter a password');
            signupPasswordInput.focus();
            return false;
        }
        
        if (password.length < 8) {
            showError(signupErrorMessage, 'Password must be at least 8 characters long');
            signupPasswordInput.focus();
            return false;
        }
        
        if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
            showError(signupErrorMessage, 'Password must contain at least one uppercase letter, one lowercase letter, and one number');
            signupPasswordInput.focus();
            return false;
        }
        
        if (password !== confirmPassword) {
            showError(signupErrorMessage, 'Passwords do not match');
            confirmPasswordInput.focus();
            return false;
        }
        
        if (!agreeTermsCheckbox.checked) {
            showError(signupErrorMessage, 'Please agree to the terms and conditions');
            agreeTermsCheckbox.focus();
            return false;
        }
        
        return true;
    }

    // Email validation helper
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Show error message
    function showError(errorElement, message) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
        
        // Hide error after 5 seconds
        setTimeout(() => {
            hideError(errorElement);
        }, 5000);
    }

    // Hide error message
    function hideError(errorElement) {
        errorElement.classList.remove('show');
    }

    // Show loading state
    function showLoading(btn) {
        btn.classList.add('loading');
        btn.disabled = true;
    }

    // Hide loading state
    function hideLoading(btn) {
        btn.classList.remove('loading');
        btn.disabled = false;
    }

    // Show success state (simplified for PHP redirect)
    function showSuccess(btn, btnText, message = 'Success!') {
        btn.classList.add('success');
        btnText.textContent = message;
    }



    // Handle login form submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Hide any existing errors
        hideError(loginErrorMessage);
        
        // Validate form
        if (!validateLoginForm()) {
            return;
        }
        
        // Show loading state
        showLoading(loginBtn);
        
        // Submit form to PHP backend
        const formData = new FormData();
        formData.append('action', 'login');
        formData.append('username', loginUsernameInput.value.trim());
        formData.append('password', loginPasswordInput.value.trim());
        formData.append('remember', rememberCheckbox.checked ? '1' : '0');
        
        // Submit to PHP handler
        loginForm.action = 'auth.php';
        loginForm.method = 'POST';
        loginForm.submit();
    });

    // Handle signup form submission
    signupForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Hide any existing errors
        hideError(signupErrorMessage);
        
        // Validate form
        if (!validateSignupForm()) {
            return;
        }
        
        // Show loading state
        showLoading(signupBtn);
        
        // Submit form to PHP backend
        const formData = new FormData();
        formData.append('action', 'signup');
        formData.append('username', signupUsernameInput.value.trim());
        formData.append('email', signupEmailInput.value.trim());
        formData.append('password', signupPasswordInput.value.trim());
        formData.append('confirmPassword', confirmPasswordInput.value.trim());
        formData.append('agreeTerms', agreeTermsCheckbox.checked ? '1' : '0');
        
        // Submit to PHP handler
        signupForm.action = 'auth.php';
        signupForm.method = 'POST';
        signupForm.submit();
    });

    // Handle input focus events
    loginUsernameInput.addEventListener('focus', () => hideError(loginErrorMessage));
    loginPasswordInput.addEventListener('focus', () => hideError(loginErrorMessage));
    signupUsernameInput.addEventListener('focus', () => hideError(signupErrorMessage));
    signupEmailInput.addEventListener('focus', () => hideError(signupErrorMessage));
    signupPasswordInput.addEventListener('focus', () => hideError(signupErrorMessage));
    confirmPasswordInput.addEventListener('focus', () => hideError(signupErrorMessage));

    // Handle Enter key press for login
    loginUsernameInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            loginPasswordInput.focus();
        }
    });

    loginPasswordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            loginForm.dispatchEvent(new Event('submit'));
        }
    });

    // Handle Enter key press for signup
    signupUsernameInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            signupEmailInput.focus();
        }
    });

    signupEmailInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            signupPasswordInput.focus();
        }
    });

    signupPasswordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            confirmPasswordInput.focus();
        }
    });

    confirmPasswordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            signupForm.dispatchEvent(new Event('submit'));
        }
    });

    // Check if user should be remembered
    function checkRememberedUser() {
        const rememberMe = localStorage.getItem('adminRemember');
        const savedUsername = localStorage.getItem('adminUsername');
        
        if (rememberMe === 'true' && savedUsername) {
            loginUsernameInput.value = savedUsername;
            rememberCheckbox.checked = true;
            loginPasswordInput.focus();
        }
    }

    // Initialize remembered user check
    checkRememberedUser();

    // Handle background animations
    function initBackgroundAnimation() {
        const shapes = document.querySelectorAll('.floating-shape');
        
        shapes.forEach((shape, index) => {
            // Add random animation variations
            const randomDelay = Math.random() * 2;
            const randomDuration = 6 + Math.random() * 4;
            
            shape.style.animationDelay = `${randomDelay}s`;
            shape.style.animationDuration = `${randomDuration}s`;
            
            // Add mouse interaction
            shape.addEventListener('mouseenter', function() {
                this.style.animationPlayState = 'paused';
                this.style.transform = 'scale(1.2)';
                this.style.opacity = '0.2';
            });
            
            shape.addEventListener('mouseleave', function() {
                this.style.animationPlayState = 'running';
                this.style.transform = 'scale(1)';
                this.style.opacity = '0.1';
            });
        });
    }

    // Initialize background animation
    initBackgroundAnimation();

    // Forgot password handler (placeholder)
    const forgotLink = document.querySelector('.forgot-link');
    if (forgotLink) {
        forgotLink.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Password reset functionality will be implemented soon. Please contact the administrator.');
        });
    }

    // Add smooth animations on page load
    setTimeout(() => {
        document.querySelector('.login-card').style.opacity = '1';
        document.querySelector('.login-card').style.transform = 'translateY(0)';
    }, 100);

    // Initial setup
    document.querySelector('.login-card').style.opacity = '0';
    document.querySelector('.login-card').style.transform = 'translateY(20px)';
    document.querySelector('.login-card').style.transition = 'all 0.6s ease';
});

// Utility functions for session management (simplified for PHP backend)
const AdminAuth = {
    // Check if user is logged in (will be handled by PHP sessions)
    isLoggedIn: function() {
        // This will be replaced by PHP session checks
        return false;
    },
    
    // Get logged in user (will be handled by PHP)
    getUser: function() {
        // This will be replaced by PHP user data
        return null;
    },
    
    // Logout user (will redirect to PHP logout handler)
    logout: function() {
        window.location.href = 'logout.php';
    },
    
    // Check authentication (will be handled by PHP)
    checkAuth: function() {
        // This will be replaced by PHP authentication checks
        return false;
    }
};

// Export for use in other admin pages
window.AdminAuth = AdminAuth;
