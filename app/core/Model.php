<?php
namespace app\core;

require_once APP_ROOT . '/core/Database.php';




abstract class Model {
  protected Database $db;
  public function __construct(Database $db){ $this->db = $db; }
}
