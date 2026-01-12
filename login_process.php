<?php
/**
 * BloodConnect - Login Process
 * Handles user authentication
 */

// Start session
session_start();

// Database configuration
require_once 'config/database.php';

// Set JSON header
header('Content-Type: application/json');

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'role' => ''
];

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Get and sanitize inputs
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']) && $_POST['remember'] === 'true';

// Validate inputs
if (empty($email) || empty($password)) {
    $response['message'] = 'Email and password are required';
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Invalid email address';
    echo json_encode($response);
    exit;
}

try {
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("
        SELECT id, user_id, name, email, password_hash, role, status, email_verified 
        FROM users 
        WHERE email = ? 
        LIMIT 1
    ");
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['message'] = 'Invalid email or password';
        echo json_encode($response);
        exit;
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        $response['message'] = 'Invalid email or password';
        
        // Log failed attempt
        logActivity($conn, $email, 'Failed login attempt', $_SERVER['REMOTE_ADDR']);
        
        echo json_encode($response);
        exit;
    }
    
    // Check account status
    if ($user['status'] === 'inactive') {
        $response['message'] = 'Your account is inactive. Please contact support.';
        echo json_encode($response);
        exit;
    }
    
    if ($user['status'] === 'suspended') {
        $response['message'] = 'Your account has been suspended. Please contact support.';
        echo json_encode($response);
        exit;
    }
    
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_unique_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();
    
    // Set remember me cookie (30 days)
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + (30 * 24 * 60 * 60); // 30 days
        
        setcookie('remember_token', $token, $expiry, '/', '', true, true);
        
        // Store token in database
        $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
        $hashedToken = password_hash($token, PASSWORD_BCRYPT);
        $stmt->bind_param("si", $hashedToken, $user['id']);
        $stmt->execute();
        $stmt->close();
    }
    
    // Update last login
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $stmt->close();
    
    // Log successful login
    logActivity($conn, $user['user_id'], 'Successful login', $_SERVER['REMOTE_ADDR']);
    
    // Success response
    $response['success'] = true;
    $response['message'] = 'Login successful!';
    $response['role'] = $user['role'];
    $response['user_name'] = $user['name'];
    $response['redirect'] = $user['role'] === 'admin' ? 'admin/dashboard.php' : 'dashboard.php';
    
} catch (Exception $e) {
    $response['message'] = 'An error occurred. Please try again.';
    error_log('Login error: ' . $e->getMessage());
}

// Close database connection
$conn->close();

// Return JSON response
echo json_encode($response);

/**
 * Log user activity
 */
function logActivity($conn, $userId, $action, $ipAddress) {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    $stmt = $conn->prepare("
        INSERT INTO activity_logs (user_id, action, ip_address, user_agent, created_at) 
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("ssss", $userId, $action, $ipAddress, $userAgent);
    $stmt->execute();
    $stmt->close();
}
?>