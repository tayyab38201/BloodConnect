<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$userId = $_SESSION['user_id'];

// Get user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get donation history
$stmt = $conn->prepare("
    SELECT * FROM donations_history 
    WHERE donor_id = ? 
    ORDER BY donation_date DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$donations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get donation stats
$totalDonations = count($donations);
$totalUnits = 0;
foreach ($donations as $donation) {
    $totalUnits += $donation['units_donated'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Donations - BloodConnect</title>
    
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
            --secondary-color: #ff6b6b;
            --dark-color: #2c3e50;
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
            color: var(--dark-color);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-box {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }

        .stat-box h3 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-box p {
            color: #7f8c8d;
            font-weight: 500;
        }

        .donations-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .donations-header {
            padding: 1.5rem;
            border-bottom: 2px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .donations-header h2 {
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-dropdown {
            padding: 0.6rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
        }

        .donations-timeline {
            padding: 2rem;
        }

        .donation-item {
            position: relative;
            padding-left: 3rem;
            padding-bottom: 2rem;
            border-left: 3px solid #e0e0e0;
        }

        .donation-item:last-child {
            border-left: 3px solid transparent;
            padding-bottom: 0;
        }

        .donation-marker {
            position: absolute;
            left: -12px;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--primary-color);
            border: 4px solid white;
            box-shadow: 0 0 0 3px var(--primary-color);
        }

        .donation-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s;
        }

        .donation-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.15);
        }

        .donation-header-row {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .donation-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.3rem;
        }

        .donation-date {
            color: #7f8c8d;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .blood-badge {
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .donation-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #5a6c7d;
        }

        .detail-item i {
            color: var(--primary-color);
            width: 20px;
        }

        .certificate-btn {
            margin-top: 1rem;
            padding: 0.6rem 1.2rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .certificate-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .no-donations {
            text-align: center;
            padding: 4rem 2rem;
        }

        .no-donations i {
            font-size: 4rem;
            color: #e0e0e0;
            margin-bottom: 1rem;
        }

        .no-donations h3 {
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .no-donations p {
            color: #7f8c8d;
            margin-bottom: 1.5rem;
        }

        .btn-primary {
            padding: 0.8rem 2rem;
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
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
            <li><a href="find-donors.php"><i class="fas fa-search"></i> Find Donors</a></li>
            <li><a href="blood-requests.php"><i class="fas fa-hand-holding-heart"></i> Blood Requests</a></li>
            <li><a href="my-donations.php" class="active"><i class="fas fa-history"></i> My Donations</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-history"></i> My Donation History</h1>
            <p style="color: #7f8c8d; margin-top: 0.5rem;">Track your life-saving contributions</p>
        </div>

        <div class="stats-row">
            <div class="stat-box">
                <h3><?php echo $totalDonations; ?></h3>
                <p>Total Donations</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $totalUnits; ?></h3>
                <p>Units Donated</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $user['blood_type']; ?></h3>
                <p>Blood Type</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $totalDonations * 3; ?></h3>
                <p>Lives Saved</p>
            </div>
        </div>

        <div class="donations-container">
            <div class="donations-header">
                <h2><i class="fas fa-tint"></i> Donation Timeline</h2>
                <select class="filter-dropdown">
                    <option>All Time</option>
                    <option>Last 6 Months</option>
                    <option>Last Year</option>
                    <option>Last 2 Years</option>
                </select>
            </div>

            <?php if (empty($donations)): ?>
                <div class="no-donations">
                    <i class="fas fa-tint-slash"></i>
                    <h3>No Donations Yet</h3>
                    <p>You haven't made any blood donations yet. Start saving lives today!</p>
                    <a href="blood-requests.php" class="btn-primary">
                        <i class="fas fa-hand-holding-heart"></i> View Blood Requests
                    </a>
                </div>
            <?php else: ?>
                <div class="donations-timeline">
                    <?php foreach ($donations as $donation): ?>
                        <div class="donation-item">
                            <div class="donation-marker"></div>
                            <div class="donation-card">
                                <div class="donation-header-row">
                                    <div>
                                        <div class="donation-title">
                                            Blood Donation #<?php echo $donation['id']; ?>
                                        </div>
                                        <div class="donation-date">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('F d, Y', strtotime($donation['donation_date'])); ?>
                                        </div>
                                    </div>
                                    <div class="blood-badge">
                                        <?php echo $donation['blood_type']; ?>
                                    </div>
                                </div>

                                <div class="donation-details">
                                    <div class="detail-item">
                                        <i class="fas fa-tint"></i>
                                        <span><strong><?php echo $donation['units_donated']; ?> Units</strong> Donated</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-hospital"></i>
                                        <span><?php echo htmlspecialchars($donation['hospital_name']); ?></span>
                                    </div>
                                    <?php if ($donation['hemoglobin_level']): ?>
                                        <div class="detail-item">
                                            <i class="fas fa-heartbeat"></i>
                                            <span>Hb: <?php echo $donation['hemoglobin_level']; ?> g/dL</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($donation['blood_pressure']): ?>
                                        <div class="detail-item">
                                            <i class="fas fa-stethoscope"></i>
                                            <span>BP: <?php echo $donation['blood_pressure']; ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($donation['notes']): ?>
                                    <div class="detail-item" style="margin-top: 1rem;">
                                        <i class="fas fa-notes-medical"></i>
                                        <span><?php echo htmlspecialchars($donation['notes']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <button class="certificate-btn" onclick="downloadCertificate(<?php echo $donation['id']; ?>)">
                                    <i class="fas fa-download"></i>
                                    Download Certificate
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        function downloadCertificate(donationId) {
            window.open('download-certificate.php?id=' + donationId, '_blank');
        }

        $('.filter-dropdown').change(function() {
            const filter = $(this).val();
            // Implement filtering logic here
            console.log('Filter:', filter);
        });
    </script>
</body>
</html>