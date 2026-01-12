<?php
/**
 * FILE 3: mark-notification-read.php
 * Mark single notification as read
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['logged_in'])) {
    exit;
}

$userId = $_SESSION['user_id'];
$notificationId = $_POST['notification_id'] ?? 0;

$stmt = $conn->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $notificationId, $userId);
$stmt->execute();
$stmt->close();
$conn->close();
?>

<!-- SAVE AS: mark-notification-read.php -->

