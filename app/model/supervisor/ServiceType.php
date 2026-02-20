<?php

class ServiceType
{
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function all()
    {
        $stmt = $this->db->query("SELECT * FROM service_types");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
