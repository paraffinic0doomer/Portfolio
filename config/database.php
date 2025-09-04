<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portfolio_db');

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $dbh;
    private $error;
    private $stmt;

    public function __construct() {
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        
        // Set options
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );

        // Create PDO instance
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    // Get database connection
    public function getConnection() {
        return $this->dbh;
    }

    // Prepare statement with query
    public function query($query) {
        $this->stmt = $this->dbh->prepare($query);
    }

    // Bind values
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Execute the prepared statement
    public function execute() {
        return $this->stmt->execute();
    }

    // Get result set as array of objects
    public function resultset() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get single record as object
    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get row count
    public function rowCount() {
        return $this->stmt->rowCount();
    }

    // Get last insert ID
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }

    // ========================================
    // 🔐 SESSION MANAGEMENT METHODS
    // ========================================

    // Set session variable
    public static function setSession($key, $value) {
        $_SESSION[$key] = $value;
    }

    // Get session variable
    public static function getSession($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    // Check if session exists
    public static function hasSession($key) {
        return isset($_SESSION[$key]);
    }

    // Remove session variable
    public static function removeSession($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    // Login user (set login sessions)
    public static function loginUser($userId, $username, $email = null) {
        self::setSession('user_id', $userId);
        self::setSession('username', $username);
        self::setSession('email', $email);
        self::setSession('login_time', time());
        self::setSession('last_activity', time());
        
        // Regenerate session ID for security
        session_regenerate_id(true);
    }

    // Logout user
    public static function logoutUser() {
        // Remove login sessions
        self::removeSession('user_id');
        self::removeSession('username');
        self::removeSession('email');
        self::removeSession('login_time');
        self::removeSession('last_activity');
        
        // Remove remember me cookie
        self::removeCookie('remember_token');
        
        // Destroy session
        session_destroy();
    }

    // Check session timeout (30 minutes default)
    public static function checkTimeout($timeout = 1800) {
        if (self::isLoggedIn()) {
            $lastActivity = self::getSession('last_activity', 0);
            if ((time() - $lastActivity) > $timeout) {
                self::logoutUser();
                return false;
            }
            // Update last activity
            self::setSession('last_activity', time());
        }
        return self::isLoggedIn();
    }

    // ========================================
    // 🍪 COOKIE MANAGEMENT METHODS
    // ========================================

    // Set cookie
    public static function setCookie($name, $value, $days = 30) {
        $expires = time() + ($days * 24 * 60 * 60);
        return setcookie($name, $value, $expires, '/', '', false, true);
    }

    // Get cookie
    public static function getCookie($name, $default = null) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
    }

    // Check if cookie exists
    public static function hasCookie($name) {
        return isset($_COOKIE[$name]);
    }

    // Remove cookie
    public static function removeCookie($name) {
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
            return setcookie($name, '', time() - 3600, '/');
        }
        return false;
    }

    // Set "Remember Me" functionality
    public static function setRememberMe($userId, $token) {
        $cookieValue = base64_encode($userId . ':' . $token);
        return self::setCookie('remember_token', $cookieValue, 30); // 30 days
    }

    // Get "Remember Me" data
    public static function getRememberMe() {
        $cookie = self::getCookie('remember_token');
        if ($cookie) {
            $decoded = base64_decode($cookie);
            $parts = explode(':', $decoded);
            if (count($parts) === 2) {
                return [
                    'user_id' => $parts[0],
                    'token' => $parts[1]
                ];
            }
        }
        return null;
    }

    // ========================================
    // 💬 FLASH MESSAGES
    // ========================================

    // Set flash message
    public static function setFlash($type, $message) {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = array();
        }
        $_SESSION['flash_messages'][$type] = $message;
    }

    // Get flash message (and remove it)
    public static function getFlash($type) {
        if (isset($_SESSION['flash_messages'][$type])) {
            $message = $_SESSION['flash_messages'][$type];
            unset($_SESSION['flash_messages'][$type]);
            return $message;
        }
        return null;
    }

    // Get all flash messages
    public static function getAllFlash() {
        $messages = isset($_SESSION['flash_messages']) ? $_SESSION['flash_messages'] : array();
        $_SESSION['flash_messages'] = array();
        return $messages;
    }

    // ========================================
    // 🔒 SECURITY HELPERS
    // ========================================

    // Generate secure token
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    // Hash password
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    // Verify password
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // Generate CSRF token
    public static function generateCSRFToken() {
        if (!self::hasSession('csrf_token')) {
            self::setSession('csrf_token', self::generateToken());
        }
        return self::getSession('csrf_token');
    }

    // Verify CSRF token
    public static function verifyCSRFToken($token) {
        $sessionToken = self::getSession('csrf_token');
        return $sessionToken && hash_equals($sessionToken, $token);
    }
}

// Auto-check session timeout on every page load
if (Database::isLoggedIn()) {
    Database::checkTimeout();
}
?>