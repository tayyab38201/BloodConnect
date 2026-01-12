<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

// Determine user identifier from session
$sessionUserIdentifier = $_SESSION['user_id']; // could be numeric id OR the varchar user_id (e.g., BC6963...)

// Fetch user record robustly (support numeric id or user_id string)
if (is_numeric($sessionUserIdentifier)) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $sessionUserIdentifier);
} else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $sessionUserIdentifier);
}

$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// If no user found, force logout (session stale)
if (!$user) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

// Now we have the numeric id for references
$numericUserId = (int)$user['id'];

// Get user's donation count (use numeric id from fetched user)
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM donations_history WHERE donor_id = ?");
$stmt->bind_param("i", $numericUserId);
$stmt->execute();
$donationCount = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

// Get active blood requests
// Note: include requests where expires_at is not set (0000-00-00 00:00:00) OR expires_at > NOW()
$stmt = $conn->prepare("
    SELECT * FROM blood_requests 
    WHERE status = 'pending' 
    AND (expires_at = '0000-00-00 00:00:00' OR expires_at > NOW())
    ORDER BY 
      CASE urgency WHEN 'critical' THEN 3 WHEN 'urgent' THEN 2 ELSE 1 END DESC,
      created_at DESC 
    LIMIT 6
");
$stmt->execute();
$activeRequests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count total active requests
$totalActiveRequestsRow = $conn->query("SELECT COUNT(*) as count FROM blood_requests WHERE status = 'pending' AND (expires_at = '0000-00-00 00:00:00' OR expires_at > NOW())")->fetch_assoc();
$totalActiveRequests = $totalActiveRequestsRow ? $totalActiveRequestsRow['count'] : 0;

// Unread notifications count (for badge)
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->bind_param("i", $numericUserId);
$stmt->execute();
$notificationBadge = $stmt->get_result()->fetch_assoc()['cnt'];
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BloodConnect</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* (same CSS as before) */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #dc3545;
            --secondary-color: #ff6b6b;
            --dark-color: #2c3e50;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #3498db;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f6fa;
        }

        /* Sidebar */
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
            transition: all 0.3s;
        }

        .sidebar-logo {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
        }

        .sidebar-logo h2 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            color: white;
        }

        .sidebar-menu i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        /* Top Bar */
        .topbar {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .topbar h1 {
            color: var(--dark-color);
            font-size: 1.8rem;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification-icon {
            position: relative;
            cursor: pointer;
            font-size: 1.3rem;
            color: var(--dark-color);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--primary-color);
            color: white;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 50%;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            transition: background 0.3s;
        }

        .user-profile:hover {
            background: #f5f6fa;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-info h3 {
            font-size: 2rem;
            margin-bottom: 0.3rem;
            color: var(--dark-color);
        }

        .stat-info p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
        }

        .stat-icon.red { background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%); }
        .stat-icon.green { background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); }
        .stat-icon.blue { background: linear-gradient(135deg, #3498db 0%, #5dade2 100%); }
        .stat-icon.orange { background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%); }

        /* Quick Actions */
        .quick-actions {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .quick-actions h2 {
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .action-btn {
            padding: 1.5rem;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: var(--dark-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.8rem;
        }

        .action-btn:hover {
            border-color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.2);
        }

        .action-btn i {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .action-btn span {
            font-weight: 500;
        }

        /* Blood Requests */
        .requests-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .requests-section h2 {
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .view-all {
            font-size: 0.9rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .requests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .request-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
        }

        .request-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .blood-type-badge {
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .urgency-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .urgency-badge.critical {
            background: #fee;
            color: #c00;
        }

        .urgency-badge.urgent {
            background: #fff3cd;
            color: #856404;
        }

        .urgency-badge.routine {
            background: #d1ecf1;
            color: #0c5460;
        }

        .request-info {
            margin-bottom: 1rem;
        }

        .request-info p {
            color: #7f8c8d;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .request-info i {
            color: var(--primary-color);
            width: 20px;
        }

        .request-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-outline {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .sidebar {
                left: -100%;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: block;
                position: fixed;
                bottom: 2rem;
                right: 2rem;
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
                color: white;
                border: none;
                border-radius: 50%;
                font-size: 1.5rem;
                cursor: pointer;
                box-shadow: 0 5px 20px rgba(220, 53, 69, 0.4);
                z-index: 999;
            }
        }

        .mobile-menu-btn {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h2><i class="fas fa-heartbeat"></i> BloodConnect</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="blood-requests.php"><i class="fas fa-hand-holding-heart"></i> Blood Requests</a></li>
            <li><a href="create-request.php"><i class="fas fa-plus-circle"></i> Create Request</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>! 👋</h1>
            <div class="user-menu">
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge"><?php echo (int)$notificationBadge; ?></span>
                </div>
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($user['name']); ?></div>
                        <div style="font-size: 0.75rem; color: #7f8c8d;"><?php echo ucfirst($user['role']); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $donationCount; ?></h3>
                    <p>Total Donations</p>
                </div>
                <div class="stat-icon red">
                    <i class="fas fa-tint"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $totalActiveRequests; ?></h3>
                    <p>Active Requests</p>
                </div>
                <div class="stat-icon blue">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $user['blood_type']; ?></h3>
                    <p>Your Blood Type</p>
                </div>
                <div class="stat-icon green">
                    <i class="fas fa-heartbeat"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $user['available'] ? 'Available' : 'Unavailable'; ?></h3>
                    <p>Donation Status</p>
                </div>
                <div class="stat-icon orange">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
            <div class="actions-grid">
                <a href="create-request.php" class="action-btn">
                    <i class="fas fa-plus-circle"></i>
                    <span>Create Blood Request</span>
                </a>
                <a href="blood-requests.php" class="action-btn">
                    <i class="fas fa-search"></i>
                    <span>Find Donors</span>
                </a>
                <a href="profile.php" class="action-btn">
                    <i class="fas fa-user-edit"></i>
                    <span>Update Profile</span>
                </a>
                <a href="blood-requests.php" class="action-btn">
                    <i class="fas fa-calendar-alt"></i>
                    <span>View All Requests</span>
                </a>
            </div>
        </div>

        <!-- Active Blood Requests -->
        <div class="requests-section">
            <h2>
                <span><i class="fas fa-hand-holding-heart"></i> Active Blood Requests</span>
                <a href="blood-requests.php" class="view-all">View All →</a>
            </h2>
            <div class="requests-grid">
                <?php if (empty($activeRequests)): ?>
                    <p style="color: #7f8c8d; text-align: center; padding: 2rem; grid-column: 1/-1;">No active blood requests at the moment.</p>
                <?php else: ?>
                    <?php foreach ($activeRequests as $request): ?>
                        <div class="request-card">
                            <div class="request-header">
                                <div class="blood-type-badge"><?php echo htmlspecialchars($request['blood_type']); ?></div>
                                <span class="urgency-badge <?php echo strtolower($request['urgency']); ?>">
                                    <?php echo htmlspecialchars($request['urgency']); ?>
                                </span>
                            </div>
                            <div class="request-info">
                                <p><i class="fas fa-user"></i> <strong><?php echo htmlspecialchars($request['patient_name']); ?></strong></p>
                                <p><i class="fas fa-hospital"></i> <?php echo htmlspecialchars($request['hospital_name']); ?></p>
                                <p><i class="fas fa-tint"></i> <?php echo htmlspecialchars($request['units_needed']); ?> units needed</p>
                                <p><i class="fas fa-clock"></i> Posted <?php echo date('M d, Y', strtotime($request['created_at'])); ?></p>
                            </div>
                            <div class="request-actions">
                                <a href="blood-requests.php" class="btn btn-outline">View Details</a>
                                <a href="blood-requests.php" class="btn btn-primary">Respond</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        function toggleSidebar() {
            $('.sidebar').toggleClass('active');
        }

        $(document).click(function(e) {
            if ($(window).width() <= 968) {
                if (!$(e.target).closest('.sidebar, .mobile-menu-btn').length) {
                    $('.sidebar').removeClass('active');
                }
            }
        });
    </script>
</body>
</html>