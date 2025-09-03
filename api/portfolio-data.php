<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $database = new Database();
    
    // Get personal info
    $database->query('SELECT * FROM personal_info LIMIT 1');
    $personal = $database->single();
    
    // Get skills grouped by category
    $database->query('SELECT * FROM skills ORDER BY category, display_order');
    $skillsResult = $database->resultSet();
    $skills = [];
    foreach ($skillsResult as $skill) {
        $skills[$skill->category][] = $skill;
    }
    
    // Get projects
    $database->query('SELECT * FROM projects ORDER BY display_order, created_at DESC');
    $projects = $database->resultSet();
    
    // Get social links
    $database->query('SELECT * FROM social_links WHERE is_active = 1 ORDER BY display_order');
    $social = $database->resultSet();
    
    // Return all data
    echo json_encode([
        'success' => true,
        'personal' => $personal,
        'skills' => $skills,
        'projects' => $projects,
        'social' => $social
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
