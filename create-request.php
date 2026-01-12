<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

// --- BACKEND LOGIC: Sirf Data Save karne ke liye ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blood_type'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];

    try {
        $user_id = $_SESSION['user_id'];
        $blood_type = mysqli_real_escape_string($conn, $_POST['blood_type']);
        $patient_name = mysqli_real_escape_string($conn, $_POST['patient_name']);
        $patient_age = (int)$_POST['patient_age'];
        $patient_gender = mysqli_real_escape_string($conn, $_POST['patient_gender']);
        $units = (int)$_POST['units_needed'];
        $urgency = mysqli_real_escape_string($conn, $_POST['urgency']);
        $hosp_name = mysqli_real_escape_string($conn, $_POST['hospital_name']);
        $contact = mysqli_real_escape_string($conn, $_POST['contact_number']);
        $address = mysqli_real_escape_string($conn, $_POST['hospital_address']);
        $reason = mysqli_real_escape_string($conn, $_POST['reason'] ?? ''); // Column fix here

        // Query fix: additional_note ki jagah reason use kiya hai
        $query = "INSERT INTO blood_requests 
                 (requester_id, blood_type, patient_name, patient_age, patient_gender, units_needed, urgency, hospital_name, contact_number, hospital_address, reason, status, created_at) 
                 VALUES 
                 ('$user_id', '$blood_type', '$patient_name', '$patient_age', '$patient_gender', '$units', '$urgency', '$hosp_name', '$contact', '$address', '$reason', 'pending', NOW())";
        
        if ($conn->query($query)) {
            $response['success'] = true;
            $response['message'] = "Request submitted successfully!";
        } else {
            $response['message'] = "Database Error: " . $conn->error;
        }
    } catch (Exception $e) {
        $response['message'] = "Error: " . $e->getMessage();
    }
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blood Request - BloodConnect</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* AAPKA ORIGINAL STYLE (Line 24-300+) */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary-color: #dc3545;
            --secondary-color: #ff6b6b;
            --dark-color: #2c3e50;
            --sidebar-width: 260px;
        }

        body { font-family: 'Poppins', sans-serif; background: #f5f6fa; }

        .sidebar {
            position: fixed; left: 0; top: 0; bottom: 0; width: var(--sidebar-width);
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            padding: 2rem 0; color: white; z-index: 1000;
        }

        .sidebar-logo { padding: 0 1.5rem; margin-bottom: 2rem; }
        .sidebar-logo h2 { font-size: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }

        .sidebar-menu { list-style: none; }
        .sidebar-menu li { margin-bottom: 0.5rem; }
        .sidebar-menu a {
            display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.9); text-decoration: none; transition: all 0.3s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255, 255, 255, 0.2); color: white; }
        .sidebar-menu i { width: 20px; text-align: center; }

        .main-content { margin-left: var(--sidebar-width); padding: 2rem; }

        .page-header {
            background: white; padding: 2rem; border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 2rem;
        }

        .page-header h1 { color: var(--dark-color); margin-bottom: 0.5rem; }
        .page-header p { color: #666; }

        .form-container {
            background: white; padding: 2rem; border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 900px; margin: 0 auto;
        }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; color: var(--dark-color); font-weight: 500; font-size: 0.9rem; }
        .required { color: var(--primary-color); }

        input, select, textarea {
            width: 100%; padding: 0.9rem 1rem; border: 2px solid #e0e0e0;
            border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; transition: all 0.3s;
        }

        input:focus, select:focus, textarea:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1); }

        .blood-type-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.8rem; }
        .blood-type-option { position: relative; }
        .blood-type-option input[type="radio"] { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
        .blood-type-label {
            display: block; padding: 0.8rem; text-align: center; border: 2px solid #e0e0e0;
            border-radius: 10px; cursor: pointer; transition: all 0.3s; font-weight: 600; color: var(--dark-color);
        }
        .blood-type-option input[type="radio"]:checked + .blood-type-label {
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            color: white; border-color: var(--primary-color); box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }

        .urgency-options { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
        .urgency-option { position: relative; }
        .urgency-option input[type="radio"] { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
        .urgency-label {
            display: block; padding: 1rem; text-align: center; border: 2px solid #e0e0e0;
            border-radius: 10px; cursor: pointer; transition: all 0.3s;
        }
        .urgency-option input[type="radio"]:checked + .urgency-label { border-color: var(--primary-color); background: rgba(220, 53, 69, 0.1); }

        .btn-primary {
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            color: white; width: 100%; padding: 1rem; border: none; border-radius: 10px;
            cursor: pointer; font-weight: 600; font-size: 1rem; margin-top: 1rem; transition: all 0.3s;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4); }

        .alert { padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; display: none; }
        .alert.show { display: block; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h2><i class="fas fa-heartbeat"></i> BloodConnect</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="find-donors.php"><i class="fas fa-search"></i> Find Donors</a></li>
            <li><a href="create-request.php" class="active"><i class="fas fa-plus-circle"></i> Create Request</a></li>
            <li><a href="blood-requests.php"><i class="fas fa-hand-holding-heart"></i> Blood Requests</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-plus-circle"></i> Create Blood Request</h1>
            <p>Fill in the details below to submit a new blood request</p>
        </div>

        <div class="form-container">
            <div id="successAlert" class="alert alert-success"></div>
            <div id="errorAlert" class="alert alert-danger"></div>

            <form id="createRequestForm">
                <div class="form-group">
                    <label>Blood Type Needed <span class="required">*</span></label>
                    <div class="blood-type-grid">
                        <div class="blood-type-option"><input type="radio" name="blood_type" id="a_pos" value="A+" required><label for="a_pos" class="blood-type-label">A+</label></div>
                        <div class="blood-type-option"><input type="radio" name="blood_type" id="a_neg" value="A-"><label for="a_neg" class="blood-type-label">A-</label></div>
                        <div class="blood-type-option"><input type="radio" name="blood_type" id="b_pos" value="B+"><label for="b_pos" class="blood-type-label">B+</label></div>
                        <div class="blood-type-option"><input type="radio" name="blood_type" id="b_neg" value="B-"><label for="b_neg" class="blood-type-label">B-</label></div>
                        <div class="blood-type-option"><input type="radio" name="blood_type" id="ab_pos" value="AB+"><label for="ab_pos" class="blood-type-label">AB+</label></div>
                        <div class="blood-type-option"><input type="radio" name="blood_type" id="ab_neg" value="AB-"><label for="ab_neg" class="blood-type-label">AB-</label></div>
                        <div class="blood-type-option"><input type="radio" name="blood_type" id="o_pos" value="O+"><label for="o_pos" class="blood-type-label">O+</label></div>
                        <div class="blood-type-option"><input type="radio" name="blood_type" id="o_neg" value="O-"><label for="o_neg" class="blood-type-label">O-</label></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group"><label>Patient Name <span class="required">*</span></label><input type="text" name="patient_name" required></div>
                    <div class="form-group"><label>Patient Age <span class="required">*</span></label><input type="number" name="patient_age" required></div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Patient Gender <span class="required">*</span></label>
                        <select name="patient_gender" required>
                            <option value="male">Male</option><option value="female">Female</option><option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Units Needed <span class="required">*</span></label><input type="number" name="units_needed" required></div>
                </div>

                <div class="form-group">
                    <label>Urgency Level <span class="required">*</span></label>
                    <div class="urgency-options">
                        <div class="urgency-option"><input type="radio" name="urgency" id="crit" value="critical" required><label for="crit" class="urgency-label">Critical</label></div>
                        <div class="urgency-option"><input type="radio" name="urgency" id="urg" value="urgent"><label for="urg" class="urgency-label">Urgent</label></div>
                        <div class="urgency-option"><input type="radio" name="urgency" id="rout" value="routine"><label for="rout" class="urgency-label">Routine</label></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group"><label>Hospital Name <span class="required">*</span></label><input type="text" name="hospital_name" required></div>
                    <div class="form-group"><label>Contact Number <span class="required">*</span></label><input type="tel" name="contact_number" required></div>
                </div>

                <div class="form-group"><label>Hospital Address <span class="required">*</span></label><input type="text" name="hospital_address" required></div>
                <div class="form-group"><label>Reason / Additional Note</label><textarea name="reason" rows="3"></textarea></div>

                <button type="submit" class="btn-primary" id="submitBtn">Submit Blood Request</button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#createRequestForm').submit(function(e) {
                e.preventDefault();
                const btn = $('#submitBtn');
                btn.prop('disabled', true).text('Submitting...');

                $.ajax({
                    url: 'create-request.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if(res.success) {
                            $('#successAlert').text(res.message).addClass('show');
                            $('#errorAlert').removeClass('show');
                            $('#createRequestForm')[0].reset();
                            setTimeout(() => { window.location.href = 'blood-requests.php'; }, 2000);
                        } else {
                            $('#errorAlert').text(res.message).addClass('show');
                            btn.prop('disabled', false).text('Submit Blood Request');
                        }
                    },
                    error: function() {
                        $('#errorAlert').text('Something went wrong. Check database.').addClass('show');
                        btn.prop('disabled', false).text('Submit Blood Request');
                    }
                });
            });
        });
    </script>
</body>
</html>