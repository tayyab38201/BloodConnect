<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}
require_once '../config/database.php';

// Delete Logic
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM blood_requests WHERE id = $id");
    header('Location: requests.php'); exit;
}

$requests = $conn->query("SELECT br.*, u.name as requester_name FROM blood_requests br LEFT JOIN users u ON br.requester_id = u.id ORDER BY br.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Requests - Admin</title>
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
        .urgency-urgent { color: #dc3545; font-weight: bold; }
        .btn-del { color: #dc3545; font-size: 18px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>BloodConnect</h2>
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="users.php"><i class="fas fa-users"></i> Donors</a>
        <a href="requests.php" class="active"><i class="fas fa-heartbeat"></i> Requests</a>
        <a href="donations.php"><i class="fas fa-history"></i> Donations</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="main-content">
        <div class="card">
            <h3>Active Blood Requests</h3>
            <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Blood Type</th>
                        <th>Urgency</th>
                        <th>Hospital</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $requests->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                        <td><strong><?php echo $row['blood_type']; ?></strong></td>
                        <td class="urgency-<?php echo strtolower($row['urgency']); ?>"><?php echo ucfirst($row['urgency']); ?></td>
                        <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                        <td><a href="?delete=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>