<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin.html');
    exit();
}

require_once 'config/database.php';

// Initialize database connection
$database = new Database();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'update_personal') {
        $name = trim($_POST['name']);
        $title = trim($_POST['title']);
        $bio = trim($_POST['bio']);
        $about = trim($_POST['about']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $location = trim($_POST['location']);
        
        $database->query('UPDATE personal_info SET name = :name, title = :title, bio = :bio, about = :about, email = :email, phone = :phone, location = :location WHERE id = 1');
        $database->bind(':name', $name);
        $database->bind(':title', $title);
        $database->bind(':bio', $bio);
        $database->bind(':about', $about);
        $database->bind(':email', $email);
        $database->bind(':phone', $phone);
        $database->bind(':location', $location);
        
        if ($database->execute()) {
            $success = "Personal information updated successfully!";
        } else {
            $error = "Failed to update personal information.";
        }
    }
    
    elseif ($action == 'add_project') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $technologies = array_map('trim', explode(',', $_POST['technologies']));
        $github_url = trim($_POST['github_url']);
        
        $database->query('INSERT INTO projects (title, description, technologies, github_url) VALUES (:title, :description, :technologies, :github_url)');
        $database->bind(':title', $title);
        $database->bind(':description', $description);
        $database->bind(':technologies', json_encode($technologies));
        $database->bind(':github_url', $github_url);
        
        if ($database->execute()) {
            $success = "Project added successfully!";
        } else {
            $error = "Failed to add project.";
        }
    }
    
    elseif ($action == 'add_skill') {
        $category = trim($_POST['category']);
        $skill_name = trim($_POST['skill_name']);
        $proficiency = (int)$_POST['proficiency'];
        
        $database->query('INSERT INTO skills (category, skill_name, proficiency) VALUES (:category, :skill_name, :proficiency)');
        $database->bind(':category', $category);
        $database->bind(':skill_name', $skill_name);
        $database->bind(':proficiency', $proficiency);
        
        if ($database->execute()) {
            $success = "Skill added successfully!";
        } else {
            $error = "Failed to add skill.";
        }
    }
    
    elseif ($action == 'edit_project') {
        $project_id = (int)$_POST['project_id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $technologies = array_map('trim', explode(',', $_POST['technologies']));
        $github_url = trim($_POST['github_url']);
        
        $database->query('UPDATE projects SET title = :title, description = :description, technologies = :technologies, github_url = :github_url WHERE id = :id');
        $database->bind(':id', $project_id);
        $database->bind(':title', $title);
        $database->bind(':description', $description);
        $database->bind(':technologies', json_encode($technologies));
        $database->bind(':github_url', $github_url);
        
        if ($database->execute()) {
            $success = "Project updated successfully!";
        } else {
            $error = "Failed to update project.";
        }
    }
    
    elseif ($action == 'edit_skill') {
        $skill_id = (int)$_POST['skill_id'];
        $category = trim($_POST['category']);
        $skill_name = trim($_POST['skill_name']);
        $proficiency = (int)$_POST['proficiency'];
        
        $database->query('UPDATE skills SET category = :category, skill_name = :skill_name, proficiency = :proficiency WHERE id = :id');
        $database->bind(':id', $skill_id);
        $database->bind(':category', $category);
        $database->bind(':skill_name', $skill_name);
        $database->bind(':proficiency', $proficiency);
        
        if ($database->execute()) {
            $success = "Skill updated successfully!";
        } else {
            $error = "Failed to update skill.";
        }
    }
    
    elseif ($action == 'edit_social') {
        $social_id = (int)$_POST['social_id'];
        $platform = trim($_POST['platform']);
        $url = trim($_POST['url']);
        $icon_class = trim($_POST['icon_class']);
        
        $database->query('UPDATE social_links SET platform = :platform, url = :url, icon_class = :icon_class WHERE id = :id');
        $database->bind(':id', $social_id);
        $database->bind(':platform', $platform);
        $database->bind(':url', $url);
        $database->bind(':icon_class', $icon_class);
        
        if ($database->execute()) {
            $success = "Social link updated successfully!";
        } else {
            $error = "Failed to update social link.";
        }
    }
    
    elseif ($action == 'delete_project') {
        $project_id = (int)$_POST['project_id'];
        
        $database->query('DELETE FROM projects WHERE id = :id');
        $database->bind(':id', $project_id);
        
        if ($database->execute()) {
            $success = "Project deleted successfully!";
        } else {
            $error = "Failed to delete project.";
        }
    }
    
    elseif ($action == 'delete_skill') {
        $skill_id = (int)$_POST['skill_id'];
        
        $database->query('DELETE FROM skills WHERE id = :id');
        $database->bind(':id', $skill_id);
        
        if ($database->execute()) {
            $success = "Skill deleted successfully!";
        } else {
            $error = "Failed to delete skill.";
        }
    }
    
    elseif ($action == 'add_social') {
        $platform = trim($_POST['platform']);
        $url = trim($_POST['url']);
        $icon_class = trim($_POST['icon_class']);
        
        $database->query('INSERT INTO social_links (platform, url, icon_class, is_active) VALUES (:platform, :url, :icon_class, 1)');
        $database->bind(':platform', $platform);
        $database->bind(':url', $url);
        $database->bind(':icon_class', $icon_class);
        
        if ($database->execute()) {
            $success = "Social link added successfully!";
        } else {
            $error = "Failed to add social link.";
        }
    }
    
    elseif ($action == 'delete_social') {
        $social_id = (int)$_POST['social_id'];
        
        $database->query('DELETE FROM social_links WHERE id = :id');
        $database->bind(':id', $social_id);
        
        if ($database->execute()) {
            $success = "Social link deleted successfully!";
        } else {
            $error = "Failed to delete social link.";
        }
    }
    
    elseif ($action == 'delete_message') {
        $message_id = (int)$_POST['message_id'];
        
        try {
            $database->query('DELETE FROM contact_messages WHERE id = :id');
            $database->bind(':id', $message_id);
            $result = $database->execute();
            
            if ($result) {
                $success = "Message deleted successfully!";
            } else {
                $error = "Failed to delete message.";
            }
        } catch (Exception $e) {
            $error = "Error deleting message: " . $e->getMessage();
        }
    }
    
    elseif ($action == 'update_profile_image') {
        // Handle file upload
        if (isset($_FILES['profile_image'])) {
            $file_error = $_FILES['profile_image']['error'];
            
            // Check for upload errors
            switch ($file_error) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error = "No file was uploaded.";
                    break;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error = "File is too large. Maximum size is " . ini_get('upload_max_filesize') . ".";
                    break;
                default:
                    $error = "Unknown upload error occurred.";
                    break;
            }
            
            if (!isset($error)) {
                $upload_dir = 'uploads/';
                
                // Create uploads directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    if (!mkdir($upload_dir, 0755, true)) {
                        $error = "Failed to create uploads directory.";
                    }
                }
                
                if (!isset($error)) {
                    $file_name = $_FILES['profile_image']['name'];
                    $file_size = $_FILES['profile_image']['size'];
                    $file_tmp = $_FILES['profile_image']['tmp_name'];
                    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $allowed_mimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    $max_size = 5 * 1024 * 1024; // 5MB
                    
                    // Get file mime type
                    $file_mime = $_FILES['profile_image']['type'];
                    $detected_mime = mime_content_type($file_tmp);
                    
                    if (!in_array($file_extension, $allowed_types)) {
                        $error = "Invalid file extension. Please upload JPG, JPEG, PNG, GIF, or WebP images only.";
                    } elseif (!in_array($file_mime, $allowed_mimes) && !in_array($detected_mime, $allowed_mimes)) {
                        $error = "Invalid file type detected. Please upload a valid image file (JPG, JPEG, PNG, GIF, or WebP).";
                    } elseif ($file_size > $max_size) {
                        $error = "File is too large. Maximum size is 5MB.";
                    } else {
                        $new_filename = 'profile_' . time() . '.' . $file_extension;
                        $upload_path = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            // Delete old profile image if exists
                            $database->query('SELECT profile_image FROM personal_info WHERE id = 1');
                            $old_data = $database->single();
                            if ($old_data && $old_data->profile_image && file_exists($old_data->profile_image)) {
                                unlink($old_data->profile_image);
                            }
                            
                            $database->query('UPDATE personal_info SET profile_image = :profile_image WHERE id = 1');
                            $database->bind(':profile_image', $upload_path);
                            
                            if ($database->execute()) {
                                $success = "Profile image updated successfully!";
                            } else {
                                $error = "Failed to update profile image in database.";
                            }
                        } else {
                            // More detailed error reporting
                            $upload_errors = [];
                            if (!is_dir($upload_dir)) $upload_errors[] = "Upload directory doesn't exist";
                            if (!is_writable($upload_dir)) $upload_errors[] = "Upload directory not writable";
                            if (!file_exists($file_tmp)) $upload_errors[] = "Temporary file doesn't exist";
                            
                            $error = "Failed to move uploaded file. Issues: " . 
                                    (!empty($upload_errors) ? implode(', ', $upload_errors) : "Unknown error") . 
                                    ". Upload dir: $upload_dir, Temp file: $file_tmp, Target: $upload_path";
                        }
                    }
                }
            }
        } else {
            $error = "No file was selected for upload.";
        }
    }
}

