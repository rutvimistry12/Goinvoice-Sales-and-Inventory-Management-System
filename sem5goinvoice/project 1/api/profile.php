<?php
require_once __DIR__ . '/../config/database.php';

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

// Ensure we have a getDB() helper compatible with other endpoints
if (!function_exists('getDB')) {
  function getDB() {
    global $pdo;
    if (!$pdo) {
      header('Content-Type: application/json');
      http_response_code(500);
      echo json_encode(['success' => false, 'error' => 'Database not initialized']);
      exit;
    }
    return $pdo;
  }
}

session_start();
if (!isset($_SESSION['user_id'])) {
  header('Content-Type: application/json');
  echo json_encode(['success' => false, 'error' => 'Authentication required']);
  exit;
}

$user_id = (int)$_SESSION['user_id'];
$action = $_GET['action'] ?? 'get_profile';

function json_ok($data = [], $message = 'OK') {
  header('Content-Type: application/json');
  echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
  exit;
}
function json_err($message, $code = 400) {
  header('Content-Type: application/json');
  http_response_code($code);
  echo json_encode(['success' => false, 'error' => $message]);
  exit;
}

try {
  $db = getDB();
  if (!$db) json_err('DB connection failed', 500);

  // Ensure profile & bank tables exist (idempotent for safety)
  $db->exec("CREATE TABLE IF NOT EXISTS user_profiles (
    user_id INT PRIMARY KEY,
    logo_path VARCHAR(255) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  ) ENGINE=InnoDB");

  $db->exec("CREATE TABLE IF NOT EXISTS user_bank_details (
    user_id INT PRIMARY KEY,
    account_holder VARCHAR(100) DEFAULT NULL,
    bank_name VARCHAR(100) DEFAULT NULL,
    account_number VARCHAR(50) DEFAULT NULL,
    ifsc VARCHAR(20) DEFAULT NULL,
    upi_id VARCHAR(100) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  ) ENGINE=InnoDB");

  switch ($action) {
    case 'get_profile': {
      $stmt = $db->prepare('SELECT p.logo_path, b.account_holder, b.bank_name, b.account_number, b.ifsc, b.upi_id FROM user_profiles p 
        LEFT JOIN user_bank_details b ON p.user_id = b.user_id WHERE p.user_id = ?');
      $stmt->execute([$user_id]);
      $row = $stmt->fetch();
      // If profile row missing, create it lazily
      if (!$row) {
        $db->prepare('INSERT IGNORE INTO user_profiles (user_id) VALUES (?)')->execute([$user_id]);
        $row = ['logo_path' => null, 'account_holder' => null, 'bank_name' => null, 'account_number' => null, 'ifsc' => null, 'upi_id' => null];
      }
      json_ok($row);
    }
    case 'upload_logo': {
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed', 405);
      if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
        json_err('No file uploaded or upload error');
      }
      $file = $_FILES['logo'];
      $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
      if (!isset($allowed[$file['type']])) json_err('Only JPG, PNG, WEBP allowed');

      $ext = $allowed[$file['type']];
      $targetDir = __DIR__ . '/../uploads/logos';
      if (!is_dir($targetDir)) @mkdir($targetDir, 0777, true);

      $filename = 'logo_user_' . $user_id . '_' . time() . '.' . $ext;
      $targetPath = $targetDir . '/' . $filename;
      if (!move_uploaded_file($file['tmp_name'], $targetPath)) json_err('Failed to save file', 500);

      // Store relative web path
      $webPath = '../uploads/logos/' . $filename;
      $db->prepare('INSERT INTO user_profiles (user_id, logo_path) VALUES (?, ?) ON DUPLICATE KEY UPDATE logo_path = VALUES(logo_path)')
         ->execute([$user_id, $webPath]);

      json_ok(['logo_path' => $webPath], 'Logo updated');
    }
    case 'update_bank': {
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed', 405);
      $payload = json_decode(file_get_contents('php://input'), true);
      if (!is_array($payload)) json_err('Invalid JSON');
      $account_holder = trim($payload['account_holder'] ?? '');
      $bank_name = trim($payload['bank_name'] ?? '');
      $account_number = trim($payload['account_number'] ?? '');
      $ifsc = trim($payload['ifsc'] ?? '');
      $upi_id = trim($payload['upi_id'] ?? '');

      $db->prepare('INSERT INTO user_bank_details (user_id, account_holder, bank_name, account_number, ifsc, upi_id)
        VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE 
        account_holder = VALUES(account_holder), bank_name = VALUES(bank_name), account_number = VALUES(account_number), ifsc = VALUES(ifsc), upi_id = VALUES(upi_id)')
        ->execute([$user_id, $account_holder, $bank_name, $account_number, $ifsc, $upi_id]);

      json_ok(['saved' => true], 'Bank details saved');
    }
    default:
      json_err('Invalid action');
  }
} catch (Exception $e) {
  json_err('Server error: ' . $e->getMessage(), 500);
}
