<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}
require_once '../config/database.php';

// Stats Queries
$totalDonors = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'donor'")->fetch_assoc()['count'];
$totalReq = $conn->query("SELECT COUNT(*) as count FROM blood_requests")->fetch_assoc()['count'];
$totalDonations = $conn->query("SELECT COUNT(*) as count FROM donations_history")->fetch_assoc()['count'];
$pendingReq = $conn->query("SELECT COUNT(*) as count FROM blood_requests WHERE status = 'pending'")->fetch_assoc()['count'];

$recentUsers = $conn->query("SELECT * FROM users WHERE role = 'donor' ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Admin Dashboard | BloodConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #e63946;
            --secondary: #1d3557;
            --accent: #457b9d;
            --background: #f8f9fc;
            --sidebar-color: #111827;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--background); display: flex; min-height: 100vh; }

        /* Modern Sidebar */
        .sidebar {
            width: 280px;
            background: var(--sidebar-color);
            color: #fff;
            position: fixed;
            height: 100vh;
            padding: 30px 20px;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .logo { font-size: 24px; font-weight: 700; color: var(--primary); margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }

        .nav-links a {
            display: flex; align-items: center; color: #9ca3af; padding: 14px 18px;
            text-decoration: none; border-radius: 12px; margin-bottom: 8px; transition: 0.3s;
        }

        .nav-links a:hover, .nav-links a.active { background: rgba(230, 57, 70, 0.15); color: #fff; }
        .nav-links a i { font-size: 20px; margin-right: 15px; }
        .nav-links a.active i { color: var(--primary); }

        /* Main Content */
        .main { margin-left: 280px; width: calc(100% - 280px); padding: 40px; }
        
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .welcome-text h1 { font-size: 28px; color: var(--secondary); }
        .welcome-text p { color: #6b7280; margin-top: 5px; }

        /* Stylish Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; margin-bottom: 40px; }
        .stat-card {
            background: white; padding: 25px; border-radius: 20px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 10px 25px rgba(0,0,0,0.02); transition: 0.3s; border: 1px solid #f1f1f1;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
        .stat-info h3 { font-size: 14px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; }
        .stat-info p { font-size: 32px; font-weight: 700; color: var(--secondary); margin-top: 5px; }
        .stat-icon { width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; }

        .icon-1 { background: #fee2e2; color: #ef4444; }
        .icon-2 { background: #e0e7ff; color: #6366f1; }
        .icon-3 { background: #dcfce7; color: #22c55e; }
        .icon-4 { background: #fef9c3; color: #eab308; }

        /* Section Panels */
        .content-grid { display: grid; grid-template-columns: 2fr 1.2fr; gap: 30px; }
        .panel { background: white; padding: 30px; border-radius: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.02); }
        .panel h2 { font-size: 20px; color: var(--secondary); margin-bottom: 25px; display: flex; justify-content: space-between; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #9ca3af; font-weight: 500; font-size: 13px; border-bottom: 1px solid #f3f4f6; }
        td { padding: 18px 15px; color: #374151; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
        
        .user-row { display: flex; align-items: center; gap: 12px; }
        .user-img { width: 35px; height: 35px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--accent); }
        
        .blood-badge { padding: 4px 12px; background: #fee2e2; color: #b91c1c; border-radius: 8px; font-weight: 700; font-size: 12px; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="logo">
            <i class="fas fa-droplet"></i> <span>BloodConnect</span>
        </div>
        <nav class="nav-links">
            <a href="dashboard.php" class="active"><i class="fas fa-columns"></i> Dashboard</a>
            <a href="users.php"><i class="fas fa-user-friends"></i> Donors</a>
            <a href="requests.php"><i class="fas fa-file-medical"></i> Requests</a>
            <a href="donations.php"><i class="fas fa-hand-holding-heart"></i> Donations</a>
            <a href="inventory.php"><i class="fas fa-boxes-stacked"></i> Inventory</a>
            <a href="../logout.php" style="margin-top: 40px; color: #f87171;"><i class="fas fa-power-off"></i> Logout</a>
        </nav>
    </aside>

    <main class="main">
        <div class="top-bar">
            <div class="welcome-text">
                <h1>Dashboard Overview</h1>
                <p>Hello Admin, here is what's happening today.</p>
            </div>
            <div class="admin-profile" style="display:flex; align-items:center; gap:15px; background:white; padding:10px 20px; border-radius:15px; box-shadow: 0 5px 15px rgba(0,0,0,0.02);">
                <i class="fas fa-bell" style="color:#6b7280; cursor:pointer;"></i>
                <div style="width:40px; height:40px; background:var(--primary); color:white; border-radius:12px; display:flex; align-items:center; justify-content:center; font-weight:bold;">A</div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info"><h3>Total Donors</h3><p><?php echo $totalDonors; ?></p></div>
                <div class="stat-icon icon-1"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info"><h3>Requests</h3><p><?php echo $totalReq; ?></p></div>
                <div class="stat-icon icon-2"><i class="fas fa-heartbeat"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info"><h3>Donations</h3><p><?php echo $totalDonations; ?></p></div>
                <div class="stat-icon icon-3"><i class="fas fa-check-double"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info"><h3>Pending</h3><p><?php echo $pendingReq; ?></p></div>
                <div class="stat-icon icon-4"><i class="fas fa-spinner"></i></div>
            </div>
        </div>

        <div class="content-grid">
            <div class="panel">
                <h2><i class="fas fa-user-plus" style="margin-right:10px; color:var(--primary);"></i> Newest Registered Donors</h2>
                <table>
                    <thead>
                        <tr>
                            <th>DONOR NAME</th>
                            <th>BLOOD TYPE</th>
                            <th>JOINED DATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $recentUsers->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="user-row">
                                    <div class="user-img"><?php echo substr($user['name'], 0, 1); ?></div>
                                    <?php echo htmlspecialchars($user['name']); ?>
                                </div>
                            </td>
                            <td><span class="blood-badge"><?php echo $user['blood_type']; ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="panel" style="background: linear-gradient(135deg, var(--secondary), #2c3e50); color: white;">
                <h2 style="color:white;">Quick Actions</h2>
                <div style="display:flex; flex-direction:column; gap:15px; margin-top:10px;">
                    <a href="users.php" style="background:rgba(255,255,255,0.1); padding:15px; border-radius:15px; color:white; text-decoration:none; display:flex; align-items:center; gap:10px; transition:0.3s;">
                        <i class="fas fa-plus-circle"></i> View All Donors
                    </a>
                    <a href="inventory.php" style="background:rgba(255,255,255,0.1); padding:15px; border-radius:15px; color:white; text-decoration:none; display:flex; align-items:center; gap:10px;">
                        <i class="fas fa-warehouse"></i> Check Inventory
                    </a>
                </div>
                <div style="margin-top:40px; padding:20px; background:rgba(230,57,70,0.2); border-radius:20px; border: 1px dashed rgba(255,255,255,0.3); text-align:center;">
                    <p style="font-size:13px; opacity:0.8;">Need Help with System?</p>
                    <p style="font-weight:bold; margin-top:5px;">Contact Support</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>