<?php
namespace app\controllers\Receptionist;

use app\core\Controller;

class ReceptionistD extends Controller
{
    public function index(): void
    {
        $this->view('Receptionist/Dashboard/dashboard');
    }

}
