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
            // Get all projects
            $stmt = $pdo->prepare("SELECT * FROM projects ORDER BY sort_order, created_at DESC");
            $stmt->execute();
            $projects = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $projects]);
            break;
            
        case 'POST':
            // Add new project
            $title = $input['title'] ?? '';
            $description = $input['description'] ?? '';
            $technologies = $input['technologies'] ?? '';
            $github_url = $input['github_url'] ?? '';
            $project_url = $input['project_url'] ?? '';
            $is_featured = $input['is_featured'] ?? 0;
            $sort_order = $input['sort_order'] ?? 0;
            
            if (empty($title)) {
                echo json_encode(['success' => false, 'error' => 'Title is required']);
                break;
            }
            
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, technologies, github_url, project_url, is_featured, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $technologies, $github_url, $project_url, $is_featured, $sort_order]);
            
            echo json_encode(['success' => true, 'message' => 'Project added successfully']);
            break;
            
        case 'PUT':
            // Update project
            $id = $input['id'] ?? 0;
            $title = $input['title'] ?? '';
            $description = $input['description'] ?? '';
            $technologies = $input['technologies'] ?? '';
            $github_url = $input['github_url'] ?? '';
            $project_url = $input['project_url'] ?? '';
            $is_featured = $input['is_featured'] ?? 0;
            $sort_order = $input['sort_order'] ?? 0;
            
            if (empty($id) || empty($title)) {
                echo json_encode(['success' => false, 'error' => 'ID and title are required']);
                break;
            }
            
            $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, technologies = ?, github_url = ?, project_url = ?, is_featured = ?, sort_order = ? WHERE id = ?");
            $stmt->execute([$title, $description, $technologies, $github_url, $project_url, $is_featured, $sort_order, $id]);
            
            echo json_encode(['success' => true, 'message' => 'Project updated successfully']);
            break;
            
        case 'DELETE':
            // Delete project
            $id = $input['id'] ?? $_GET['id'] ?? 0;
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'error' => 'ID is required']);
                break;
            }
            
            // First check if project exists
            $stmt = $pdo->prepare("SELECT id FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Project not found']);
                break;
            }
            
            // Delete the project
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Project deleted successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            break;
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
