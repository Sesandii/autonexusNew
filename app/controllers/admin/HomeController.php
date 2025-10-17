<?php
namespace app\controllers\admin;

use app\core\Controller;

class HomeController extends Controller {
  public function index(){
    $this->view('home/index', []);
  }
}
