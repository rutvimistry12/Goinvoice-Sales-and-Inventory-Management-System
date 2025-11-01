<?php
require_once '../config/database.php';

// Headers (support credentials for session-based auth)
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (!empty($origin)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit(0);
}

// Ensure errors are not echoed into JSON responses
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// JSON helpers (local to this endpoint)
if (!function_exists('sendResponse')) {
    function sendResponse($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
if (!function_exists('sendError')) {
    function sendError($message, $status = 400) {
        sendResponse(['success' => false, 'error' => $message], $status);
    }
}
if (!function_exists('sendSuccess')) {
    function sendSuccess($data = [], $message = 'Success') {
        sendResponse(['success' => true, 'message' => $message, 'data' => $data]);
    }
}

// Ensure access to PDO instance from config/database.php
if (!function_exists('getDB')) {
    function getDB() {
        global $pdo;
        if (!$pdo) {
            sendError('Database not initialized', 500);
        }
        return $pdo;
    }
}

// Check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    sendError('Authentication required', 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$user_id = $_SESSION['user_id'];

try {
    $db = getDB();
    
    switch ($method) {
        case 'GET':
            handleGetCustomers($db, $user_id);
            break;
        case 'POST':
            handleCreateCustomer($db, $user_id);
            break;
        case 'PUT':
            handleUpdateCustomer($db, $user_id);
            break;
        case 'DELETE':
            handleDeleteCustomer($db, $user_id);
            break;
        default:
            sendError('Method not allowed');
    }
} catch (Exception $e) {
    sendError('Server error: ' . $e->getMessage(), 500);
}

function handleGetCustomers($db, $user_id) {
    try {
        $type = $_GET['type'] ?? 'all'; // customer, vendor, or all
        $search = $_GET['search'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = max(1, min(100, (int)($_GET['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;

        $where_conditions = ["user_id = ?"];
        $params = [$user_id];

        if ($type !== 'all') {
            $where_conditions[] = "type = ?";
            $params[] = $type;
        }

        if (!empty($search)) {
            $where_conditions[] = "(name LIKE ? OR email LIKE ? OR mobile LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
        }

        $where_clause = implode(' AND ', $where_conditions);

        // Get total count (use fetchColumn for safety)
        $count_sql = "SELECT COUNT(*) FROM customers WHERE $where_clause";
        $stmt = $db->prepare($count_sql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        // Get customers. Avoid binding LIMIT/OFFSET on some drivers; they require literals
        $sql = "SELECT * FROM customers WHERE $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $customers = $stmt->fetchAll();

        sendSuccess([
            'customers' => $customers,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => (int)ceil($total / $limit)
            ]
        ]);
    } catch (Throwable $e) {
        sendError('Failed to fetch customers: ' . $e->getMessage(), 500);
    }
}

function handleCreateCustomer($db, $user_id) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            sendError('Invalid JSON');
        }

        $required_fields = ['name', 'type'];
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                sendError("Field '$field' is required");
            }
        }

        $name = trim($data['name']);
        $email = trim($data['email'] ?? '');
        $mobile = trim($data['mobile'] ?? '');
        $gst_number = trim($data['gst_number'] ?? '');
        $address = trim($data['address'] ?? '');
        $city = trim($data['city'] ?? '');
        $state = trim($data['state'] ?? '');
        $pincode = trim($data['pincode'] ?? '');
        $type = $data['type'];

        // New optional fields
        $company_name   = trim($data['company_name'] ?? '');
        $is_registered  = isset($data['is_registered']) ? (int)!!$data['is_registered'] : 0; // 0/1
        $pan_number     = trim($data['pan_number'] ?? '');
        $opening_balance= is_numeric($data['opening_balance'] ?? null) ? (float)$data['opening_balance'] : 0.0;
        $custom_fields  = isset($data['custom_fields']) ? json_encode($data['custom_fields']) : null; // store JSON string
        $fax            = trim($data['fax'] ?? '');
        $website        = trim($data['website'] ?? '');
        $credit_limit   = is_numeric($data['credit_limit'] ?? null) ? (float)$data['credit_limit'] : 0.0;
        $credit_due_date= trim($data['credit_due_date'] ?? ''); // YYYY-MM-DD
        $note           = trim($data['note'] ?? '');

        if (!in_array($type, ['customer', 'vendor'])) {
            sendError('Type must be either customer or vendor');
        }

        // created_at handled by DB default if exists; if not, insert still works
        $stmt = $db->prepare(
            "INSERT INTO customers (
                user_id, name, email, mobile, gst_number, address, city, state, pincode, type,
                company_name, is_registered, pan_number, opening_balance, custom_fields, fax, website,
                credit_limit, credit_due_date, note
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )"
        );

        $result = $stmt->execute([
            $user_id, $name, $email, $mobile, $gst_number,
            $address, $city, $state, $pincode, $type,
            $company_name, $is_registered, $pan_number, $opening_balance, $custom_fields, $fax, $website,
            $credit_limit, $credit_due_date ?: null, $note
        ]);

        if ($result) {
            $customer_id = $db->lastInsertId();
            sendSuccess(['customer_id' => $customer_id], 'Customer created successfully');
        } else {
            sendError('Failed to create customer');
        }
    } catch (Throwable $e) {
        sendError('Failed to create customer: ' . $e->getMessage(), 500);
    }
}

function handleUpdateCustomer($db, $user_id) {
    $customer_id = $_GET['id'] ?? '';
    if (empty($customer_id)) {
        sendError('Customer ID is required');
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Check if customer belongs to user
    $stmt = $db->prepare("SELECT id FROM customers WHERE id = ? AND user_id = ?");
    $stmt->execute([$customer_id, $user_id]);
    if (!$stmt->fetch()) {
        sendError('Customer not found');
    }
    
    $fields = ['name', 'email', 'mobile', 'gst_number', 'address', 'city', 'state', 'pincode', 'type'];
    $update_fields = [];
    $params = [];
    
    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $update_fields[] = "$field = ?";
            $params[] = trim($data[$field]);
        }
    }
    
    if (empty($update_fields)) {
        sendError('No fields to update');
    }
    
    $params[] = $customer_id;
    $params[] = $user_id;
    
    $sql = "UPDATE customers SET " . implode(', ', $update_fields) . " WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($sql);
    $result = $stmt->execute($params);
    
    if ($result) {
        sendSuccess([], 'Customer updated successfully');
    } else {
        sendError('Failed to update customer');
    }
}

function handleDeleteCustomer($db, $user_id) {
    $customer_id = $_GET['id'] ?? '';
    if (empty($customer_id)) {
        sendError('Customer ID is required');
    }
    
    // Check if customer belongs to user
    $stmt = $db->prepare("SELECT id FROM customers WHERE id = ? AND user_id = ?");
    $stmt->execute([$customer_id, $user_id]);
    if (!$stmt->fetch()) {
        sendError('Customer not found');
    }
    
    $stmt = $db->prepare("DELETE FROM customers WHERE id = ? AND user_id = ?");
    $result = $stmt->execute([$customer_id, $user_id]);
    
    if ($result) {
        sendSuccess([], 'Customer deleted successfully');
    } else {
        sendError('Failed to delete customer');
    }
}
?>
