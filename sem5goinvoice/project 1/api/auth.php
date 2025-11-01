<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to user
ini_set('log_errors', 1);

// Start output buffering to catch any unexpected output
ob_start();

require_once '../config/database.php';

// Handle CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    ob_clean();
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $db = getDB();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    switch ($action) {
        case 'login':
            handleLogin($db);
            break;
        case 'signup':
            handleSignup($db);
            break;
        case 'logout':
            handleLogout();
            break;
        case 'check_session':
            checkSession($db);
            break;
        default:
            sendError('Invalid action');
    }
} catch (Exception $e) {
    // Clean any output buffer and send proper JSON error
    ob_clean();
    sendError('Server error: ' . $e->getMessage(), 500);
}

function handleLogin($db) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed');
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        sendError('Invalid JSON data');
    }
    
    if (!isset($data['email']) || !isset($data['password'])) {
        sendError('Email and password are required');
    }
    
    $email = trim($data['email']);
    $password = $data['password'];
    
    $stmt = $db->prepare("SELECT id, name, email, password, business_name, status FROM users WHERE email = ? AND status = 'active'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Start session
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['business_name'] = $user['business_name'];
        
        ob_clean();
        sendSuccess([
            'user_id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'business_name' => $user['business_name']
        ], 'Login successful');
    } else {
        sendError('Invalid email or password', 401);
    }
}

function handleSignup($db) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed');
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        sendError('Invalid JSON data');
    }
    
    // Validate required fields
    $required_fields = ['name', 'email', 'mobile', 'password'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            sendError("Field '$field' is required");
        }
    }
    
    $name = trim($data['name']);
    $email = trim($data['email']);
    $mobile = trim($data['mobile']);
    $password = $data['password'];
    $business_name = trim($data['business_name'] ?? '');
    $gst_number = trim($data['gst_number'] ?? '');
    $address = trim($data['address'] ?? '');
    $city = trim($data['city'] ?? '');
    $state = trim($data['state'] ?? '');
    $pincode = trim($data['pincode'] ?? '');
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('Invalid email format');
    }
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        sendError('Email already exists');
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $db->prepare("
        INSERT INTO users (name, email, mobile, password, business_name, gst_number, address, city, state, pincode) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $name, $email, $mobile, $hashed_password, $business_name, 
        $gst_number, $address, $city, $state, $pincode
    ]);
    
    if ($result) {
        $user_id = $db->lastInsertId();
        
        // Insert default settings
        $settings = [
            ['invoice_prefix', 'INV'],
            ['invoice_start_number', '1001'],
            ['currency', 'INR'],
            ['timezone', 'Asia/Kolkata']
        ];
        
        $stmt = $db->prepare("INSERT INTO user_settings (user_id, setting_key, setting_value) VALUES (?, ?, ?)");
        foreach ($settings as $setting) {
            $stmt->execute([$user_id, $setting[0], $setting[1]]);
        }
        
        // Clean output buffer before sending response
        ob_clean();
        sendSuccess(['user_id' => $user_id], 'Registration successful');
    } else {
        sendError('Registration failed');
    }
}

function handleLogout() {
    session_start();
    session_destroy();
    ob_clean();
    sendSuccess([], 'Logout successful');
}

function checkSession($db) {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        sendError('Not logged in', 401);
    }
    
    // Verify user still exists and is active
    $stmt = $db->prepare("SELECT id, name, email, business_name FROM users WHERE id = ? AND status = 'active'");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        session_destroy();
        sendError('Session invalid', 401);
    }
    
    ob_clean();
    sendSuccess([
        'user_id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'business_name' => $user['business_name']
    ], 'Session valid');
}
?>
