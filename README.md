🩸 BloodConnect - Smart Blood Donation Management System
BloodConnect is a robust PHP-based web application designed to bridge the gap between blood donors and people in medical emergencies. It provides a real-time platform for posting, managing, and responding to blood requests efficiently.

🚀 Key Features
Secure Authentication: User-specific login and registration system.

Dynamic Dashboard: Real-time statistics showing total donations and active blood requests.

Unique Request Tracking: Every blood request is assigned a unique ID (e.g., REQ-1705001234-5582) using a custom timestamp and random logic to prevent database duplication.

Intelligent Request Flow: Users can create requests with detailed patient info, hospital location, and urgency levels.

Urgency-Based Expiry: Requests automatically calculate expiry dates (1-7 days) based on the priority level (Critical, Urgent, or Routine).

One-Click Response: Donors can instantly commit to a request, which notifies the requester and logs the donation.

Activity Logging: Tracks every major action (like creating a request) with user ID and IP address for security.

🛠️ Technical Architecture (File Overview)
create-request-process.php: The core engine that validates form data, generates unique request_id, and handles secure database insertion using Prepared Statements.

respond-request.php: Manages the donor's interaction with a specific request, updates the donations table, and triggers notifications.

dashboard.php: The user's home base, displaying a summary of activities and system-wide blood needs.

blood-requests.php: A searchable listing page where users can filter requests by blood type and urgency.

config/database.php: Centralized configuration for MySQLi connection.

💾 Database Schema
The system relies on a relational database with the following primary tables:

Users: Donor profiles and contact details.

Blood_Requests: Stores patient data, hospital info, and request status.

Donations: Records successful matches between donors and requests.

Notifications: Handles alerts for donation responses.

Activity_Logs: Maintains a security audit trail of user actions.

⚙️ Installation & Setup
Clone the Repository:

Bash

git clone https://github.com/tayyab38201/BloodConnect.git
Database Configuration:

Import the provided SQL file into your phpMyAdmin.

Update config/database.php with your local database credentials.

Deploy:

Move the folder to your XAMPP htdocs directory.

Access the project via http://localhost/Blood.

🛡️ Security Features
SQL Injection Protection: All database interactions use bind_param() to ensure data safety.

Input Sanitization: Custom sanitizeInput function to prevent XSS attacks.

Session Guard: Unauthorized users are automatically redirected to the login page.
