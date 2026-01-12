<?php
session_start();
require_once 'config/database.php';

// Search Logic
$blood_group = $_GET['blood_group'] ?? '';
$city = $_GET['city'] ?? '';

$query = "SELECT name, blood_type, phone, city, available FROM users WHERE role = 'donor'";

if (!empty($blood_group)) {
    $query .= " AND blood_type = '" . $conn->real_escape_string($blood_group) . "'";
}
if (!empty($city)) {
    $query .= " AND city LIKE '%" . $conn->real_escape_string($city) . "%'";
}

$query .= " AND status = 'active' ORDER BY last_donation_date ASC";
$donors = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Donors | BloodConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #e63946; --secondary: #1d3557; --bg: #f4f7f6; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: var(--bg); color: var(--secondary); }

        .header { background: white; padding: 20px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo { font-size: 24px; font-weight: 700; color: var(--primary); text-decoration: none; }

        .search-container { background: var(--secondary); padding: 50px 5%; color: white; text-align: center; }
        .search-container h1 { margin-bottom: 20px; font-size: 32px; }

        .search-box { background: white; padding: 20px; border-radius: 15px; display: flex; flex-wrap: wrap; gap: 15px; max-width: 900px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .search-box select, .search-box input { flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; min-width: 150px; }
        .btn-search { background: var(--primary); color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: 0.3s; }
        .btn-search:hover { background: #c12a36; }

        .donors-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; padding: 50px 5%; }
        .donor-card { background: white; padding: 25px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: relative; border-left: 5px solid var(--primary); }
        .blood-tag { position: absolute; top: 20px; right: 20px; background: #fee2e2; color: var(--primary); padding: 5px 15px; border-radius: 10px; font-weight: 800; font-size: 18px; }
        
        .donor-info h3 { margin-bottom: 10px; color: var(--secondary); }
        .donor-info p { color: #666; font-size: 14px; margin-bottom: 5px; }
        .status { font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
        .available { background: #dcfce7; color: #166534; }
        .unavailable { background: #f3f4f6; color: #6b7280; }

        .btn-contact { display: inline-block; margin-top: 15px; background: var(--secondary); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; }
        
        @media (max-width: 768px) { .search-box { flex-direction: column; } }
    </style>
</head>
<body>

<nav class="header">
    <a href="index.php" class="logo"><i class="fas fa-droplet"></i> BloodConnect</a>
    <div>
        <a href="dashboard.php" style="text-decoration:none; color:var(--secondary); font-weight:500;">My Dashboard</a>
    </div>
</nav>

<div class="search-container">
    <h1>Find a Blood Donor</h1>
    <form class="search-box" method="GET">
        <select name="blood_group">
            <option value="">All Blood Groups</option>
            <option value="A+" <?php if($blood_group=='A+') echo 'selected'; ?>>A+</option>
            <option value="A-" <?php if($blood_group=='A-') echo 'selected'; ?>>A-</option>
            <option value="B+" <?php if($blood_group=='B+') echo 'selected'; ?>>B+</option>
            <option value="B-" <?php if($blood_group=='B-') echo 'selected'; ?>>B-</option>
            <option value="O+" <?php if($blood_group=='O+') echo 'selected'; ?>>O+</option>
            <option value="O-" <?php if($blood_group=='O-') echo 'selected'; ?>>O-</option>
            <option value="AB+" <?php if($blood_group=='AB+') echo 'selected'; ?>>AB+</option>
            <option value="AB-" <?php if($blood_group=='AB-') echo 'selected'; ?>>AB-</option>
        </select>
        <input type="text" name="city" placeholder="Enter City (e.g. Lahore)" value="<?php echo htmlspecialchars($city); ?>">
        <button type="submit" class="btn-search"><i class="fas fa-search"></i> Search</button>
    </form>
</div>

<div class="donors-grid">
    <?php if ($donors->num_rows > 0): ?>
        <?php while($donor = $donors->fetch_assoc()): ?>
        <div class="donor-card">
            <div class="blood-tag"><?php echo $donor['blood_type']; ?></div>
            <div class="donor-info">
                <h3><?php echo htmlspecialchars($donor['name']); ?></h3>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($donor['city'] ?: 'Not Specified'); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo $donor['phone']; ?></p>
                <span class="status <?php echo $donor['available'] ? 'available' : 'unavailable'; ?>">
                    <?php echo $donor['available'] ? 'Available Now' : 'Not Available'; ?>
                </span>
            </div>
            <a href="tel:<?php echo $donor['phone']; ?>" class="btn-contact"><i class="fas fa-phone"></i> Call Donor</a>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
            <i class="fas fa-search" style="font-size: 50px; color: #ccc; margin-bottom: 20px;"></i>
            <p style="color: #888;">No donors found matching your search. Try a different city or group.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>