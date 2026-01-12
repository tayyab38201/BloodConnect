<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - BloodConnect</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #dc3545;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f6fa;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            padding: 2rem 0;
            color: white;
            z-index: 1000;
        }

        .sidebar-logo {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
        }

        .sidebar-logo h2 {
            font-size: 1.5rem;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar-menu i {
            width: 20px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .page-header h1 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .settings-grid {
            display: grid;
            gap: 2rem;
        }

        .settings-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .settings-card h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .setting-item {
            padding: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .setting-item:last-child {
            border-bottom: none;
        }

        .setting-info h3 {
            color: #2c3e50;
            font-size: 1.1rem;
            margin-bottom: 0.3rem;
        }

        .setting-info p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .toggle-switch {
            position: relative;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 30px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--primary-color);
        }

        input:checked + .slider:before {
            transform: translateX(30px);
        }

        .btn {
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-outline {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: none;
        }

        .alert.show {
            display: block;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        @media (max-width: 968px) {
            .sidebar {
                left: -100%;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h2><i class="fas fa-heartbeat"></i> BloodConnect</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="blood-requests.php"><i class="fas fa-hand-holding-heart"></i> Blood Requests</a></li>
            <li><a href="create-request.php"><i class="fas fa-plus-circle"></i> Create Request</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-cog"></i> Settings</h1>
            <p style="color: #7f8c8d;">Manage your account preferences and settings</p>
        </div>

        <div class="settings-grid">
            <!-- Notification Settings -->
            <div class="settings-card">
                <h2><i class="fas fa-bell"></i> Notification Preferences</h2>
                
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Email Notifications</h3>
                        <p>Receive blood request alerts via email</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" <?php echo $user['email_notifications'] ? 'checked' : ''; ?> onchange="updateSetting('email_notifications', this.checked)">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>SMS Notifications</h3>
                        <p>Get urgent requests via SMS</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" <?php echo $user['sms_notifications'] ? 'checked' : ''; ?> onchange="updateSetting('sms_notifications', this.checked)">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Push Notifications</h3>
                        <p>Browser push notifications for urgent requests</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" <?php echo $user['push_notifications'] ? 'checked' : ''; ?> onchange="updateSetting('push_notifications', this.checked)">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <!-- Availability Settings -->
            <div class="settings-card">
                <h2><i class="fas fa-user-check"></i> Donation Availability</h2>
                
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Available for Donation</h3>
                        <p>Turn off if you're temporarily unavailable to donate</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" <?php echo $user['available'] ? 'checked' : ''; ?> onchange="updateSetting('available', this.checked)">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <!-- Change Password -->
            <div class="settings-card">
                <h2><i class="fas fa-lock"></i> Change Password</h2>
                <div class="alert alert-success" id="passwordAlert"></div>
                
                <form id="passwordForm">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" id="currentPassword" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" id="newPassword" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" id="confirmPassword" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="settings-card">
                <h2><i class="fas fa-exclamation-triangle"></i> Danger Zone</h2>
                
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Deactivate Account</h3>
                        <p>Temporarily disable your account</p>
                    </div>
                    <button class="btn btn-outline" onclick="deactivateAccount()">Deactivate</button>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Delete Account</h3>
                        <p>Permanently delete your account and all data</p>
                    </div>
                    <button class="btn btn-danger" onclick="deleteAccount()">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        function updateSetting(setting, value) {
            $.ajax({
                url: 'update-settings.php',
                type: 'POST',
                data: {
                    setting: setting,
                    value: value ? 1 : 0
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        console.log('Setting updated');
                    }
                }
            });
        }

        $('#passwordForm').submit(function(e) {
            e.preventDefault();
            
            const current = $('#currentPassword').val();
            const newPass = $('#newPassword').val();
            const confirm = $('#confirmPassword').val();

            if (newPass !== confirm) {
                alert('New passwords do not match!');
                return;
            }

            $.ajax({
                url: 'change-password.php',
                type: 'POST',
                data: {
                    current_password: current,
                    new_password: newPass
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#passwordAlert').text('Password updated successfully!').addClass('show');
                        $('#passwordForm')[0].reset();
                        setTimeout(() => {
                            $('#passwordAlert').removeClass('show');
                        }, 3000);
                    } else {
                        alert(response.message);
                    }
                }
            });
        });

        function deactivateAccount() {
            if (confirm('Are you sure you want to deactivate your account?')) {
                window.location.href = 'deactivate-account.php';
            }
        }

        function deleteAccount() {
            if (confirm('⚠️ WARNING: This will permanently delete your account and all data. This cannot be undone!\n\nAre you absolutely sure?')) {
                if (confirm('This is your final confirmation. Delete account?')) {
                    window.location.href = 'delete-account.php';
                }
            }
        }
    </script>
</body>
</html>