// Get current data
$database->query('SELECT * FROM personal_info WHERE id = 1');
$personal = $database->single();

$database->query('SELECT * FROM projects ORDER BY created_at DESC');
$projects = $database->resultset();

$database->query('SELECT * FROM skills ORDER BY category, skill_name');
$skills = $database->resultset();

$database->query('SELECT * FROM social_links ORDER BY display_order, platform');
$social_links = $database->resultset();

// Load contact messages from database
$contact_messages = [];
try {
    $database->query('SELECT * FROM contact_messages ORDER BY created_at DESC');
    $contact_messages = $database->resultset();
} catch (Exception $e) {
    error_log("Error loading contact messages: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #0a0a0a;
            color: #ffffff;
            line-height: 1.6;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #333;
        }
        
        .dashboard-header h1 {
            font-size: 2rem;
            background: linear-gradient(135deg, #00d4ff, #7c3aed);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .header-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            background: #00d4ff;
            color: #0a0a0a;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #00b8e6;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #7c3aed;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #6d28d9;
        }
        
        .btn-danger {
            background: #ff4757;
            color: white;
        }
        
        .btn-danger:hover {
            background: #ff3742;
        }
        
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid #333;
        }
        
        .tab-btn {
            padding: 1rem 2rem;
            background: transparent;
            border: none;
            color: #b0b0b0;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
            font-size: 1rem;
        }
        
        .tab-btn.active {
            color: #00d4ff;
            border-bottom-color: #00d4ff;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .form-section {
            background: #1a1a1a;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid #333;
            margin-bottom: 2rem;
        }
        
        .form-section h3 {
            margin-bottom: 1.5rem;
            color: #00d4ff;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ffffff;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 1rem;
            background: #242424;
            border: 2px solid #333;
            border-radius: 8px;
            color: #ffffff;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input[type="file"] {
            padding: 0.75rem;
            background: #1a1a1a;
            border: 2px dashed #333;
        }
        
        .form-group input[type="file"]:hover {
            border-color: #00d4ff;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #00d4ff;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: rgba(46, 213, 115, 0.1);
            border: 1px solid #2ed573;
            color: #2ed573;
        }
        
        .alert-error {
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid #ff4757;
            color: #ff4757;
        }
        
        .data-table {
            width: 100%;
            background: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #333;
        }
        
        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        
        .data-table th {
            background: #242424;
            color: #00d4ff;
            font-weight: 600;
        }
        
        .data-table tr:hover {
            background: #242424;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        /* Messages Section Styles */
        .message-count {
            background: #ff4757;
            color: white;
            border-radius: 12px;
            padding: 0.2rem 0.6rem;
            font-size: 0.8rem;
            margin-left: 0.5rem;
        }
        
        .message-stats {
            color: #888;
            font-weight: normal;
            font-size: 0.9rem;
        }
        
        .no-messages {
            text-align: center;
            padding: 3rem;
            color: #888;
        }
        
        .no-messages i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #555;
        }
        
        .messages-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .message-card {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .message-card:hover {
            border-color: #00d4ff;
            transform: translateY(-2px);
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .message-info h4 {
            color: #00d4ff;
            margin-bottom: 0.5rem;
        }
        
        .message-email {
            color: #888;
            margin: 0;
        }
        
        .message-email a {
            color: #00d4ff;
            text-decoration: none;
        }
        
        .message-email a:hover {
            text-decoration: underline;
        }
        
        .message-time {
            color: #888;
            font-size: 0.9rem;
            text-align: right;
        }
        
        .message-subject {
            background: #0a0a0a;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            border-left: 3px solid #00d4ff;
        }
        
        .message-content {
            background: #242424;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        
        .message-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #333;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .message-ip {
            color: #888;
            font-size: 0.9rem;
        }
        
        .message-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .tabs {
                flex-wrap: wrap;
            }
            
            .message-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .message-time {
                text-align: left;
            }
            
            .message-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .message-actions {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Portfolio Admin Dashboard</h1>
            <div class="header-actions">
                <a href="index.html" class="btn btn-secondary" target="_blank">
                    <i class="fas fa-eye"></i> View Portfolio
                </a>
                <a href="auth.php?action=logout" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <div class="tabs">
            <button class="tab-btn active" onclick="showTab('personal', this)">Personal Info</button>
            <button class="tab-btn" onclick="showTab('projects', this)">Projects</button>
            <button class="tab-btn" onclick="showTab('skills', this)">Skills</button>
            <button class="tab-btn" onclick="showTab('social', this)">Social Links</button>
            <button class="tab-btn" onclick="showTab('messages', this)">
                <i class="fas fa-envelope"></i> Messages 
                <?php if (count($contact_messages) > 0): ?>
                    <span class="message-count"><?php echo count($contact_messages); ?></span>
                <?php endif; ?>
            </button>
        </div>
        
        <!-- Personal Info Tab -->
        <div id="personal" class="tab-content active">
            <div class="form-section">
                <h3>Update Personal Information</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="update_personal">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($personal->name ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="title">Professional Title</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($personal->title ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Short Bio</label>
                        <textarea id="bio" name="bio" required><?php echo htmlspecialchars($personal->bio ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="about">About Description</label>
                        <textarea id="about" name="about" required><?php echo htmlspecialchars($personal->about ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($personal->email ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($personal->phone ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($personal->location ?? ''); ?>">
                    </div>
                    
                    <button type="submit" class="btn">Update Personal Info</button>
                </form>
            </div>
            
            <div class="form-section">
                <h3>Update Profile Image</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile_image">
                    
                    <?php if (!empty($personal->profile_image) && file_exists($personal->profile_image)): ?>
                    <div class="form-group">
                        <label>Current Profile Image</label>
                        <div style="margin: 1rem 0;">
                            <img src="<?php echo htmlspecialchars($personal->profile_image); ?>" 
                                 alt="Current Profile" 
                                 style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 2px solid #00d4ff;">
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="profile_image">Choose New Profile Image</label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*">
                        <small>Supported formats: JPG, PNG, GIF, WebP (Max size: 5MB)</small>
                        <br><small><a href="test-upload.php" target="_blank" style="color: #00d4ff;">Test Upload Functionality</a></small>
                    </div>
                    
                    <button type="submit" class="btn">Upload Profile Image</button>
                </form>
            </div>
        </div>
        
        <!-- Projects Tab -->
        <div id="projects" class="tab-content">
            <div class="form-section">
                <h3>Add New Project</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add_project">
                    
                    <div class="form-group">
                        <label for="project_title">Project Title</label>
                        <input type="text" id="project_title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="project_description">Description</label>
                        <textarea id="project_description" name="description" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="technologies">Technologies (comma-separated)</label>
                        <input type="text" id="technologies" name="technologies" placeholder="React, Node.js, MongoDB" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="github_url">GitHub URL</label>
                            <input type="url" id="github_url" name="github_url" placeholder="https://github.com/username/project">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">Add Project</button>
                </form>
            </div>
            
            <?php if (!empty($projects)): ?>
            <div class="form-section">
                <h3>Existing Projects</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Technologies</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($project->title); ?></td>
                            <td><?php echo htmlspecialchars(substr($project->description, 0, 100)) . '...'; ?></td>
                            <td>
                                <?php 
                                $techs = json_decode($project->technologies);
                                echo $techs ? implode(', ', array_slice($techs, 0, 3)) : 'N/A';
                                ?>
                            </td>
                            <td class="action-buttons">
                                <button class="btn btn-small btn-secondary" onclick="editProject(
                                    <?php echo $project->id; ?>, 
                                    '<?php echo addslashes($project->title); ?>', 
                                    '<?php echo addslashes($project->description); ?>', 
                                    '<?php echo addslashes(implode(', ', json_decode($project->technologies) ?? [])); ?>', 
                                    '<?php echo addslashes($project->github_url ?? ''); ?>'
                                )">Edit</button>
                                <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this project?')">
                                    <input type="hidden" name="action" value="delete_project">
                                    <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">
                                    <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Skills Tab -->
        <div id="skills" class="tab-content">
            <div class="form-section">
                <h3>Add New Skill</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add_skill">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="skill_category">Category</label>
                            <select id="skill_category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Frontend">Frontend</option>
                                <option value="Backend">Backend</option>
                                <option value="Database">Database</option>
                                <option value="Tools">Tools</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="skill_name">Skill Name</label>
                            <input type="text" id="skill_name" name="skill_name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="proficiency">Proficiency (1-100)</label>
                        <input type="number" id="proficiency" name="proficiency" min="1" max="100" value="80" required>
                    </div>
                    
                    <button type="submit" class="btn">Add Skill</button>
                </form>
            </div>
            
            <?php if (!empty($skills)): ?>
            <div class="form-section">
                <h3>Existing Skills</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Skill</th>
                            <th>Proficiency</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($skills as $skill): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($skill->category); ?></td>
                            <td><?php echo htmlspecialchars($skill->skill_name); ?></td>
                            <td><?php echo $skill->proficiency; ?>%</td>
                            <td class="action-buttons">
                                <button class="btn btn-small btn-secondary" onclick="editSkill(
                                    <?php echo $skill->id; ?>, 
                                    '<?php echo addslashes($skill->category); ?>', 
                                    '<?php echo addslashes($skill->skill_name); ?>', 
                                    <?php echo $skill->proficiency; ?>
                                )">Edit</button>
                                <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this skill?')">
                                    <input type="hidden" name="action" value="delete_skill">
                                    <input type="hidden" name="skill_id" value="<?php echo $skill->id; ?>">
                                    <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Social Links Tab -->
        <div id="social" class="tab-content">
            <div class="form-section">
                <h3>Add Social Link</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add_social">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="platform">Platform</label>
                            <input type="text" id="platform" name="platform" placeholder="e.g., GitHub, LinkedIn, Twitter" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="url">URL</label>
                            <input type="url" id="url" name="url" placeholder="https://github.com/username" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon_class">Icon Class</label>
                        <input type="text" id="icon_class" name="icon_class" placeholder="fab fa-github" required>
                        <small>Font Awesome icon class (e.g., fab fa-github, fab fa-linkedin, fab fa-twitter, fas fa-envelope)</small>
                    </div>
                    
                    <button type="submit" class="btn">Add Social Link</button>
                </form>
            </div>
            
            <?php if (!empty($social_links)): ?>
            <div class="form-section">
                <h3>Existing Social Links</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Platform</th>
                            <th>URL</th>
                            <th>Icon</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($social_links as $social): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($social->platform); ?></td>
                            <td><a href="<?php echo htmlspecialchars($social->url); ?>" target="_blank"><?php echo htmlspecialchars($social->url); ?></a></td>
                            <td><i class="<?php echo htmlspecialchars($social->icon_class); ?>"></i> <?php echo htmlspecialchars($social->icon_class); ?></td>
                            <td class="action-buttons">
                                <button class="btn btn-small btn-secondary" onclick="editSocial(
                                    <?php echo $social->id; ?>, 
                                    '<?php echo addslashes($social->platform); ?>', 
                                    '<?php echo addslashes($social->url); ?>', 
                                    '<?php echo addslashes($social->icon_class); ?>'
                                )">Edit</button>
                                <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this social link?')">
                                    <input type="hidden" name="action" value="delete_social">
                                    <input type="hidden" name="social_id" value="<?php echo $social->id; ?>">
                                    <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Messages Tab -->
        <div id="messages" class="tab-content">
            <div class="form-section">
                <h3>
                    <i class="fas fa-envelope"></i> Contact Messages 
                    <span class="message-stats">(<?php echo count($contact_messages); ?> total)</span>
                </h3>
                
                <?php if (empty($contact_messages)): ?>
                <div class="no-messages">
                    <i class="fas fa-inbox"></i>
                    <h4>No messages yet</h4>
                    <p>Messages from your contact form will appear here.</p>
                </div>
                <?php else: ?>
                <div class="messages-list">
                    <?php foreach ($contact_messages as $index => $message): ?>
                    <div class="message-card">
                        <div class="message-header">
                            <div class="message-info">
                                <h4><?php echo htmlspecialchars($message->name); ?></h4>
                                <p class="message-email">
                                    <i class="fas fa-envelope"></i> 
                                    <a href="mailto:<?php echo htmlspecialchars($message->email); ?>">
                                        <?php echo htmlspecialchars($message->email); ?>
                                    </a>
                                </p>
                            </div>
                            <div class="message-time">
                                <i class="fas fa-clock"></i>
                                <?php echo date('M j, Y \a\t g:i A', strtotime($message->created_at)); ?>
                            </div>
                        </div>
                        
                        <div class="message-subject">
                            <strong>Subject:</strong> <?php echo htmlspecialchars($message->subject); ?>
                        </div>
                        
                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars($message->message)); ?>
                        </div>
                        
                        <div class="message-meta">
                            <span class="message-ip">
                                <i class="fas fa-globe"></i> <?php echo htmlspecialchars($message->ip_address); ?>
                            </span>
                            <div class="message-actions">
                                <a href="mailto:<?php echo htmlspecialchars($message->email); ?>?subject=Re: <?php echo urlencode($message->subject); ?>" 
                                   class="btn btn-small">
                                    <i class="fas fa-reply"></i> Reply
                                </a>
                                <button class="btn btn-small btn-danger" onclick="deleteMessage(<?php echo $message->id; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName, clickedButton) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked button
            if (clickedButton) {
                clickedButton.classList.add('active');
            } else {
                // Fallback: find the button by onclick attribute
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    if (btn.getAttribute('onclick').includes(tabName)) {
                        btn.classList.add('active');
                    }
                });
            }
        }
        
        function editProject(id, title, description, technologies, githubUrl) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="edit_project">
                <input type="hidden" name="project_id" value="${id}">
                
                <div class="form-group">
                    <label>Project Title</label>
                    <input type="text" name="title" value="${title}" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required>${description}</textarea>
                </div>
                
                <div class="form-group">
                    <label>Technologies (comma-separated)</label>
                    <input type="text" name="technologies" value="${technologies}" required>
                </div>
                
                <div class="form-group">
                    <label>GitHub URL</label>
                    <input type="url" name="github_url" value="${githubUrl}">
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" class="btn">Update Project</button>
                    <button type="button" class="btn btn-secondary" onclick="location.reload()">Cancel</button>
                </div>
            `;
            
            // Replace the projects tab content with edit form
            const projectsTab = document.getElementById('projects');
            projectsTab.innerHTML = '<div class="form-section"><h3>Edit Project</h3>' + form.outerHTML + '</div>';
        }
        
        function editSkill(id, category, skillName, proficiency) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="edit_skill">
                <input type="hidden" name="skill_id" value="${id}">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" required>
                            <option value="Frontend" ${category === 'Frontend' ? 'selected' : ''}>Frontend</option>
                            <option value="Backend" ${category === 'Backend' ? 'selected' : ''}>Backend</option>
                            <option value="Database" ${category === 'Database' ? 'selected' : ''}>Database</option>
                            <option value="Tools" ${category === 'Tools' ? 'selected' : ''}>Tools</option>
                            <option value="Other" ${category === 'Other' ? 'selected' : ''}>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Skill Name</label>
                        <input type="text" name="skill_name" value="${skillName}" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Proficiency (1-100)</label>
                    <input type="number" name="proficiency" min="1" max="100" value="${proficiency}" required>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" class="btn">Update Skill</button>
                    <button type="button" class="btn btn-secondary" onclick="location.reload()">Cancel</button>
                </div>
            `;
            
            // Replace the skills tab content with edit form
            const skillsTab = document.getElementById('skills');
            skillsTab.innerHTML = '<div class="form-section"><h3>Edit Skill</h3>' + form.outerHTML + '</div>';
        }
        
        function editSocial(id, platform, url, iconClass) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="edit_social">
                <input type="hidden" name="social_id" value="${id}">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Platform</label>
                        <input type="text" name="platform" value="${platform}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>URL</label>
                        <input type="url" name="url" value="${url}" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Icon Class</label>
                    <input type="text" name="icon_class" value="${iconClass}" required>
                    <small>Font Awesome icon class (e.g., fab fa-github, fab fa-linkedin, fab fa-twitter, fas fa-envelope)</small>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" class="btn">Update Social Link</button>
                    <button type="button" class="btn btn-secondary" onclick="location.reload()">Cancel</button>
                </div>
            `;
            
            // Replace the social tab content with edit form
            const socialTab = document.getElementById('social');
            socialTab.innerHTML = '<div class="form-section"><h3>Edit Social Link</h3>' + form.outerHTML + '</div>';
        }
        
        function deleteMessage(messageId) {
            if (confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
                // Create a form to delete the message
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_message">
                    <input type="hidden" name="message_id" value="${messageId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
