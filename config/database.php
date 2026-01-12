<?php
/**
 * BloodConnect - Database Configuration
 * MySQL database connection using mysqli
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Default XAMPP password is empty
define('DB_NAME', 'bloodconnect');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Set charset to utf8mb4 for emoji support
$conn->set_charset("utf8mb4");

// Optional: Set timezone
date_default_timezone_set('America/New_York');

/**
 * Helper function to execute prepared statements safely
 */
function executeQuery($conn, $query, $types = "", $params = []) {
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        return [
            'success' => false,
            'message' => 'Query preparation failed: ' . $conn->error
        ];
    }
    
    if (!empty($types) && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $stmt->close();
        
        return [
            'success' => true,
            'result' => $result
        ];
    } else {
        $error = $stmt->error;
        $stmt->close();
        
        return [
            'success' => false,
            'message' => 'Query execution failed: ' . $error
        ];
    }
}
?>