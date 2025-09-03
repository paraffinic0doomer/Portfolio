<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';

try {
    // Fetch featured projects ordered by sort_order
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE is_featured = 1 ORDER BY sort_order, created_at DESC");
    $stmt->execute();
    $projects = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $projects
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch projects'
    ]);
}
?>
