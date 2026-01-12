<?php
/**
 * FILE 4: mark-all-notifications-read.php
 * Mark all notifications as read
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['logged_in'])) {
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->close();
$conn->close();
?>

<!-- SAVE AS: mark-all-notifications-read.php -->