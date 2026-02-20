<!--load-appointments.php -->
<?php
/**
 * File: app/views/customer/file-complaint/load-appointments.php
 * This file shows how to load appointments for the complaint form
 * Include this at the top of index.php OR use your own controller
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
    die('Database connection failed: ' . $e->getMessage());
}

// Get customer ID from session
$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 0;

// Initialize appointments array
$appointments = [];

// Fetch completed appointments for the logged-in customer
if ($customer_id > 0) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                a.appointment_id,
                s.name as service,
                DATE_FORMAT(a.appointment_date, '%d %b %Y') as date,
                CONCAT(v.make, ' ', v.model, ' (', v.license_plate, ')') as vehicle,
                a.status
            FROM appointments a
            INNER JOIN services s ON a.service_id = s.service_id
            INNER JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            WHERE a.customer_id = :customer_id
            AND a.status IN ('completed', 'cancelled', 'in_service')
            ORDER BY a.appointment_date DESC
            LIMIT 50
        ");
        
        $stmt->execute([':customer_id' => $customer_id]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log('Error loading appointments: ' . $e->getMessage());
        $appointments = [];
    }
}

// If no customer is logged in, show sample data for testing
if ($customer_id === 0 || empty($appointments)) {
    // SAMPLE DATA FOR TESTING - Remove this in production
    $appointments = [
        [
            'appointment_id' => 20,
            'service' => 'Oil Change',
            'date' => '29 Oct 2025',
            'vehicle' => 'Mitsubishi Attrage (CBB6145)',
            'status' => 'completed'
        ],
        [
            'appointment_id' => 21,
            'service' => 'Full Package',
            'date' => '28 Nov 2025',
            'vehicle' => 'Mitsubishi Attrage (CBB6145)',
            'status' => 'completed'
        ]
    ];
}

// Now $appointments array is available for use in the view
?>