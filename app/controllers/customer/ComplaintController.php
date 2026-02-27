<?php
declare(strict_types=1);

/**
 * ComplaintController.php
 * 
 * This controller handles customer complaints.
 * - The 'file' method displays the complaint form with appointments dropdown
 * - The 'submit' method processes the complaint submission
 * 
 * For beginners: A controller is like a traffic cop that receives requests
 * from the user and decides what to do with them (e.g., show a form, save data)
 */

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\Appointments;
use app\model\Receptionist\ComplaintModel;

class ComplaintController extends Controller
{
    /**
     * file() - Display the complaint form
     * 
     * This method loads the complaint form page and passes appointment data
     * to the view so customers can select which appointment to complain about.
     * 
     * How it works:
     * 1. Checks if user is logged in as a customer
     * 2. Gets the user_id from the session (stored when user logs in)
     * 3. Fetches all completed appointments for this customer
     * 4. Passes the appointments to the view file
     */
    public function file(): void
    {
        // Check if user is logged in as customer (this prevents unauthorized access)
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        // Get the logged-in user's ID from the session
        // Sessions store data between page requests (like login info)
        $uid = (int)($_SESSION['user_id'] ?? 0);
        
        // Create an instance of the Appointments model to fetch data from database
        $appointmentModel = new Appointments();

        // Fetch all completed appointments for this user
        // We only want completed appointments because you can only complain about finished services
        $appointments = $appointmentModel->completedByUser($uid);

        // Load the view file and pass data to it
        // The view file can access $appointments variable to display the dropdown
        $this->view('customer/file-complaint', [
            'title' => 'File a Complaint',
            'appointments' => $appointments,
        ]);
    }

    /**
     * submit() - Process complaint submission (STUBBED)
     * 
     * This method will handle the form submission when customer clicks "Submit Complaint"
     * Currently it's a stub (placeholder) that shows the structure.
     * 
     * TODO: Complete this method with full implementation
     * Steps to implement:
     * 1. Validate the POST data (appointment_id, description, etc.)
     * 2. Check that appointment belongs to logged-in customer
     * 3. Get customer_id and vehicle_id from the appointment
     * 4. Insert complaint into database using ComplaintModel
     * 5. Show success message and redirect back to complaint form
     * 6. Handle errors appropriately (show error message)
     */
    public function submit(): void
    {
        // Ensure this is a POST request (form submission)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // 405 = Method Not Allowed
            echo 'Method Not Allowed';
            return;
        }

        // Check if user is logged in as customer
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        // Get user ID from session
        $uid = (int)($_SESSION['user_id'] ?? 0);

        // Get form data from POST request
        // ?? 0 means "use 0 if the value doesn't exist" (default value)
        $appointmentId = (int)($_POST['appointment_id'] ?? 0);
        
        // trim() removes extra spaces from beginning/end of text
        $description = trim((string)($_POST['description'] ?? ''));

        // TODO: Validate input
        // - Check that appointmentId is valid (> 0)
        // - Check that description is not empty
        // - If invalid, set error message and redirect back
        
        // TODO: Verify appointment belongs to this customer
        // - Use Appointments model to check ownership
        // - If not owned by customer, show error and return
        
        // TODO: Get customer_id and vehicle_id from appointment
        // - Query appointments table to get these IDs
        
        // TODO: Insert complaint into database
        // - Create ComplaintModel instance
        // - Call create() method with complaint data
        // - Set status to 'Open' and priority based on customer choice or default to 'Medium'
        
        // TODO: Set success message
        // $_SESSION['flash'] = 'Your complaint has been submitted successfully!';
        
        // TODO: Redirect back to complaint form
        // header('Location: ' . rtrim(BASE_URL, '/') . '/customer/file-complaint');
        // exit;

        // TEMPORARY PLACEHOLDER - Remove when implementing
        echo "Complaint submission endpoint - TO BE IMPLEMENTED";
        echo "<br>Appointment ID: " . htmlspecialchars((string)$appointmentId);
        echo "<br>Description: " . htmlspecialchars($description);
    }
}
