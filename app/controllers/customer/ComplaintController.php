<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;

/**
 * Customer Complaint Controller
 * 
 * Handles customer complaints submission and management.
 * Customers can file complaints about services, staff, billing, etc.
 */
class ComplaintController extends Controller
{
    /**
     * Display the complaint form
     */
    public function index(): void
    {
        // Ensure user is logged in as customer
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }
        
        $userId = (int)($_SESSION['user_id'] ?? 0);
        
        // Fetch customer's vehicles for the dropdown
        $vehicles = $this->getCustomerVehicles($userId);
        
        $this->view('customer/complaint/index', [
            'title' => 'File a Complaint',
            'vehicles' => $vehicles,
        ]);
    }
    
    /**
     * Handle complaint form submission
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }
        
        // Ensure user is logged in as customer
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }
        
        $userId = (int)($_SESSION['user_id'] ?? 0);
        
        // Extract and validate form data
        $vehicleId = (int)($_POST['vehicle_id'] ?? 0);
        $complaintType = trim((string)($_POST['complaint_type'] ?? ''));
        $priority = trim((string)($_POST['priority'] ?? 'medium'));
        $incidentDate = trim((string)($_POST['incident_date'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $contactMethod = trim((string)($_POST['contact_method'] ?? 'email'));
        
        // Validation
        if ($vehicleId <= 0 || empty($complaintType) || empty($description) || empty($incidentDate)) {
            $_SESSION['flash'] = 'Please fill in all required fields.';
            header('Location: ' . rtrim(BASE_URL, '/') . '/customer/complaint');
            exit;
        }
        
        if (strlen($description) < 20) {
            $_SESSION['flash'] = 'Complaint description must be at least 20 characters.';
            header('Location: ' . rtrim(BASE_URL, '/') . '/customer/complaint');
            exit;
        }
        
        // Verify vehicle belongs to customer
        if (!$this->vehicleBelongsToUser($userId, $vehicleId)) {
            $_SESSION['flash'] = 'Invalid vehicle selection.';
            header('Location: ' . rtrim(BASE_URL, '/') . '/customer/complaint');
            exit;
        }
        
        // Insert complaint into database
        try {
            $pdo = db();
            
            // Insert into complaints table
            $stmt = $pdo->prepare(
                "INSERT INTO complaints (
                    customer_id, 
                    vehicle_id, 
                    complaint_type, 
                    priority, 
                    incident_date, 
                    description, 
                    contact_method, 
                    status, 
                    created_at
                ) VALUES (
                    :customer_id, 
                    :vehicle_id, 
                    :complaint_type, 
                    :priority, 
                    :incident_date, 
                    :description, 
                    :contact_method, 
                    'open', 
                    NOW()
                )"
            );
            
            $stmt->execute([
                'customer_id' => $userId,
                'vehicle_id' => $vehicleId,
                'complaint_type' => $complaintType,
                'priority' => $priority,
                'incident_date' => $incidentDate,
                'description' => $description,
                'contact_method' => $contactMethod,
            ]);
            
            $_SESSION['flash'] = 'Your complaint has been submitted successfully. We will contact you soon.';
            
            // Redirect back to complaint form or to a success page
            header('Location: ' . rtrim(BASE_URL, '/') . '/customer/complaint');
            exit;
            
        } catch (\PDOException $e) {
            error_log('Complaint submission error: ' . $e->getMessage());
            $_SESSION['flash'] = 'An error occurred while submitting your complaint. Please try again.';
            header('Location: ' . rtrim(BASE_URL, '/') . '/customer/complaint');
            exit;
        }
    }
    
    /**
     * Get all vehicles owned by a customer
     * 
     * @param int $userId Customer user ID
     * @return array List of vehicles
     */
    private function getCustomerVehicles(int $userId): array
    {
        try {
            $pdo = db();
            $stmt = $pdo->prepare(
                "SELECT 
                    v.vehicle_id, 
                    v.vehicle_number, 
                    v.brand, 
                    v.model 
                FROM vehicles v
                WHERE v.user_id = :user_id
                ORDER BY v.brand, v.model"
            );
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            error_log('Error fetching customer vehicles: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Verify that a vehicle belongs to a specific user
     * 
     * @param int $userId Customer user ID
     * @param int $vehicleId Vehicle ID
     * @return bool True if vehicle belongs to user
     */
    private function vehicleBelongsToUser(int $userId, int $vehicleId): bool
    {
        try {
            $pdo = db();
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) as count 
                FROM vehicles 
                WHERE vehicle_id = :vehicle_id AND user_id = :user_id"
            );
            $stmt->execute([
                'vehicle_id' => $vehicleId,
                'user_id' => $userId,
            ]);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return ($result['count'] ?? 0) > 0;
        } catch (\PDOException $e) {
            error_log('Error verifying vehicle ownership: ' . $e->getMessage());
            return false;
        }
    }
}
