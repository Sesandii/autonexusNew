<?php
/**
 * Standardized Customer Page Header Partial
 * 
 * Usage:
 * <?php include APP_ROOT . '/views/partials/customer-page-header.php'; ?>
 * 
 * Parameters passed from parent view:
 * - $headerIcon (string, optional): Font Awesome class, e.g., 'fa-solid fa-calendar'
 * - $headerTitle (string, required): Page title
 * - $headerSubtitle (string, optional): Page subtitle/description
 * - $headerActionBtn (string, optional): HTML for action button
 */

$icon = $headerIcon ?? '';
$title = $headerTitle ?? 'Page';
$subtitle = $headerSubtitle ?? '';
$actionBtn = $headerActionBtn ?? '';
?>

<header class="page-header">
  <div class="header-left">
    <h1 class="header-title">
      <?php if ($icon): ?>
        <i class="<?= htmlspecialchars($icon) ?>"></i>
      <?php endif; ?>
      <span><?= htmlspecialchars($title) ?></span>
    </h1>
    <?php if ($subtitle): ?>
      <p class="header-subtitle"><?= htmlspecialchars($subtitle) ?></p>
    <?php endif; ?>
  </div>

  <?php if ($actionBtn): ?>
    <div class="header-right">
      <?= $actionBtn ?>
    </div>
  <?php endif; ?>
</header>
