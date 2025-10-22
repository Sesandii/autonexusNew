<?php
namespace app\controllers\supervisor;

use app\core\Controller;

class FeedbacksController extends Controller
{
    public function index()
    {
        // Later, you can connect this to your Feedback model
        $this->view('supervisor/feedbacks/index');
    }
}
