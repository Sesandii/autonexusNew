<?php
namespace app\controllers\supervisor;

use app\core\Controller;

class ComplaintsController extends Controller
{
    public function index()
    {
        // Include the DB config
        require_once __DIR__ . '/../../../config/config.php';

        // ✅ Connect to DB
        $conn = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT, DB_SOCKET ?: null);

        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        // ✅ Fetch complaints
        $sql = "SELECT * FROM complaints ORDER BY created_at DESC";
        $result = $conn->query($sql);

        $complaints = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $complaints[] = $row;
            }
        }

        $conn->close();

        // ✅ Pass data to view
        $this->view('supervisor/complaints/index', ['complaints' => $complaints]);
    }
}

