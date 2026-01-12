<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}
require_once '../config/database.php';

// Get counts for each blood type from donors
$query = "SELECT blood_type, COUNT(*) as count FROM users WHERE role = 'donor' GROUP BY blood_type";
$result = $conn->query($query);
$inventory = [];
while($row = $result->fetch_assoc()){
    $inventory[$row['blood_type']] = $row['count'];
}

$bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Inventory - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #dc3545; --dark: #2c3e50; --bg: #f4f7f6; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background: var(--bg); display: flex; }
        .sidebar { width: 260px; height: 100vh; background: var(--dark); color: white; position: fixed; padding: 20px; }
        .sidebar h2 { color: var(--primary); margin-bottom: 30px; }
        .sidebar a { display: block; color: #adb5bd; padding: 12px; text-decoration: none; border-radius: 8px; margin-bottom: 10px; }
        .sidebar a.active { background: rgba(255,255,255,0.1); color: white; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 30px; }
        
        .inventory-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .blood-card { background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-bottom: 4px solid var(--primary); }
        .blood-type { font-size: 35px; font-weight: bold; color: var(--primary); margin-bottom: 5px; }
        .donor-count { font-size: 18px; color: #666; }
        .header { background: white; padding: 20px; border-radius: 12px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>BloodConnect</h2>
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="users.php"><i class="fas fa-users"></i> Donors</a>
        <a href="requests.php"><i class="fas fa-heartbeat"></i> Requests</a>
        <a href="donations.php"><i class="fas fa-history"></i> Donations</a>
        <a href="inventory.php" class="active"><i class="fas fa-warehouse"></i> Inventory</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h3>Blood Inventory (Donor Availability)</h3>
            <span>Admin / Inventory</span>
        </div>

        <div class="inventory-grid">
            <?php foreach($bloodTypes as $type): ?>
            <div class="blood-card">
                <div class="blood-type"><?php echo $type; ?></div>
                <div class="donor-count">
                    <i class="fas fa-users"></i> 
                    <?php echo isset($inventory[$type]) ? $inventory[$type] : 0; ?> Donors
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>