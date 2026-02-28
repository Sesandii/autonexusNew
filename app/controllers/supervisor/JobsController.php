<?php
namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\Job;

class JobsController extends Controller
{
    private Job $jobModel;

    public function __construct()
    {
        $this->jobModel = new Job();
    }

    /** Show all jobs */
    public function index()
    {
        $jobs = $this->jobModel->all();
        $this->render('supervisor/jobs/index', ['jobs' => $jobs]);
    }

    /** Show form to create a job */
    public function create()
    {
        $vehicles = []; // fetch from DB later
        $mechanics = []; // fetch from DB later

        $this->render('supervisor/jobs/create', [
            'vehicles' => $vehicles,
            'mechanics' => $mechanics
        ]);
    }

    /** Handle job creation */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'vehicle_id' => $_POST['vehicle_id'] ?? null,
                'assigned_mechanic_id' => $_POST['assigned_mechanic_id'] ?? null,
                'job_title' => $_POST['job_title'] ?? '',
                'notes' => $_POST['notes'] ?? '',
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->jobModel->create($data);

            header('Location: /autonexus/supervisor/jobs');
            exit;
        }
    }
} // <--- file ends here, no
