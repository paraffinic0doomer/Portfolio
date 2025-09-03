<?php
// Database setup script
require_once 'config/database.php';

try {
    // Connect to MySQL without specifying database
    $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '" . DB_NAME . "' created successfully or already exists.<br>";

    // Select the database
    $pdo->exec("USE " . DB_NAME);

    // Create users table for admin authentication
    $createUsersTable = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($createUsersTable);
    echo "Users table created successfully.<br>";

    // Create personal_info table
    $createPersonalTable = "
    CREATE TABLE IF NOT EXISTS personal_info (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        title VARCHAR(150) NOT NULL,
        bio TEXT,
        about TEXT,
        email VARCHAR(100),
        phone VARCHAR(20),
        location VARCHAR(100),
        profile_image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($createPersonalTable);
    echo "Personal info table created successfully.<br>";

    // Create skills table
    $createSkillsTable = "
    CREATE TABLE IF NOT EXISTS skills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category VARCHAR(50) NOT NULL,
        skill_name VARCHAR(100) NOT NULL,
        proficiency INT DEFAULT 80,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($createSkillsTable);
    echo "Skills table created successfully.<br>";

    // Create projects table
    $createProjectsTable = "
    CREATE TABLE IF NOT EXISTS projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        description TEXT,
        long_description TEXT,
        image_url VARCHAR(255),
        technologies JSON,
        github_url VARCHAR(255),
        featured BOOLEAN DEFAULT FALSE,
        status ENUM('completed', 'in-progress', 'planned') DEFAULT 'completed',
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($createProjectsTable);
    echo "Projects table created successfully.<br>";

    // Create social_links table
    $createSocialTable = "
    CREATE TABLE IF NOT EXISTS social_links (
        id INT AUTO_INCREMENT PRIMARY KEY,
        platform VARCHAR(50) NOT NULL,
        url VARCHAR(255) NOT NULL,
        icon_class VARCHAR(100),
        display_order INT DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($createSocialTable);
    echo "Social links table created successfully.<br>";

    // Create site_settings table
    $createSettingsTable = "
    CREATE TABLE IF NOT EXISTS site_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        setting_type ENUM('text', 'json', 'boolean', 'number') DEFAULT 'text',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($createSettingsTable);
    echo "Site settings table created successfully.<br>";

    // Insert default admin user (password: admin123)
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $insertAdmin = "
    INSERT IGNORE INTO users (username, email, password, role) 
    VALUES ('admin', 'admin@portfolio.com', :password, 'admin')";
    $stmt = $pdo->prepare($insertAdmin);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->execute();
    echo "Default admin user created (username: admin, password: admin123).<br>";

    // Insert default personal info
    $insertPersonal = "
    INSERT IGNORE INTO personal_info (id, name, title, bio, about, email, phone, location) 
    VALUES (1, 'Your Name', 'Full Stack Developer', 
            'Passionate developer creating amazing digital experiences',
            'I am a dedicated full stack developer with expertise in modern web technologies. I love creating innovative solutions and bringing ideas to life through code.',
            'your.email@example.com', '+1 (555) 123-4567', 'Your City, Country')";
    $pdo->exec($insertPersonal);
    echo "Default personal info inserted.<br>";

    // Insert default skills
    $defaultSkills = [
        ['Frontend', 'HTML5', 90],
        ['Frontend', 'CSS3', 85],
        ['Frontend', 'JavaScript', 88],
        ['Frontend', 'React', 85],
        ['Frontend', 'Vue.js', 80],
        ['Backend', 'PHP', 85],
        ['Backend', 'Node.js', 80],
        ['Backend', 'Python', 75],
        ['Backend', 'MySQL', 85],
        ['Backend', 'MongoDB', 70],
        ['Tools', 'Git', 90],
        ['Tools', 'Docker', 75],
        ['Tools', 'VS Code', 95],
        ['Tools', 'Linux', 80]
    ];

    $insertSkill = "INSERT IGNORE INTO skills (category, skill_name, proficiency) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($insertSkill);
    foreach ($defaultSkills as $skill) {
        $stmt->execute($skill);
    }
    echo "Default skills inserted.<br>";

    // Insert default social links
    $defaultSocial = [
        ['GitHub', 'https://github.com/yourusername', 'fab fa-github'],
        ['LinkedIn', 'https://linkedin.com/in/yourusername', 'fab fa-linkedin'],
        ['Twitter', 'https://twitter.com/yourusername', 'fab fa-twitter'],
        ['Email', 'mailto:your.email@example.com', 'fas fa-envelope']
    ];

    $insertSocial = "INSERT IGNORE INTO social_links (platform, url, icon_class) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($insertSocial);
    foreach ($defaultSocial as $social) {
        $stmt->execute($social);
    }
    echo "Default social links inserted.<br>";

    echo "<br><strong>Database setup completed successfully!</strong><br>";
    echo "<a href='admin.html'>Go to Admin Login</a> | <a href='index.html'>View Portfolio</a>";

} catch(PDOException $e) {
    echo "Database setup failed: " . $e->getMessage();
}
?>
