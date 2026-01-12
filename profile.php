<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - BloodConnect</title>
    
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
        }

        .page-header h1 {
            color: #2c3e50;
            font-size: 1.8rem;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 2rem;
        }

        .profile-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            font-weight: 700;
            margin: 0 auto 1.5rem;
            border: 5px solid #f5f6fa;
            box-shadow: 0 10px 30px rgba(220, 53, 69, 0.2);
        }

        .profile-name {
            font-size: 1.8rem;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .profile-email {
            color: #7f8c8d;
            margin-bottom: 1rem;
        }

        .blood-type-display {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            color: white;
            border-radius: 50px;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 1rem 0;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 2rem;
        }

        .stat-box {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .stat-box h3 {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.3rem;
        }

        .stat-box p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .profile-details {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .profile-details h2 {
            color: #2c3e50;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-item {
            padding: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #7f8c8d;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-label i {
            color: var(--primary-color);
            width: 20px;
        }

        .detail-value {
            color: #2c3e50;
            font-weight: 600;
        }

        .btn {
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            color: white;
            width: 100%;
            margin-top: 1.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-badge.available {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.unavailable {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 968px) {
            .sidebar {
                left: -100%;
            }

            .main-content {
                margin-left: 0;
            }

            .profile-grid {
                grid-template-columns: 1fr;
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
            <li><a href="find-donors.php"><i class="fas fa-search"></i> Find Donors</a></li>
            <li><a href="blood-requests.php"><i class="fas fa-hand-holding-heart"></i> Blood Requests</a></li>
            <li><a href="my-donations.php"><i class="fas fa-history"></i> My Donations</a></li>
            <li><a href="profile.php" class="active"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user-circle"></i> My Profile</h1>
            <p style="color: #7f8c8d; margin-top: 0.5rem;">Manage your personal information and donation settings</p>
        </div>

        <div class="profile-grid">
            <div class="profile-card">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                <div class="profile-name"><?php echo htmlspecialchars($user['name']); ?></div>
                <div class="profile-email"><?php echo htmlspecialchars($user['email']); ?></div>
                <div class="blood-type-display"><?php echo htmlspecialchars($user['blood_type']); ?></div>
                
                <div class="profile-stats">
                    <div class="stat-box">
                        <h3><?php echo $user['total_donations']; ?></h3>
                        <p>Total Donations</p>
                    </div>
                    <div class="stat-box">
                        <h3><?php echo ucfirst($user['role']); ?></h3>
                        <p>Account Type</p>
                    </div>
                </div>

                <a href="edit-profile.php" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
            </div>

            <div class="profile-details">
                <h2><i class="fas fa-info-circle"></i> Personal Information</h2>
                
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-user"></i>
                        Full Name
                    </div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['name']); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['email']); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-phone"></i>
                        Phone Number
                    </div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['phone']); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-tint"></i>
                        Blood Type
                    </div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['blood_type']); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-birthday-cake"></i>
                        Date of Birth
                    </div>
                    <div class="detail-value"><?php echo date('F d, Y', strtotime($user['dob'])); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Address
                    </div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['address']); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-check-circle"></i>
                        Availability Status
                    </div>
                    <div class="detail-value">
                        <span class="status-badge <?php echo $user['available'] ? 'available' : 'unavailable'; ?>">
                            <?php echo $user['available'] ? 'Available for Donation' : 'Currently Unavailable'; ?>
                        </span>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-calendar-alt"></i>
                        Last Donation
                    </div>
                    <div class="detail-value">
                        <?php 
                        if ($user['last_donation_date']) {
                            echo date('F d, Y', strtotime($user['last_donation_date']));
                        } else {
                            echo 'No donations yet';
                        }
                        ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-user-clock"></i>
                        Member Since
                    </div>
                    <div class="detail-value"><?php echo date('F d, Y', strtotime($user['created_at'])); ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>