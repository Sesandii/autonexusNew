<?php
namespace app\controllers\supervisor;

use app\core\Controller;

class FeedbacksController extends Controller
{
    public function index()
    {
        $this->view('supervisor/feedbacks/index');
    }
}
