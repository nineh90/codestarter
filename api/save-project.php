<?php
// =====================================================================
// api/save-project.php — Speichert ein Spielwiesen-Projekt von Jonas.
// Ohne id wird ein neues Projekt angelegt, mit id ein eigenes
// bestehendes aktualisiert. Antwort: die Projekt-id.
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

$input = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($input)) {
    json_out(['ok' => false, 'error' => 'Ungültige Anfrage'], 400);
}

$project_id = (int) ($input['id'] ?? 0);
$name = trim((string) ($input['name'] ?? ''));
$code = (string) ($input['code'] ?? '');

if ($name === '') {
    json_out(['ok' => false, 'error' => 'Gib deinem Projekt einen Namen'], 422);
}
$name = mb_substr($name, 0, 60);

// 100 KB reichen für jedes Spielwiesen-Projekt locker
if (strlen($code) > 100 * 1024) {
    json_out(['ok' => false, 'error' => 'Das Projekt ist zu groß zum Speichern'], 422);
}

$db = get_db();
$user_id = current_user_id($db);
$now = date('c');

if ($project_id > 0) {
    // Bestehendes Projekt aktualisieren — aber nur das EIGENE
    $stmt = $db->prepare(
        'UPDATE projects SET name = ?, code = ?, updated_at = ?
         WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([$name, $code, $now, $project_id, $user_id]);

    if ($stmt->rowCount() === 0) {
        json_out(['ok' => false, 'error' => 'Projekt nicht gefunden'], 404);
    }
} else {
    $stmt = $db->prepare(
        'INSERT INTO projects (user_id, name, code, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$user_id, $name, $code, $now, $now]);
    $project_id = (int) $db->lastInsertId();
}

json_out(['ok' => true, 'id' => $project_id, 'name' => $name]);
