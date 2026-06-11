<?php
// =====================================================================
// api/save-progress.php — Speichert eine geschaffte Aufgabe.
// Wird von lesson.js per fetch() aufgerufen (JSON rein, JSON raus).
//
// Wichtig: Der Server prüft die Antwort SELBST nochmal. Dem Browser
// allein vertrauen wir nicht — sonst könnte man sich XP erschummeln. 😉
// =====================================================================

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

/**
 * Schickt eine JSON-Antwort und beendet das Script.
 */
function json_out(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['ok' => false, 'error' => 'Nur POST erlaubt'], 405);
}

// JSON-Body lesen (Fallback: normale Formulardaten)
$input = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$lesson_id = (string) ($input['lesson_id'] ?? '');
$task_id = (string) ($input['task_id'] ?? '');
$answer = (string) ($input['answer'] ?? '');

if (!in_array($lesson_id, LESSON_IDS, true)) {
    json_out(['ok' => false, 'error' => 'Unbekannte Lektion'], 400);
}

$lesson = load_lesson($lesson_id);
$task = $lesson !== null ? find_task($lesson, $task_id) : null;
if ($task === null) {
    json_out(['ok' => false, 'error' => 'Unbekannte Aufgabe'], 400);
}

if (!check_answer($task, $answer)) {
    json_out(['ok' => false, 'error' => 'Die Antwort ist noch nicht richtig'], 422);
}

$db = get_db();
$user_id = current_user_id($db);

// Level VOR dem Speichern merken, um ein Level-Up zu erkennen
$stats_before = xp_stats(get_total_xp($db, $user_id));

$is_new = save_task_result($db, $user_id, $task_id, (int) $task['xp']);

$total_xp = get_total_xp($db, $user_id);
$stats = xp_stats($total_xp);

json_out([
    'ok'           => true,
    'already_done' => !$is_new,
    'xp_gained'    => $is_new ? (int) $task['xp'] : 0,
    'total_xp'     => $total_xp,
    'level'        => $stats['level'],
    'level_name'   => $stats['name'],
    'percent'      => $stats['percent'],
    'level_up'     => $stats['level'] > $stats_before['level'],
]);
