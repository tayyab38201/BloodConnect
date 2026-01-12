<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}
require_once '../config/database.php';

$donations = $conn->query("
    SELECT dh.*, u.name as donor_name, br.patient_name 
    FROM donations_history dh
    JOIN users u ON dh.donor_id = u.id
    JOIN blood_requests br ON dh.request_id = br.id
    ORDER BY dh.donation_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #dc3545; --dark: #2c3e50; --bg: #f4f7f6; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background: var(--bg); display: flex; }
        .sidebar { width: 260px; height: 100vh; background: var(--dark); color: white; position: fixed; padding: 20px; }
        .sidebar h2 { color: var(--primary); margin-bottom: 30px; }
        .sidebar a { display: block; color: #adb5bd; padding: 12px; text-decoration: none; border-radius: 8px; margin-bottom: 10px; }
        .sidebar a.active { background: rgba(255,255,255,0.1); color: white; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 30px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .status { color: #28a745; font-weight: 600; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>BloodConnect</h2>
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="users.php"><i class="fas fa-users"></i> Donors</a>
        <a href="requests.php"><i class="fas fa-heartbeat"></i> Requests</a>
        <a href="donations.php" class="active"><i class="fas fa-history"></i> Donations</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="main-content">
        <div class="card">
            <h3>Donation Activity Log</h3>
            <table>
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Recipient</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $donations->fetch_assoc()): ?>
                    <tr>
                        <td><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($row['donor_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['donation_date'])); ?></td>
                        <td class="status">Completed</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>