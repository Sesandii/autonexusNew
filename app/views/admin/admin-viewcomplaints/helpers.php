<?php
/**
 * View helpers for Complaints Management
 */

function badge($status): string
{
    return "<span class=\"badge {$status}\">" . htmlspecialchars(ucwords(str_replace('_', ' ', $status))) . "</span>";
}

function priority($priority): string
{
    return "<span class=\"priority {$priority}\">" . htmlspecialchars(ucfirst($priority)) . "</span>";
}

function slaStatus($slaStatus): string
{
    return "<span class=\"sla {$slaStatus}\">" . htmlspecialchars(ucwords(str_replace('_', ' ', $slaStatus))) . "</span>";
}

function escalationFlag($escalated): string
{
    $class = $escalated ? 'escalated' : 'normal';
    $text = $escalated ? 'Escalated' : 'Normal';
    return "<span class=\"flag {$class}\">{$text}</span>";
}

function linkBtn($href, $label, $icon = 'fa-arrow-right'): string
{
    return "<a class=\"link-btn\" href=\"" . htmlspecialchars($href) . "\">"
        . "<i class=\"fa-solid {$icon}\"></i> " . htmlspecialchars($label) . "</a>";
}

function fieldRow($label, $value): string
{
    return "<div class=\"field\"><span class=\"label\">" . htmlspecialchars($label) . ":</span>"
        . htmlspecialchars($value) . "</div>";
}

function customerInfo($r): string
{
    return htmlspecialchars($r['customer_name']) . "<br>"
        . "<span class=\"muted\">" . htmlspecialchars($r['customer_code'] ?? '') . "</span>";
}

function branchServiceInfo($r): string
{
    return htmlspecialchars($r['branch_name'] ?? '—') . "<br>"
        . "<span class=\"muted\">" . htmlspecialchars($r['service_name'] ?? 'No linked service') . "</span>";
}

function assignedUserInfo($r): string
{
    $name = htmlspecialchars($r['assigned_user_name'] ?? 'Unassigned');
    $role = $r['assigned_user_role'] ?? '';
    return $name . ($role ? "<br><span class=\"muted\">" . htmlspecialchars($role) . "</span>" : "");
}

function vehicleInfo($r): string
{
    $vehicle = trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? '')) ?: '—';
    return "<strong>" . htmlspecialchars($r['vehicle_code'] ?? '—') . "</strong><br>"
        . "<span class=\"muted\">" . htmlspecialchars($vehicle) . "</span>";
}
