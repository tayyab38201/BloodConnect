<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}
require_once '../config/database.php';

// Delete or Toggle Status Logic
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] === 'delete') {
        $conn->query("DELETE FROM users WHERE id = $id AND role != 'admin'");
    } elseif ($_GET['action'] === 'toggle') {
        $conn->query("UPDATE users SET status = IF(status='active', 'inactive', 'active') WHERE id = $id");
    }
    header('Location: users.php'); exit;
}

$users = $conn->query("SELECT * FROM users WHERE role = 'donor' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Donors - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #dc3545; --dark: #2c3e50; --bg: #f4f7f6; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background: var(--bg); display: flex; }
        
        /* Matching Dashboard Sidebar */
        .sidebar { width: 260px; height: 100vh; background: var(--dark); color: white; position: fixed; padding: 20px; }
        .sidebar h2 { color: var(--primary); margin-bottom: 30px; font-size: 22px; }
        .sidebar a { display: block; color: #adb5bd; padding: 12px; text-decoration: none; border-radius: 8px; margin-bottom: 10px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: white; }

        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 30px; }
        .header { background: white; padding: 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { color: var(--dark); background: #fafafa; }
        
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .active { background: #d4edda; color: #155724; }
        .inactive { background: #f8d7da; color: #721c24; }
        
        .btn-action { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; color: white; }
        .btn-status { background: #007bff; }
        .btn-del { background: #dc3545; margin-left: 5px; }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar { width: 70px; padding: 10px; }
            .sidebar h2, .sidebar a span { display: none; }
            .main-content { margin-left: 70px; width: calc(100% - 70px); }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>BloodConnect</h2>
        <a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
        <a href="users.php" class="active"><i class="fas fa-users"></i> <span>Donors</span></a>
        <a href="requests.php"><i class="fas fa-heartbeat"></i> <span>Requests</span></a>
        <a href="donations.php"><i class="fas fa-history"></i> <span>Donations</span></a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
    </div>

    <div class="main-content">
        <div class="header">
            <h3 style="margin:0;">Manage Donors</h3>
            <div class="admin-info">Admin / Users</div>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Blood Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><strong style="color:var(--primary);"><?php echo $row['blood_type']; ?></strong></td>
                        <td><span class="badge <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        <td>
                            <a href="?action=toggle&id=<?php echo $row['id']; ?>" class="btn-action btn-status" title="Toggle Status"><i class="fas fa-sync"></i></a>
                            <a href="?action=delete&id=<?php echo $row['id']; ?>" class="btn-action btn-del" onclick="return confirm('Delete user?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>