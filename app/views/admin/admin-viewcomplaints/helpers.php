<?php /* Admin view: renders admin-viewcomplaints/helpers page. */ ?>
<?php
/**
 * View helpers for Complaints Management
 */

function badge($status): string
{
    $colors = [
        'open' => ['bg' => '#fef2f2', 'text' => '#b91c1c'],
        'in_progress' => ['bg' => '#fff7ed', 'text' => '#c2410c'],
        'resolved' => ['bg' => '#ecfdf5', 'text' => '#047857'],
        'closed' => ['bg' => '#eef2ff', 'text' => '#4338ca'],
    ];
    $style = $colors[$status] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
    $label = htmlspecialchars(ucwords(str_replace('_', ' ', $status)));
    return "<span style=\"display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; background:{$style['bg']}; color:{$style['text']};\">{$label}</span>";
}

function priority($priority): string
{
    $colors = [
        'low' => '#2563eb',
        'medium' => '#d97706',
        'high' => '#dc2626',
    ];
    $color = $colors[$priority] ?? '#374151';
    $label = htmlspecialchars(ucfirst($priority));
    return "<span style=\"color:{$color}; font-weight:700; font-size:12px;\">{$label}</span>";
}

function slaStatus($slaStatus): string
{
    $colors = [
        'healthy' => '#16a34a',
        'due_soon' => '#d97706',
        'breached' => '#dc2626',
    ];
    $color = $colors[$slaStatus] ?? '#374151';
    $label = htmlspecialchars(ucwords(str_replace('_', ' ', $slaStatus)));
    return "<span style=\"color:{$color}; font-weight:700; font-size:12px;\">{$label}</span>";
}

function escalationFlag($escalated): string
{
    if ($escalated) {
        return "<span style=\"display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; background:#fee2e2; color:#991b1b;\">Escalated</span>";
    }
    return "<span style=\"display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; background:#f3f4f6; color:#475569;\">Normal</span>";
}

function linkBtn($href, $label, $icon = 'fa-arrow-right'): string
{
    return "<a class=\"link-btn\" href=\"" . htmlspecialchars($href) . "\" style=\"display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:8px; background:#ecfdf5; color:#047857; border:1px solid #d1fae5; font-size:12px; font-weight:600; text-decoration:none; white-space:nowrap;\">"
        . "<i class=\"fa-solid {$icon}\" style=\"font-size:10px;\"></i> " . htmlspecialchars($label) . "</a>";
}

function customerInfo($r): string
{
    return "<strong>" . htmlspecialchars($r['customer_name']) . "</strong><br>"
        . "<span style=\"font-size:12px; color:#6b7280;\">" . htmlspecialchars($r['customer_code'] ?? '') . "</span>";
}

function branchServiceInfo($r): string
{
    return htmlspecialchars($r['branch_name'] ?? '—') . "<br>"
        . "<span style=\"font-size:12px; color:#6b7280;\">" . htmlspecialchars($r['service_name'] ?? 'No linked service') . "</span>";
}

function assignedUserInfo($r): string
{
    $name = htmlspecialchars($r['assigned_user_name'] ?? 'Unassigned');
    $role = $r['assigned_user_role'] ?? '';
    return $name . ($role ? "<br><span style=\"font-size:12px; color:#6b7280;\">" . htmlspecialchars($role) . "</span>" : "");
}

function vehicleInfo($r): string
{
    $vehicle = trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? '')) ?: '—';
    return "<strong>" . htmlspecialchars($r['vehicle_code'] ?? '—') . "</strong><br>"
        . "<span style=\"font-size:12px; color:#6b7280;\">" . htmlspecialchars($vehicle) . "</span>";
}
