<?php

class Service
{
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function all()
    {
        $stmt = $this->db->query("SELECT * FROM services WHERE status = 'active'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
