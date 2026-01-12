<?php
session_start();
if (!isset($_SESSION['logged_in'])) { exit; }
require_once 'config/database.php';

$donationId = $_GET['id'] ?? 0;
$userId = $_SESSION['user_id'];

// Verify donation belongs to user
$stmt = $conn->prepare("SELECT d.*, u.name FROM donations_history d JOIN users u ON d.donor_id = u.id WHERE d.id = ? AND d.donor_id = ?");
$stmt->bind_param("ii", $donationId, $userId);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) { die("Unauthorized or Donation not found!"); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        .certificate { border: 10px solid #dc3545; padding: 50px; width: 800px; margin: 50px auto; text-align: center; position: relative; font-family: 'Georgia', serif; }
        .certificate:after { content: ''; border: 2px solid #dc3545; position: absolute; top: 10px; left: 10px; right: 10px; bottom: 10px; pointer-events: none; }
        h1 { font-size: 50px; color: #dc3545; }
        h2 { font-size: 30px; }
        .name { font-size: 40px; border-bottom: 2px solid #333; display: inline-block; padding: 0 20px; }
        .date { margin-top: 30px; }
    </style>
</head>
<body>
    <div class="certificate">
        <h1>CERTIFICATE</h1>
        <h3>OF APPRECIATION</h3>
        <p>This certificate is proudly presented to</p>
        <div class="name"><?php echo htmlspecialchars($data['name']); ?></div>
        <p>For their noble contribution and life-saving blood donation on</p>
        <div class="date"><strong><?php echo date('F d, Y', strtotime($data['donation_date'])); ?></strong></div>
        <p style="margin-top: 50px;"><em>"You don't have to be a doctor to save lives."</em></p>
        <button onclick="window.print()" style="margin-top: 20px; padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">Print Certificate</button>
    </div>
</body>
</html>