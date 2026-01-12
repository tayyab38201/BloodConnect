<?php
session_start();
if (!isset($_SESSION['logged_in'])) { 
    header('Location: login.php'); 
    exit; 
}
require_once 'config/database.php';

// URL se ID uthayen
$requestId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$donorId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form se aane wali ID
    $reqId = $_POST['request_id'];
    
    // Check karein ke ye donation pehle se to nahi mojud
    $check = $conn->prepare("SELECT id FROM donations WHERE donor_id = ? AND request_id = ?");
    $check->bind_param("ii", $donorId, $reqId);
    $check->execute();
    $res = $check->get_result();
    
    if ($res->num_rows > 0) {
        die("Aap pehle hi is request ko respond kar chuke hain!");
    }

    // Sahi Insert Query
    $stmt = $conn->prepare("INSERT INTO donations (donor_id, request_id, donation_date, status) VALUES (?, ?, NOW(), 'pending')");
    $stmt->bind_param("ii", $donorId, $reqId);
    
    if ($stmt->execute()) {
        // Requester ko notification bhejne ka code
        $msg = "A donor has responded to your blood request!";
        $notif = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) 
                                SELECT requester_id, 'Donation Response', ?, 'info' 
                                FROM blood_requests WHERE id = ?");
        $notif->bind_param("si", $msg, $reqId);
        $notif->execute();
        
        // Success par redirect
        header('Location: dashboard.php?success=1');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Display ke liye request fetch karein
$stmt = $conn->prepare("SELECT * FROM blood_requests WHERE id = ?");
$stmt->bind_param("i", $requestId);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

if (!$request) {
    die("Blood request not found!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Respond to Request</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; width: 400px; }
        .btn-confirm { background: #dc3545; color: white; border: none; padding: 12px; border-radius: 6px; cursor: pointer; width: 100%; font-size: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <i class="fas fa-heartbeat" style="font-size: 50px; color: #dc3545;"></i>
        <h2>Confirm Response</h2>
        <p>Aap <strong><?php echo $request['blood_type']; ?></strong> blood ke liye respond kar rahe hain.</p>
        
        <form method="POST">
            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
            <button type="submit" class="btn-confirm">Yes, I want to help!</button>
        </form>
        <a href="blood-requests.php" style="display:block; margin-top:15px; color:#777;">Cancel</a>
    </div>
</body>
</html>