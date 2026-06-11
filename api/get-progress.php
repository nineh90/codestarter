<?php
// =====================================================================
// api/get-progress.php — Liefert den aktuellen Stand als JSON:
// XP, Level und welche Aufgaben schon geschafft sind.
// =====================================================================

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

$db = get_db();
$user_id = current_user_id($db);
$total_xp = get_total_xp($db, $user_id);
$stats = xp_stats($total_xp);

echo json_encode([
    'ok'         => true,
    'total_xp'   => $total_xp,
    'level'      => $stats['level'],
    'level_name' => $stats['name'],
    'percent'    => $stats['percent'],
    'completed'  => get_completed_task_ids($db, $user_id),
], JSON_UNESCAPED_UNICODE);
