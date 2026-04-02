<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Team Schedule - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
   
<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

  <div class="main">
    
  
  <h2>
    <?= htmlspecialchars(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')) ?>
</h2>

<ul>
    <li><strong>Phone:</strong> <?= htmlspecialchars($member['phone'] ?? 'N/A') ?></li>
    <li><strong>Email:</strong> <?= htmlspecialchars($member['email'] ?? 'N/A') ?></li>
    <li>
        <strong>Address:</strong>
        <?php
            $address = array_filter([
                $member['street_address'] ?? null,
                $member['city'] ?? null,
                $member['state'] ?? null
            ]);
            echo $address ? htmlspecialchars(implode(', ', $address)) : 'N/A';
        ?>
    </li>
</ul>

<a href="<?= BASE_URL ?>/manager/schedule">← Back to Schedule</a>
