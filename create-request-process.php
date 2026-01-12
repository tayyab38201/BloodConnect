<?php
session_start();
if (!isset($_SESSION['logged_in'])) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
require_once 'config/database.php';
header('Content-Type: application/json');

$bloodType = htmlspecialchars($_POST['blood_type'] ?? '');
$patientName = htmlspecialchars($_POST['patient_name'] ?? '');
$urgency = htmlspecialchars($_POST['urgency'] ?? '');
$requesterId = $_SESSION['user_id'];

try {
    // FIX: Unique ID hamesha generate hoga aur kabhi khali nahi jayega
    $requestId = 'REQ-' . time() . '-' . rand(1000, 9999);
    
    $stmt = $conn->prepare("INSERT INTO blood_requests (request_id, requester_id, blood_type, units_needed, urgency, patient_name, patient_age, patient_gender, hospital_name, hospital_address, contact_number, reason, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");

    $stmt->bind_param("sisississsss", 
        $requestId, $requesterId, $bloodType, $_POST['units_needed'], $urgency, 
        $patientName, $_POST['patient_age'], $_POST['patient_gender'], 
        $_POST['hospital_name'], $_POST['hospital_address'], 
        $_POST['contact_number'], $_POST['reason']
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Request created with ID: ' . $requestId]);
    } else {
        // Agar ab bhi error aaye to yahan check hoga
        echo json_encode(['success' => false, 'message' => 'Database Error: ' . $stmt->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>