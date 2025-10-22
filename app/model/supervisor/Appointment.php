<?php

class Appointment
{
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function allAvailable()
    {
        $sql = "SELECT a.*, s.name AS service_name
                FROM appointments a
                LEFT JOIN services s USING(service_id)
                WHERE a.status IN ('requested', 'confirmed')";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

