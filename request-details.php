<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$requestId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch full details of the request
$stmt = $conn->prepare("
    SELECT br.*, u.name as requester_name, u.phone as requester_phone 
    FROM blood_requests br 
    JOIN users u ON br.requester_id = u.id 
    WHERE br.id = ?
");
$stmt->bind_param("i", $requestId);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$request) {
    die("<div style='padding:20px; text-align:center;'><h3>Request not found!</h3><a href='blood-requests.php'>Go Back</a></div>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Details - BloodConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; padding: 40px; }
        .details-card { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #dc3545; margin-bottom: 20px; padding-bottom: 10px; color: #dc3545; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .label { font-weight: 600; color: #2c3e50; }
        .urgency-urgent { color: #dc3545; font-weight: bold; text-transform: uppercase; }
        .btn-back { display: inline-block; margin-top: 20px; text-decoration: none; color: #6c757d; }
        .btn-respond { display: block; text-align: center; background: #dc3545; color: white; padding: 12px; border-radius: 8px; text-decoration: none; margin-top: 20px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="details-card">
        <div class="header">
            <h2><i class="fas fa-tint"></i> Blood Request Details</h2>
        </div>
        
        <div class="info-row">
            <span class="label">Patient Name:</span>
            <span><?php echo htmlspecialchars($request['patient_name']); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Blood Type Needed:</span>
            <span style="font-size: 1.2rem; font-weight: bold; color: #dc3545;"><?php echo htmlspecialchars($request['blood_type']); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Urgency:</span>
            <span class="urgency-<?php echo strtolower($request['urgency']); ?>"><?php echo ucfirst($request['urgency']); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Hospital:</span>
            <span><?php echo htmlspecialchars($request['hospital_name']); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Units Needed:</span>
            <span><?php echo htmlspecialchars($request['units_needed']); ?> Units</span>
        </div>
        <div class="info-row">
            <span class="label">Contact:</span>
            <span><?php echo htmlspecialchars($request['contact_number']); ?></span>
        </div>
        
        <p><strong>Reason:</strong><br><?php echo nl2br(htmlspecialchars($request['reason'])); ?></p>

        <a href="respond-request.php?id=<?php echo $request['id']; ?>" class="btn-respond">I want to Donate / Respond</a>
        <a href="blood-requests.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to All Requests</a>
    </div>
</body>
</html>