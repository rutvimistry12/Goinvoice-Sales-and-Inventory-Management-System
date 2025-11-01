<?php
/**
 * Database Setup and Validation Script for GoInvoice
 * This script creates the database and tables if they don't exist
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'localhost';
$db   = 'goinvoice_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// First, connect without specifying database to create it
try {
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "Connected to MySQL server successfully.\n";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET $charset COLLATE {$charset}_unicode_ci");
    echo "Database '$db' created or already exists.\n";
    
    // Now connect to the specific database
    $pdo->exec("USE `$db`");
    echo "Using database '$db'.\n";
    
    // Read and execute schema
    $schemaFile = __DIR__ . '/schema.sql';
    if (file_exists($schemaFile)) {
        $schema = file_get_contents($schemaFile);
        
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignore table already exists errors
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "Warning: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
        
        echo "Database schema executed successfully.\n";
    } else {
        echo "Schema file not found at: $schemaFile\n";
    }
    
    // Validate database structure
    validateDatabaseStructure($pdo);
    
    echo "Database setup completed successfully!\n";
    
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage() . "\n");
}

function validateDatabaseStructure($pdo) {
    $requiredTables = [
        'users',
        'customers', 
        'products',
        'sales_invoices',
        'sales_invoice_items',
        'purchase_invoices',
        'purchase_invoice_items',
        'payments',
        'user_settings'
    ];
    
    echo "Validating database structure...\n";
    
    foreach ($requiredTables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        
        if ($stmt->rowCount() > 0) {
            echo "✓ Table '$table' exists\n";
        } else {
            echo "✗ Table '$table' missing\n";
        }
    }
    
    // Check if users table has correct structure
    try {
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $requiredColumns = ['id', 'name', 'email', 'mobile', 'password'];
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (empty($missingColumns)) {
            echo "✓ Users table structure is correct\n";
        } else {
            echo "✗ Users table missing columns: " . implode(', ', $missingColumns) . "\n";
        }
    } catch (PDOException $e) {
        echo "✗ Could not validate users table structure\n";
    }
}

// If running from command line, execute immediately
if (php_sapi_name() === 'cli') {
    echo "Running database setup from command line...\n";
}
?>
