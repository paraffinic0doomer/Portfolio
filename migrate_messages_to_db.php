<?php
// Migrate JSON messages to database
require_once 'config/database.php';

try {
    $database = new Database();
    
    // Read JSON file
    $json_file = 'contact_messages.json';
    if (file_exists($json_file)) {
        $json_content = file_get_contents($json_file);
        $messages = json_decode($json_content, true);
        
        if (is_array($messages)) {
            foreach ($messages as $message) {
                // Insert each message into database
                $sql = "INSERT INTO contact_messages (name, email, subject, message, created_at, is_read, ip_address) 
                        VALUES (:name, :email, :subject, :message, :created_at, :is_read, :ip_address)";
                
                $database->query($sql);
                $database->bind(':name', $message['name']);
                $database->bind(':email', $message['email']);
                $database->bind(':subject', $message['subject']);
                $database->bind(':message', $message['message']);
                $database->bind(':created_at', $message['timestamp']);
                $database->bind(':is_read', $message['read'] ? 1 : 0);
                $database->bind(':ip_address', $message['ip']);
                
                $database->execute();
            }
            
            echo "Successfully migrated " . count($messages) . " messages to database!";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
