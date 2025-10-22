<?php
namespace app\controllers\mechanic;

use app\core\Controller;

class JobsMController extends Controller
{
    public function index(): void
    {
        $this->view('mechanic/jobs/index');  // ← use view(), not render()
    }
}
