<?php
session_start();
require_once 'config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['success' => false]);
    exit;
}

$userId = $_SESSION['user_id'];
$setting = $_POST['setting'] ?? '';
$value = $_POST['value'] ?? 0;

$allowedSettings = ['email_notifications', 'sms_notifications', 'push_notifications', 'available'];

if (in_array($setting, $allowedSettings)) {
    $stmt = $conn->prepare("UPDATE users SET $setting = ? WHERE id = ?");
    $stmt->bind_param("ii", $value, $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();
}
$conn->close();
?>