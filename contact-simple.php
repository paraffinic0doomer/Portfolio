<?php
// Contact Form Handler - Database Version
// This version saves messages to the database

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
require_once 'config/database.php';

// Configuration
$RECIPIENT_EMAIL = "studyhardufkinmoron@gmail.com";

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Only POST requests are allowed.';
    echo json_encode($response);
    exit;
}

// Get and sanitize form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validation
if (empty($name)) {
    $response['errors'][] = 'Name is required.';
}

if (empty($email)) {
    $response['errors'][] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['errors'][] = 'Please enter a valid email address.';
}

if (empty($subject)) {
    $response['errors'][] = 'Subject is required.';
}

if (empty($message)) {
    $response['errors'][] = 'Message is required.';
}

// If there are validation errors, return them
if (!empty($response['errors'])) {
    $response['message'] = 'Please fix the following errors:';
    echo json_encode($response);
    exit;
}

// Save message to database
try {
    $database = new Database();
    
    $sql = "INSERT INTO contact_messages (name, email, subject, message, created_at, is_read, ip_address, user_agent) 
            VALUES (:name, :email, :subject, :message, NOW(), 0, :ip_address, :user_agent)";
    
    $database->query($sql);
    $database->bind(':name', $name);
    $database->bind(':email', $email);
    $database->bind(':subject', $subject);
    $database->bind(':message', $message);
    $database->bind(':ip_address', $_SERVER['REMOTE_ADDR']);
    $database->bind(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown');
    
    $database->execute();
    
} catch (Exception $e) {
    // Log error but don't show to user
    error_log("Contact form database error: " . $e->getMessage());
}

// Try to send email as well (optional)
$email_subject = "New Contact Form Submission: " . $subject;
$email_body = "
New contact form submission from Portfolio Website

Name: $name
Email: $email
Subject: $subject

Message:
$message

---
Timestamp: " . date('Y-m-d H:i:s') . "
IP: " . $_SERVER['REMOTE_ADDR'];

$headers = [
    'From: noreply@portfolio.local',
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8'
];

// Try to send email, but don't fail if it doesn't work
@mail($RECIPIENT_EMAIL, $email_subject, $email_body, implode("\r\n", $headers));

// Always return success
$response['success'] = true;
$response['message'] = 'Thank you for your message! I have received it and will get back to you soon.';

echo json_encode($response);
?>
