<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';

// Check if user is logged in (uncomment when authentication is set up)
// if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
//     echo json_encode(['success' => false, 'error' => 'Unauthorized']);
//     exit;
// }

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Handle DELETE requests with URL parameters
if ($method === 'DELETE' && isset($_GET['id'])) {
    $input = ['id' => $_GET['id']];
}

try {
    switch ($method) {
        case 'GET':
            // Get all skills
            $stmt = $pdo->prepare("SELECT * FROM skills ORDER BY category, sort_order, name");
            $stmt->execute();
            $skills = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $skills]);
            break;
            
        case 'POST':
            // Add new skill
            $name = $input['name'] ?? '';
            $category = $input['category'] ?? '';
            $proficiency_level = $input['proficiency_level'] ?? 1;
            $sort_order = $input['sort_order'] ?? 0;
            
            if (empty($name) || empty($category)) {
                echo json_encode(['success' => false, 'error' => 'Name and category are required']);
                break;
            }
            
            $stmt = $pdo->prepare("INSERT INTO skills (name, category, proficiency_level, sort_order) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $category, $proficiency_level, $sort_order]);
            
            echo json_encode(['success' => true, 'message' => 'Skill added successfully']);
            break;
            
        case 'PUT':
            // Update skill
            $id = $input['id'] ?? 0;
            $name = $input['name'] ?? '';
            $category = $input['category'] ?? '';
            $proficiency_level = $input['proficiency_level'] ?? 1;
            $sort_order = $input['sort_order'] ?? 0;
            
            if (empty($id) || empty($name) || empty($category)) {
                echo json_encode(['success' => false, 'error' => 'ID, name and category are required']);
                break;
            }
            
            $stmt = $pdo->prepare("UPDATE skills SET name = ?, category = ?, proficiency_level = ?, sort_order = ? WHERE id = ?");
            $stmt->execute([$name, $category, $proficiency_level, $sort_order, $id]);
            
            echo json_encode(['success' => true, 'message' => 'Skill updated successfully']);
            break;
            
        case 'DELETE':
            // Delete skill
            $id = $input['id'] ?? $_GET['id'] ?? 0;
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'error' => 'ID is required']);
                break;
            }
            
            // First check if skill exists
            $stmt = $pdo->prepare("SELECT id FROM skills WHERE id = ?");
            $stmt->execute([$id]);
            
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Skill not found']);
                break;
            }
            
            // Delete the skill
            $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Skill deleted successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            break;
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
