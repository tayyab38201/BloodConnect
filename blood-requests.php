<?php
session_start();

// 1. Login Check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$userId = $_SESSION['user_id'];

// 2. Filters hasil karna
$bloodTypeFilter = $_GET['blood_type'] ?? '';
$urgencyFilter = $_GET['urgency'] ?? '';
$searchQuery = $_GET['search'] ?? '';

// 3. Query Build karna (Expiry check nikal diya hai takay data nazar aaye)
$query = "SELECT * FROM blood_requests WHERE status = 'pending'";
$params = [];
$types = "";

if (!empty($bloodTypeFilter)) {
    $query .= " AND blood_type = ?";
    $params[] = $bloodTypeFilter;
    $types .= "s";
}

if (!empty($urgencyFilter)) {
    $query .= " AND urgency = ?";
    $params[] = $urgencyFilter;
    $types .= "s";
}

if (!empty($searchQuery)) {
    $query .= " AND (patient_name LIKE ? OR hospital_name LIKE ?)";
    $searchTerm = "%$searchQuery%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);

// 4. Params bind karna (Sirf tab jab filter use ho)
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Requests - BloodConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --primary-color: #dc3545; --secondary-color: #ff6b6b; --dark-color: #2c3e50; --sidebar-width: 260px; }
        body { font-family: 'Poppins', sans-serif; background: #f5f6fa; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: var(--sidebar-width); background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%); padding: 2rem 0; color: white; z-index: 1000; }
        .main-content { margin-left: var(--sidebar-width); padding: 2rem; }
        .sidebar-menu { list-style: none; margin-top: 2rem; }
        .sidebar-menu a { display: block; padding: 1rem 1.5rem; color: white; text-decoration: none; }
        .sidebar-menu a:hover { background: rgba(255,255,255,0.1); }
        .filters-section { background: white; padding: 1.5rem; border-radius: 15px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .filters-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 1rem; align-items: end; }
        .requests-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; }
        .request-card { background: white; border-radius: 12px; padding: 1.5rem; border: 1px solid #eee; }
        .blood-badge { background: #dc3545; color: white; padding: 5px 15px; border-radius: 5px; font-weight: bold; }
        .btn-primary { background: #dc3545; color: white; padding: 10px; border-radius: 5px; text-decoration: none; display: inline-block; width: 100%; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div style="padding: 0 1.5rem;"><h2><i class="fas fa-heartbeat"></i> BloodConnect</h2></div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="blood-requests.php" style="background:rgba(255,255,255,0.2);"><i class="fas fa-hand-holding-heart"></i> Blood Requests</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1>Blood Requests</h1>
            <p>Donors are waiting for your help.</p>
        </div>

        <div class="filters-section">
            <form method="GET">
                <div class="filters-grid">
                    <div>
                        <label>Search</label><br>
                        <input type="text" name="search" placeholder="Patient or Hospital..." value="<?php echo htmlspecialchars($searchQuery); ?>" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ddd;">
                    </div>
                    <div>
                        <label>Blood Type</label><br>
                        <select name="blood_type" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ddd;">
                            <option value="">All</option>
                            <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $type): ?>
                                <option value="<?php echo $type; ?>" <?php echo $bloodTypeFilter == $type ? 'selected' : ''; ?>><?php echo $type; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn-primary" style="margin-top:0;">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="requests-grid">
            <?php if (empty($requests)): ?>
                <div style="background:white; padding:2rem; border-radius:15px; text-align:center; width:100%;">
                    <h3>No requests found!</h3>
                    <p>Try changing filters or check XAMPP database.</p>
                </div>
            <?php else: ?>
                <?php foreach ($requests as $request): ?>
                    <div class="request-card">
                        <div style="display:flex; justify-content:space-between; margin-bottom:1rem;">
                            <span class="blood-badge"><?php echo $request['blood_type']; ?></span>
                            <span style="color:#777; font-size:0.8rem;"><?php echo $request['urgency']; ?></span>
                        </div>
                        <p><strong>Patient:</strong> <?php echo htmlspecialchars($request['patient_name']); ?></p>
                        <p><strong>Hospital:</strong> <?php echo htmlspecialchars($request['hospital_name']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($request['contact_number']); ?></p>
                        <a href="respond-request.php?id=<?php echo $request['id']; ?>" class="btn-primary">Respond Now</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>