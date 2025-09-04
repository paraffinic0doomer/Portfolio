<?php
// Email Configuration Setup
// This script automatically configures the contact form with your email from the database

require_once 'config/database.php';

try {
    $database = new Database();
    
    // Get email from personal_info table
    $database->query('SELECT email FROM personal_info WHERE id = 1');
    $personal = $database->single();
    
    if ($personal && $personal->email) {
        $recipient_email = $personal->email;
        
        // Read the contact handler file
        $contact_handler_content = file_get_contents('contact-handler.php');
        
        // Replace the placeholder email with the actual email
        $updated_content = str_replace(
            '$RECIPIENT_EMAIL = "your-email@example.com";',
            '$RECIPIENT_EMAIL = "' . $recipient_email . '";',
            $contact_handler_content
        );
        
        // Write back to file
        file_put_contents('contact-handler.php', $updated_content);
        
        echo "✅ Contact form configured successfully!\n";
        echo "📧 Emails will be sent to: " . $recipient_email . "\n";
        echo "🔧 Contact form is now ready to use.\n";
    } else {
        echo "❌ No email found in database. Please add your email in the admin panel first.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
