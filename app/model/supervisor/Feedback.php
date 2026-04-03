<?php
namespace app\model\supervisor;

use PDO;

class Feedback {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllFeedbacks() {
        $query = "
            SELECT 
                f.feedback_id,
                f.rating,
                f.comment,
                f.created_at,
                b.name,
                f.replied_status,
                f.reply_text,
                s.name AS service_name,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
                a.appointment_date
            FROM feedback f
            JOIN appointments a ON f.appointment_id = a.appointment_id
            JOIN branches b ON a.branch_id = b.branch_id
            JOIN customers c ON a.customer_id = c.customer_id
            JOIN users u ON c.user_id = u.user_id
            JOIN services s ON a.service_id = s.service_id
            ORDER BY f.created_at DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
