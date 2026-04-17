<?php
// app/views/customer/appointments/show.php
$a    = $appointment ?? [];
$base = rtrim(BASE_URL, '/');
$appointmentsCssVersion = @filemtime(dirname(APP_ROOT) . '/public/assets/css/customer/appointments.css') ?: time();

// Status mapping for display
$statusMap = [
    'requested'  => ['label' => 'Pending', 'class' => 'pending'],
    'confirmed'  => ['label' => 'Upcoming', 'class' => 'upcoming'],
    'in_service' => ['label' => 'In Service', 'class' => 'ongoing'],
    'completed'  => ['label' => 'Completed', 'class' => 'completed'],
    'cancelled'  => ['label' => 'Cancelled', 'class' => 'cancelled'],
];
$rawStatus = $a['status'] ?? '';
$statusInfo = $statusMap[$rawStatus] ?? ['label' => ucfirst($rawStatus), 'class' => 'pending'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details - AutoNexus</title>
    <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
    <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/dashboard.css">
    <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/appointments.css?v=<?= (int)$appointmentsCssVersion ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .appointment-details-main {
            padding: 20px 30px;
            background: #f5f6fa;
            min-height: 100vh;
        }
        .details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .details-header h1 {
            color: #1a1a2e;
            font-size: 1.8rem;
            margin: 0;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.3s;
        }
        .back-btn:hover {
            background: #5a6268;
        }
        .details-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .details-card h2 {
            color: #1a1a2e;
            font-size: 1.2rem;
            margin: 0 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #e74c3c;
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .detail-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .detail-item label {
            display: block;
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .detail-item span {
            display: block;
            color: #1a1a2e;
            font-size: 1.05rem;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-badge.upcoming {
            background: #d4edda;
            color: #155724;
        }
        .status-badge.ongoing {
            background: #d1ecf1;
            color: #0c5460;
        }
        .status-badge.completed {
            background: #c3e6cb;
            color: #155724;
        }
        .status-badge.cancelled {
            background: #f5c6cb;
            color: #721c24;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            flex-wrap: wrap;
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            font-size: 0.95rem;
        }
        .btn-reschedule {
            background: #007bff;
            color: white;
        }
        .btn-reschedule:hover {
            background: #0056b3;
        }
        .btn-cancel {
            background: #dc3545;
            color: white;
        }
        .btn-cancel:hover {
            background: #c82333;
        }
        .btn-track {
            background: #28a745;
            color: white;
        }
        .btn-track:hover {
            background: #218838;
        }
        @media (max-width: 768px) {
            .appointment-details-main {
                padding: 15px;
            }
            .details-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>
    
    <main class="appointment-details-main customer-layout-main">
        <div class="details-header">
            <h1><i class="fas fa-calendar-alt"></i> Appointment Details</h1>
            <a href="<?= $base ?>/customer/appointments" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Appointments
            </a>
        </div>

        <?php if (empty($a)): ?>
            <div class="details-card">
                <p>Appointment not found.</p>
            </div>
        <?php else: ?>
            <!-- Appointment Info Card -->
            <div class="details-card">
                <h2><i class="fas fa-info-circle"></i> Appointment Information</h2>
                <div class="details-grid">
                    <div class="detail-item">
                        <label>Appointment ID</label>
                        <span>#<?= htmlspecialchars((string)($a['appointment_id'] ?? '')) ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Status</label>
                        <span class="status-badge <?= $statusInfo['class'] ?>"><?= $statusInfo['label'] ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Date</label>
                        <span><?= htmlspecialchars(date('F j, Y', strtotime($a['appointment_date'] ?? ''))) ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Time</label>
                        <span><?= htmlspecialchars(date('g:i A', strtotime($a['appointment_time'] ?? '00:00:00'))) ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Created On</label>
                        <span><?= htmlspecialchars(date('M j, Y g:i A', strtotime($a['created_at'] ?? ''))) ?></span>
                    </div>
                </div>
            </div>

            <!-- Service Info Card -->
            <div class="details-card">
                <h2><i class="fas fa-tools"></i> Service Information</h2>
                <div class="details-grid">
                    <div class="detail-item">
                        <label>Service Type</label>
                        <span><?= htmlspecialchars((string)($a['service_name'] ?? 'N/A')) ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Service Description</label>
                        <span><?= htmlspecialchars((string)($a['service_description'] ?? 'N/A')) ?></span>
                    </div>
                    <?php if (!empty($a['work_order_id'])): ?>
                    <div class="detail-item">
                        <label>Work Order ID</label>
                        <span>#<?= htmlspecialchars((string)$a['work_order_id']) ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Work Order Status</label>
                        <span><?= htmlspecialchars(ucfirst($a['work_order_status'] ?? 'N/A')) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Vehicle Info Card -->
            <div class="details-card">
                <h2><i class="fas fa-car"></i> Vehicle Information</h2>
                <div class="details-grid">
                    <div class="detail-item">
                        <label>Vehicle</label>
                        <span><?= htmlspecialchars((string)($a['make'] ?? '') . ' ' . ($a['model'] ?? '')) ?></span>
                    </div>
                    <div class="detail-item">
                        <label>License Plate</label>
                        <span><?= htmlspecialchars((string)($a['license_plate'] ?? 'N/A')) ?></span>
                    </div>
                    <?php if (!empty($a['vehicle_year'])): ?>
                    <div class="detail-item">
                        <label>Year</label>
                        <span><?= htmlspecialchars((string)$a['vehicle_year']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Branch Info Card -->
            <div class="details-card">
                <h2><i class="fas fa-map-marker-alt"></i> Branch Information</h2>
                <div class="details-grid">
                    <div class="detail-item">
                        <label>Branch Name</label>
                        <span><?= htmlspecialchars((string)($a['branch_name'] ?? 'N/A')) ?></span>
                    </div>
                    <?php if (!empty($a['branch_city'])): ?>
                    <div class="detail-item">
                        <label>City</label>
                        <span><?= htmlspecialchars((string)$a['branch_city']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="details-card">
                <div class="action-buttons">
                    <?php if (in_array($rawStatus, ['requested', 'confirmed'])): ?>
                        <a href="<?= $base ?>/customer/book?rebook=<?= (int)$a['appointment_id'] ?>" class="action-btn btn-reschedule">
                            <i class="fas fa-calendar-plus"></i> Reschedule
                        </a>
                        <form method="POST" action="<?= $base ?>/customer/appointments/cancel" style="display:inline;">
                            <input type="hidden" name="appointment_id" value="<?= (int)$a['appointment_id'] ?>">
                            <button type="submit" class="action-btn btn-cancel" onclick="return confirm('Are you sure you want to cancel this appointment?');">
                                <i class="fas fa-times"></i> Cancel Appointment
                            </button>
                        </form>
                    <?php endif; ?>
                    <?php if (in_array($rawStatus, ['in_service', 'confirmed'])): ?>
                        <a href="<?= $base ?>/customer/track-services" class="action-btn btn-track">
                            <i class="fas fa-tasks"></i> Track Service
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
