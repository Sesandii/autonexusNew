<?php
namespace app\model\supervisor;

use PDO;

class Feedback {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllFeedbacks(int $branchId) {
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
                a.appointment_date,
                 CONCAT(v.make, ' ', v.model) AS vehicle
            FROM feedback f
            JOIN appointments a ON f.appointment_id = a.appointment_id
            JOIN branches b ON a.branch_id = b.branch_id
             LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            JOIN customers c ON a.customer_id = c.customer_id
            JOIN users u ON c.user_id = u.user_id
            JOIN services s ON a.service_id = s.service_id
            WHERE b.branch_id = :branch_id
            ORDER BY f.created_at DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['branch_id' => $branchId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveFeedbackReply($data) {
        $stmt = $this->db->prepare("UPDATE feedback 
                                    SET reply_text = :reply_text, 
                                        replied_at = NOW(), 
                                        replied_by = :replied_by, 
                                        replied_status = 'replied' 
                                    WHERE feedback_id = :feedback_id");
        
        return $stmt->execute([
            ':reply_text' => $data['reply_text'],
            ':replied_by' => $data['replied_by'],
            ':feedback_id' => $data['feedback_id']
        ]);
    }
}
