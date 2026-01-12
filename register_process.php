<?php
/**
 * BloodConnect - Registration Process
 * Handles user registration with validation and security
 */

// Start session
session_start();

// Database configuration
require_once 'config/database.php';

// Set JSON header
header('Content-Type: application/json');

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Sanitize and validate inputs
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Get and sanitize form data
$name = sanitizeInput($_POST['name'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$phone = sanitizeInput($_POST['phone'] ?? '');
$dob = sanitizeInput($_POST['dob'] ?? '');
$blood_type = sanitizeInput($_POST['blood_type'] ?? '');
$address = sanitizeInput($_POST['address'] ?? '');
$password = $_POST['password'] ?? '';

// Validation
$errors = [];

// Name validation
if (empty($name)) {
    $errors['name'] = 'Name is required';
} elseif (strlen($name) < 3) {
    $errors['name'] = 'Name must be at least 3 characters long';
}

// Email validation
if (empty($email)) {
    $errors['email'] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address';
}

// Phone validation
if (empty($phone)) {
    $errors['phone'] = 'Phone number is required';
} elseif (!preg_match('/^[\d\s\-\+\(\)]{10,}$/', $phone)) {
    $errors['phone'] = 'Please enter a valid phone number (at least 10 digits)';
}

// Date of birth validation (must be 18+)
if (empty($dob)) {
    $errors['dob'] = 'Date of birth is required';
} else {
    try {
        $dobDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($dobDate)->y;
        
        if ($age < 18) {
            $errors['dob'] = 'You must be at least 18 years old to register';
        }
        if ($age > 100) {
            $errors['dob'] = 'Please enter a valid date of birth';
        }
    } catch (Exception $e) {
        $errors['dob'] = 'Invalid date format';
    }
}

// Blood type validation
$validBloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
if (empty($blood_type)) {
    $errors['blood_type'] = 'Please select your blood type';
} elseif (!in_array($blood_type, $validBloodTypes)) {
    $errors['blood_type'] = 'Please select a valid blood type';
}

// Address validation
if (empty($address)) {
    $errors['address'] = 'Address is required';
} elseif (strlen($address) < 10) {
    $errors['address'] = 'Please enter a complete address (at least 10 characters)';
}

// Password validation
if (empty($password)) {
    $errors['password'] = 'Password is required';
} elseif (strlen($password) < 8) {
    $errors['password'] = 'Password must be at least 8 characters long';
} elseif (!preg_match('/[A-Z]/', $password)) {
    $errors['password'] = 'Password must contain at least one uppercase letter';
} elseif (!preg_match('/[a-z]/', $password)) {
    $errors['password'] = 'Password must contain at least one lowercase letter';
} elseif (!preg_match('/[0-9]/', $password)) {
    $errors['password'] = 'Password must contain at least one number';
}

// If there are validation errors, return them
if (!empty($errors)) {
    $response['message'] = 'Please fix the following errors';
    $response['errors'] = $errors;
    echo json_encode($response);
    exit;
}

try {
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $response['message'] = 'Email address is already registered. Please login or use a different email.';
        echo json_encode($response);
        exit;
    }
    $stmt->close();
    
    // Hash password using bcrypt
    $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    // Generate unique user ID
    $userId = 'BC' . strtoupper(uniqid());
    
    // Prepare insert statement
    $stmt = $conn->prepare("
        INSERT INTO users 
        (user_id, name, email, phone, dob, blood_type, address, password_hash, role, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'donor', 'active', NOW())
    ");
    
    $stmt->bind_param(
        "ssssssss",
        $userId,
        $name,
        $email,
        $phone,
        $dob,
        $blood_type,
        $address,
        $passwordHash
    );
    
    if ($stmt->execute()) {
        // Registration successful
        $response['success'] = true;
        $response['message'] = 'Registration successful! You can now login.';
        $response['user_id'] = $userId;
        
        // Log the registration
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $logStmt = $conn->prepare("
            INSERT INTO activity_logs (user_id, action, ip_address, user_agent, created_at) 
            VALUES (?, 'User registered', ?, ?, NOW())
        ");
        $logStmt->bind_param("sss", $userId, $ipAddress, $userAgent);
        $logStmt->execute();
        $logStmt->close();
        
    } else {
        throw new Exception('Database error: ' . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    $response['message'] = 'Registration failed. Please try again.';
    error_log('Registration error: ' . $e->getMessage());
}

// Close database connection
$conn->close();

// Return JSON response
echo json_encode($response);
?>