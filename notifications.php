<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$userId = $_SESSION['user_id'];

// Get all notifications
$stmt = $conn->prepare("
    SELECT * FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 50
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count unread
$unreadCount = 0;
foreach ($notifications as $notif) {
    if (!$notif['is_read']) $unreadCount++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - BloodConnect</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #dc3545;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f6fa;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            padding: 2rem 0;
            color: white;
            z-index: 1000;
        }

        .sidebar-logo {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
        }

        .sidebar-logo h2 {
            font-size: 1.5rem;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar-menu i {
            width: 20px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .unread-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .mark-all-read {
            padding: 0.8rem 1.5rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .mark-all-read:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        .notifications-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .notification-item {
            padding: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            gap: 1.5rem;
            align-items: start;
            transition: all 0.3s;
            cursor: pointer;
        }

        .notification-item:hover {
            background: #f8f9fa;
        }

        .notification-item.unread {
            background: #fff5f5;
            border-left: 4px solid var(--primary-color);
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .notification-icon.request {
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            color: white;
        }

        .notification-icon.system {
            background: linear-gradient(135deg, #3498db 0%, #5dade2 100%);
            color: white;
        }

        .notification-icon.achievement {
            background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
            color: white;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.3rem;
            font-size: 1.05rem;
        }

        .notification-message {
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }

        .notification-time {
            font-size: 0.85rem;
            color: #95a5a6;
        }

        .no-notifications {
            text-align: center;
            padding: 4rem 2rem;
        }

        .no-notifications i {
            font-size: 4rem;
            color: #e0e0e0;
            margin-bottom: 1rem;
        }

        .no-notifications h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .no-notifications p {
            color: #7f8c8d;
        }

        @media (max-width: 968px) {
            .sidebar {
                left: -100%;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h2><i class="fas fa-heartbeat"></i> BloodConnect</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="blood-requests.php"><i class="fas fa-hand-holding-heart"></i> Blood Requests</a></li>
            <li><a href="create-request.php"><i class="fas fa-plus-circle"></i> Create Request</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h1>
                    <i class="fas fa-bell"></i> Notifications
                    <?php if ($unreadCount > 0): ?>
                        <span class="unread-badge"><?php echo $unreadCount; ?> New</span>
                    <?php endif; ?>
                </h1>
                <p style="color: #7f8c8d; margin-top: 0.5rem;">Stay updated with blood donation alerts</p>
            </div>
            <?php if ($unreadCount > 0): ?>
                <button class="mark-all-read" onclick="markAllRead()">
                    <i class="fas fa-check-double"></i> Mark All Read
                </button>
            <?php endif; ?>
        </div>

        <div class="notifications-container">
            <?php if (empty($notifications)): ?>
                <div class="no-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <h3>No Notifications Yet</h3>
                    <p>We'll notify you when there are urgent blood requests or important updates</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notif): ?>
                    <div class="notification-item <?php echo !$notif['is_read'] ? 'unread' : ''; ?>" 
                         onclick="markAsRead(<?php echo $notif['id']; ?>)">
                        <div class="notification-icon <?php echo strtolower($notif['type']); ?>">
                            <i class="fas fa-<?php 
                                echo $notif['type'] === 'request' ? 'hand-holding-heart' : 
                                     ($notif['type'] === 'system' ? 'info-circle' : 'trophy'); 
                            ?>"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title"><?php echo htmlspecialchars($notif['title']); ?></div>
                            <div class="notification-message"><?php echo htmlspecialchars($notif['message']); ?></div>
                            <div class="notification-time">
                                <i class="fas fa-clock"></i> 
                                <?php echo date('M d, Y - g:i A', strtotime($notif['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        function markAsRead(notificationId) {
            $.ajax({
                url: 'mark-notification-read.php',
                type: 'POST',
                data: { notification_id: notificationId },
                success: function() {
                    location.reload();
                }
            });
        }

        function markAllRead() {
            $.ajax({
                url: 'mark-all-notifications-read.php',
                type: 'POST',
                success: function() {
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>