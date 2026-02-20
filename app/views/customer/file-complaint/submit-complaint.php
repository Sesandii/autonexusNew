<!--submit-complaint.php -->
<?php
/**
 * File: app/views/customer/file-complaint/submit-complaint.php
 * Handles complaint form submission
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration - ADJUST THESE TO MATCH YOUR CONFIG
$host = 'localhost';
$dbname = 'autonexus';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['complaint_error'] = 'Database connection failed. Please try again later.';
    header('Location: index.php');
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Get form data
$appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
$complaint_text = isset($_POST['complaint']) ? trim($_POST['complaint']) : '';

// Validate input
if ($appointment_id <= 0) {
    $_SESSION['complaint_error'] = 'Please select an appointment.';
    header('Location: index.php');
    exit();
}

if (strlen($complaint_text) < 10) {
    $_SESSION['complaint_error'] = 'Please provide a more detailed complaint (at least 10 characters).';
    header('Location: index.php');
    exit();
}

// Get customer information from session
$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 0;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Fetch appointment and customer details
try {
    $stmt = $pdo->prepare("
        SELECT 
            a.appointment_id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            u.first_name,
            u.last_name,
            u.email,
            u.phone,
            v.make,
            v.model,
            v.license_plate,
            s.name as service_name
        FROM appointments a
        INNER JOIN customers c ON a.customer_id = c.customer_id
        INNER JOIN users u ON c.user_id = u.user_id
        INNER JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        INNER JOIN services s ON a.service_id = s.service_id
        WHERE a.appointment_id = :appointment_id
    ");
    
    $stmt->execute([':appointment_id' => $appointment_id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$appointment) {
        $_SESSION['complaint_error'] = 'Invalid appointment selected.';
        header('Location: index.php');
        exit();
    }
    
    // Prepare customer info
    $customer_name = $appointment['first_name'] . ' ' . $appointment['last_name'];
    $phone = $appointment['phone'];
    $email = $appointment['email'];
    $vehicle = $appointment['make'] . ' ' . $appointment['model'];
    $vehicle_number = $appointment['license_plate'];
    $complaint_date = date('Y-m-d');
    $complaint_time = date('H:i:s');
    
    // Insert complaint into database
    $stmt = $pdo->prepare("
        INSERT INTO complaints (
            customer_name,
            phone,
            email,
            vehicle,
            vehicle_number,
            complaint_date,
            complaint_time,
            description,
            priority,
            status,
            appointment_id,
            created_at
        ) VALUES (
            :customer_name,
            :phone,
            :email,
            :vehicle,
            :vehicle_number,
            :complaint_date,
            :complaint_time,
            :description,
            'Medium',
            'Open',
            :appointment_id,
            NOW()
        )
    ");
    
    $stmt->execute([
        ':customer_name' => $customer_name,
        ':phone' => $phone,
        ':email' => $email,
        ':vehicle' => $vehicle,
        ':vehicle_number' => $vehicle_number,
        ':complaint_date' => $complaint_date,
        ':complaint_time' => $complaint_time,
        ':description' => $complaint_text,
        ':appointment_id' => $appointment_id
    ]);
    
    // Success!
    $_SESSION['complaint_success'] = 'Your complaint has been submitted successfully. We will review it and get back to you soon.';
    header('Location: index.php');
    exit();
    
} catch (PDOException $e) {
    // Log error (in production, use proper logging)
    error_log('Complaint submission error: ' . $e->getMessage());
    
    $_SESSION['complaint_error'] = 'An error occurred while submitting your complaint. Please try again later.';
    header('Location: index.php');
    exit();
}