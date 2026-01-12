<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BloodConnect</title>
    
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
            --secondary-color: #ff6b6b;
            --dark-color: #2c3e50;
            --success-color: #27ae60;
            --gradient: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, rgba(255, 107, 107, 0.05) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .register-left {
            background: var(--gradient);
            padding: 3rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .register-left h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .register-left p {
            font-size: 1.1rem;
            line-height: 1.8;
            opacity: 0.95;
            margin-bottom: 2rem;
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            padding: 0.8rem 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .feature-list i {
            width: 30px;
            height: 30px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-right {
            padding: 3rem;
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header h2 {
            color: var(--dark-color);
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .register-header p {
            color: #7f8c8d;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .required {
            color: var(--primary-color);
        }

        input, select, textarea {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #7f8c8d;
        }

        .blood-type-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.8rem;
        }

        .blood-type-option {
            position: relative;
        }

        .blood-type-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .blood-type-label {
            display: block;
            padding: 0.8rem;
            text-align: center;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            color: var(--dark-color);
        }

        .blood-type-option input[type="radio"]:checked + .blood-type-label {
            background: var(--gradient);
            color: white;
            border-color: var(--primary-color);
        }

        .blood-type-label:hover {
            border-color: var(--primary-color);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            cursor: pointer;
        }

        .checkbox-group label {
            margin-bottom: 0;
            cursor: pointer;
            font-weight: 400;
        }

        .checkbox-group a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }

        .btn-primary {
            background: var(--gradient);
            color: white;
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #7f8c8d;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .error-message {
            color: var(--primary-color);
            font-size: 0.85rem;
            margin-top: 0.3rem;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
            display: none;
        }

        .success-message.show {
            display: block;
        }

        @media (max-width: 968px) {
            .register-container {
                grid-template-columns: 1fr;
            }

            .register-left {
                display: none;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .blood-type-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 480px) {
            .blood-type-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .register-right {
                padding: 2rem 1.5rem;
            }
        }

        /* Loading spinner */
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-left: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-left">
            <h1><i class="fas fa-heartbeat"></i> BloodConnect</h1>
            <p>Join our community of life-savers and make a real difference in someone's life today.</p>
            <ul class="feature-list">
                <li>
                    <i class="fas fa-check"></i>
                    <span>Save lives in your community</span>
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    <span>Get instant notifications for urgent requests</span>
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    <span>Track your donation history</span>
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    <span>Connect with verified hospitals</span>
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    <span>100% secure and private</span>
                </li>
            </ul>
        </div>

        <div class="register-right">
            <div class="register-header">
                <h2>Create Account</h2>
                <p>Start your journey as a blood donor hero</p>
            </div>

            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i> Registration successful! Redirecting to login...
            </div>

            <form id="registerForm" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="name" id="name" placeholder="John Doe" required>
                        <span class="error-message" id="nameError"></span>
                    </div>
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" id="email" placeholder="john@example.com" required>
                        <span class="error-message" id="emailError"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Phone Number <span class="required">*</span></label>
                        <input type="tel" name="phone" id="phone" placeholder="+1 (555) 123-4567" required>
                        <span class="error-message" id="phoneError"></span>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth <span class="required">*</span></label>
                        <input type="date" name="dob" id="dob" required>
                        <span class="error-message" id="dobError"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Blood Type <span class="required">*</span></label>
                    <div class="blood-type-grid">
                        <div class="blood-type-option">
                            <input type="radio" name="blood_type" id="a_positive" value="A+" required>
                            <label for="a_positive" class="blood-type-label">A+</label>
                        </div>
                        <div class="blood-type-option">
                            <input type="radio" name="blood_type" id="a_negative" value="A-">
                            <label for="a_negative" class="blood-type-label">A-</label>
                        </div>
                        <div class="blood-type-option">
                            <input type="radio" name="blood_type" id="b_positive" value="B+">
                            <label for="b_positive" class="blood-type-label">B+</label>
                        </div>
                        <div class="blood-type-option">
                            <input type="radio" name="blood_type" id="b_negative" value="B-">
                            <label for="b_negative" class="blood-type-label">B-</label>
                        </div>
                        <div class="blood-type-option">
                            <input type="radio" name="blood_type" id="ab_positive" value="AB+">
                            <label for="ab_positive" class="blood-type-label">AB+</label>
                        </div>
                        <div class="blood-type-option">
                            <input type="radio" name="blood_type" id="ab_negative" value="AB-">
                            <label for="ab_negative" class="blood-type-label">AB-</label>
                        </div>
                        <div class="blood-type-option">
                            <input type="radio" name="blood_type" id="o_positive" value="O+">
                            <label for="o_positive" class="blood-type-label">O+</label>
                        </div>
                        <div class="blood-type-option">
                            <input type="radio" name="blood_type" id="o_negative" value="O-">
                            <label for="o_negative" class="blood-type-label">O-</label>
                        </div>
                    </div>
                    <span class="error-message" id="bloodTypeError"></span>
                </div>

                <div class="form-group">
                    <label>Address <span class="required">*</span></label>
                    <input type="text" name="address" id="address" placeholder="123 Main Street, City, State" required>
                    <span class="error-message" id="addressError"></span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Password <span class="required">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" placeholder="••••••••" required>
                            <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                        </div>
                        <span class="error-message" id="passwordError"></span>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password <span class="required">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" name="confirm_password" id="confirmPassword" placeholder="••••••••" required>
                            <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>
                        </div>
                        <span class="error-message" id="confirmPasswordError"></span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="terms" id="terms" required>
                        <label for="terms">I agree to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a></label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Create Account
                </button>

                <div class="login-link">
                    Already have an account? <a href="login.php">Login here</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('#togglePassword').click(function() {
                const passwordInput = $('#password');
                const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                passwordInput.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            $('#toggleConfirmPassword').click(function() {
                const passwordInput = $('#confirmPassword');
                const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                passwordInput.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            // Clear error messages on input
            $('input, select').on('input change', function() {
                $(this).siblings('.error-message').removeClass('show');
            });

            // Form validation
            function validateForm() {
                let isValid = true;
                $('.error-message').removeClass('show');

                // Name validation
                const name = $('#name').val().trim();
                if (name.length < 3) {
                    $('#nameError').text('Name must be at least 3 characters').addClass('show');
                    isValid = false;
                }

                // Email validation
                const email = $('#email').val().trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    $('#emailError').text('Please enter a valid email address').addClass('show');
                    isValid = false;
                }

                // Phone validation
                const phone = $('#phone').val().trim();
                if (phone.length < 10) {
                    $('#phoneError').text('Phone number must be at least 10 digits').addClass('show');
                    isValid = false;
                }

                // Age validation (must be 18+)
                const dob = $('#dob').val();
                if (!dob) {
                    $('#dobError').text('Please select your date of birth').addClass('show');
                    isValid = false;
                } else {
                    const dobDate = new Date(dob);
                    const today = new Date();
                    const age = today.getFullYear() - dobDate.getFullYear();
                    if (age < 18) {
                        $('#dobError').text('You must be at least 18 years old').addClass('show');
                        isValid = false;
                    }
                }

                // Blood type validation
                if (!$('input[name="blood_type"]:checked').val()) {
                    $('#bloodTypeError').text('Please select your blood type').addClass('show');
                    isValid = false;
                }

                // Address validation
                const address = $('#address').val().trim();
                if (address.length < 10) {
                    $('#addressError').text('Please enter a complete address').addClass('show');
                    isValid = false;
                }

                // Password validation
                const password = $('#password').val();
                if (password.length < 8) {
                    $('#passwordError').text('Password must be at least 8 characters').addClass('show');
                    isValid = false;
                } else if (!/[A-Z]/.test(password)) {
                    $('#passwordError').text('Password must contain uppercase letter').addClass('show');
                    isValid = false;
                } else if (!/[a-z]/.test(password)) {
                    $('#passwordError').text('Password must contain lowercase letter').addClass('show');
                    isValid = false;
                } else if (!/[0-9]/.test(password)) {
                    $('#passwordError').text('Password must contain a number').addClass('show');
                    isValid = false;
                }

                // Confirm password validation
                const confirmPassword = $('#confirmPassword').val();
                if (password !== confirmPassword) {
                    $('#confirmPasswordError').text('Passwords do not match').addClass('show');
                    isValid = false;
                }

                return isValid;
            }

            // Form submission
            $('#registerForm').submit(function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    return;
                }

                // Show loading state
                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true);
                submitBtn.html('Creating Account... <span class="spinner"></span>');

                // Prepare form data
                const formData = {
                    name: $('#name').val().trim(),
                    email: $('#email').val().trim(),
                    phone: $('#phone').val().trim(),
                    dob: $('#dob').val(),
                    blood_type: $('input[name="blood_type"]:checked').val(),
                    address: $('#address').val().trim(),
                    password: $('#password').val()
                };

                // AJAX request
                $.ajax({
                    url: 'register_process.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#successMessage').addClass('show');
                            $('#registerForm')[0].reset();
                            
                            // Redirect to login after 2 seconds
                            setTimeout(function() {
                                window.location.href = 'login.php?registered=success';
                            }, 2000);
                        } else {
                            // Show error message
                            if (response.errors && Object.keys(response.errors).length > 0) {
                                // Show field-specific errors
                                $.each(response.errors, function(field, message) {
                                    const errorId = field + 'Error';
                                    $('#' + errorId).text(message).addClass('show');
                                });
                            }
                            alert(response.message || 'Registration failed. Please check the form.');
                            submitBtn.prop('disabled', false);
                            submitBtn.html('Create Account');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        alert('An error occurred. Please try again.');
                        submitBtn.prop('disabled', false);
                        submitBtn.html('Create Account');
                    }
                });
            });

            // Real-time validation
            $('#email').blur(function() {
                const email = $(this).val().trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email && !emailRegex.test(email)) {
                    $('#emailError').text('Please enter a valid email address').addClass('show');
                } else {
                    $('#emailError').removeClass('show');
                }
            });

            $('#confirmPassword').on('input', function() {
                const password = $('#password').val();
                const confirmPassword = $(this).val();
                if (confirmPassword && password !== confirmPassword) {
                    $('#confirmPasswordError').text('Passwords do not match').addClass('show');
                } else {
                    $('#confirmPasswordError').removeClass('show');
                }
            });
        });
    </script>
</body>
</html>