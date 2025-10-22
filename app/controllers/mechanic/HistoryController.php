<?php
namespace app\controllers\mechanic;

use app\core\Controller;

class HistoryController extends Controller
{
    public function index()
    {
        // Later you can fetch mechanic's completed jobs or vehicle history from DB here
        $this->view('mechanic/history/index');
    }
}
