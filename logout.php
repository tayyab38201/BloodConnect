<?php
session_start();

require_once 'config/database.php';

// Log logout if user is logged in
if (isset($_SESSION['user_unique_id'])) {
    $userId = $_SESSION['user_unique_id'];
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $conn->prepare("
        INSERT INTO activity_logs (user_id, action, ip_address, created_at) 
        VALUES (?, 'User logged out', ?, NOW())
    ");
    $stmt->bind_param("ss", $userId, $ipAddress);
    $stmt->execute();
    $stmt->close();
}

// Destroy session
session_unset();
session_destroy();

$conn->close();

// Redirect to login
header('Location: login.php?logout=success');
exit;
?>