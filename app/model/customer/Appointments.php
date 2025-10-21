<?php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

class Appointments
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function getByCustomer(int $customerId): array
    {
        if (!$customerId) return [];

        $sql = "
            SELECT 
                a.appointment_id,
                s.service_name,
                a.branch_name,
                a.date_booked,
                a.time_slot,
                a.est_completion,
                a.status
            FROM appointments a
            JOIN services s ON s.service_id = a.service_id
            WHERE a.customer_id = :cid
            ORDER BY a.date_booked DESC, a.appointment_id DESC
            LIMIT 500
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cid' => $customerId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Normalize to the keys your view expects
        return array_map(function($r) {
            // Derive a simple class for UI: upcoming/completed/cancelled
            $status = (string)$r['status'];
            $class  = 'upcoming';
            if (strcasecmp($status, 'Completed') === 0) $class = 'completed';
            if (strcasecmp($status, 'Cancelled') === 0) $class = 'cancelled';

            // Human date/time
            $date = $r['date_booked'] ? date('d M Y', strtotime($r['date_booked'])) : '';
            $time = $r['time_slot'] ?? '';

            return [
                'appointment_id' => (int)$r['appointment_id'],
                'service'        => (string)$r['service_name'],
                'branch'         => (string)$r['branch_name'],
                'date'           => $date,
                'time'           => $time,
                'status'         => $status,
                'status_class'   => $class,
                'est_completion' => $r['est_completion'] ? date('d M Y', strtotime($r['est_completion'])) : '',
            ];
        }, $rows);
    }

    public function cancelIfCustomerOwns(int $customerId, int $appointmentId): bool
    {
        if (!$customerId || !$appointmentId) return false;

        // Only allow cancel if not already completed/cancelled and belongs to customer, and date is today/future.
        $sql = "
            UPDATE appointments
            SET status = 'Cancelled'
            WHERE appointment_id = :id
              AND customer_id = :cid
              AND status NOT IN ('Completed','Cancelled')
              AND (date_booked >= CURDATE())
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $appointmentId, 'cid' => $customerId]);
    }
}
