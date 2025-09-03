<?php
session_start();
require_once 'config/database.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'login') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']) ? true : false;
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Please fill in all fields';
            header('Location: admin.html?error=missing_fields');
            exit();
        }
        
        try {
            // Prepare query to find user
            $database->query('SELECT * FROM users WHERE username = :username OR email = :username');
            $database->bind(':username', $username);
            $user = $database->single();
            
            if ($user && password_verify($password, $user->password)) {
                // Login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $user->id;
                $_SESSION['admin_username'] = $user->username;
                $_SESSION['admin_role'] = $user->role;
                
                // Set remember me cookie if requested
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 days
                    
                    // Store token in database (you might want to create a remember_tokens table)
                    $database->query('UPDATE users SET remember_token = :token WHERE id = :id');
                    $database->bind(':token', hash('sha256', $token));
                    $database->bind(':id', $user->id);
                    $database->execute();
                }
                
                // Redirect to dashboard
                header('Location: admin-dashboard.php');
                exit();
            } else {
                $_SESSION['error'] = 'Invalid username or password';
                header('Location: admin.html?error=invalid_credentials');
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Database error occurred';
            header('Location: admin.html?error=database_error');
            exit();
        }
        
    } elseif ($action == 'signup') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
        $agreeTerms = isset($_POST['agreeTerms']) ? true : false;
        
        // Validation
        $errors = [];
        
        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters long';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }
        
        if (empty($password) || strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter, one lowercase letter, and one number';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        if (!$agreeTerms) {
            $errors[] = 'You must agree to the terms and conditions';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('. ', $errors);
            header('Location: admin.html?error=validation_failed');
            exit();
        }
        
        try {
            // Check if username or email already exists
            $database->query('SELECT id FROM users WHERE username = :username OR email = :email');
            $database->bind(':username', $username);
            $database->bind(':email', $email);
            $existingUser = $database->single();
            
            if ($existingUser) {
                $_SESSION['error'] = 'Username or email already exists';
                header('Location: admin.html?error=user_exists');
                exit();
            }
            
            // Hash password and create user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $database->query('INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)');
            $database->bind(':username', $username);
            $database->bind(':email', $email);
            $database->bind(':password', $hashedPassword);
            $database->bind(':role', 'admin'); // Default to admin for portfolio
            
            if ($database->execute()) {
                // Auto-login after successful registration
                $userId = $database->lastInsertId();
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $userId;
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_role'] = 'admin';
                
                // Redirect to dashboard
                header('Location: admin-dashboard.php');
                exit();
            } else {
                $_SESSION['error'] = 'Registration failed. Please try again.';
                header('Location: admin.html?error=registration_failed');
                exit();
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Database error occurred during registration';
            header('Location: admin.html?error=database_error');
            exit();
        }
    }
}

// Handle GET requests for logout
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'logout') {
    // Destroy session
    session_destroy();
    
    // Clear remember me cookie if it exists
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    // Redirect to login page
    header('Location: admin.html?message=logged_out');
    exit();
}

// If not POST request, redirect to login
header('Location: admin.html');
exit();
?>
