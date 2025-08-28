<?php
// Signup processing will be implemented later
// For now, just redirect back to signin page with a message

if ($_POST) {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        header('Location: signin.html?error=Please fill all fields');
        exit;
    }
    
    if ($password !== $confirm_password) {
        header('Location: signin.html?error=Passwords do not match');
        exit;
    }
    
    // TODO: Add database validation and user creation
    // For now, just show success message
    header('Location: signin.html?success=Account created successfully! Please sign in.');
    exit;
} else {
    header('Location: signin.html');
    exit;
}
?>
