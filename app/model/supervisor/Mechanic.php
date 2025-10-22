<?php

class Mechanic
{
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function allActive()
    {
        $stmt = $this->db->query("SELECT * FROM mechanics WHERE status = 'active'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
