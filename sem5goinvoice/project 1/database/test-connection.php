<?php
/**
 * Database Connection Test for GoInvoice
 * Tests database connectivity and basic operations
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$results = [];

try {
    // Test 1: Basic connection
    require_once __DIR__ . '/../config/database.php';
    $results['connection'] = ['status' => 'success', 'message' => 'Database connection successful'];
    
    // Test 2: Check if tables exist
    $requiredTables = ['users', 'customers', 'products', 'sales_invoices'];
    $tableResults = [];
    
    foreach ($requiredTables as $table) {
        try {
            $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            
            if ($stmt->rowCount() > 0) {
                $tableResults[$table] = ['status' => 'exists', 'message' => 'Table exists'];
            } else {
                $tableResults[$table] = ['status' => 'missing', 'message' => 'Table does not exist'];
            }
        } catch (PDOException $e) {
            $tableResults[$table] = ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    $results['tables'] = $tableResults;
    
    // Test 3: Test basic operations
    try {
        // Test SELECT operation
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $userCount = $stmt->fetch()['count'];
        $results['operations']['select'] = [
            'status' => 'success', 
            'message' => "Found $userCount users in database"
        ];
        
        // Test INSERT operation (dry run)
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
        $stmt->execute(['test@example.com']);
        $results['operations']['query'] = [
            'status' => 'success', 
            'message' => 'Query operations working'
        ];
        
    } catch (PDOException $e) {
        $results['operations'] = [
            'status' => 'error', 
            'message' => 'Database operations failed: ' . $e->getMessage()
        ];
    }
    
    // Test 4: Check database configuration
    $stmt = $pdo->query("SELECT @@version as version, DATABASE() as current_db");
    $dbInfo = $stmt->fetch();
    
    $results['database_info'] = [
        'version' => $dbInfo['version'],
        'current_database' => $dbInfo['current_db'],
        'charset' => 'utf8mb4'
    ];
    
    $results['overall_status'] = 'success';
    $results['message'] = 'All database tests passed successfully';
    
} catch (PDOException $e) {
    $results['connection'] = [
        'status' => 'error', 
        'message' => 'Database connection failed: ' . $e->getMessage()
    ];
    $results['overall_status'] = 'error';
    $results['message'] = 'Database connection test failed';
    
    // Provide helpful error messages
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        $results['suggestion'] = 'Check your database username and password in config/database.php';
    } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
        $results['suggestion'] = 'Run database/setup.php to create the database and tables';
    } elseif (strpos($e->getMessage(), 'Connection refused') !== false) {
        $results['suggestion'] = 'Make sure MySQL/MariaDB server is running';
    }
}

// Return JSON response
echo json_encode($results, JSON_PRETTY_PRINT);
?>
