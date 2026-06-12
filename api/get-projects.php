<?php
// =====================================================================
// api/get-projects.php — Liefert die gespeicherten Spielwiesen-Projekte.
// Ohne Parameter: Liste (id, name, updated_at) der eigenen Projekte.
// Mit ?id=...: ein einzelnes eigenes Projekt inklusive Code.
// =====================================================================

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

$db = get_db();
$user_id = current_user_id($db);

// Einzelnes Projekt laden?
if (isset($_GET['id'])) {
    $stmt = $db->prepare(
        'SELECT id, name, code FROM projects WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([(int) $_GET['id'], $user_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($project === false) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Projekt nicht gefunden'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['ok' => true, 'project' => $project], JSON_UNESCAPED_UNICODE);
    exit;
}

// Sonst: Liste aller eigenen Projekte, neueste zuerst
$stmt = $db->prepare(
    'SELECT id, name, updated_at FROM projects WHERE user_id = ? ORDER BY updated_at DESC'
);
$stmt->execute([$user_id]);

echo json_encode(['ok' => true, 'projects' => $stmt->fetchAll(PDO::FETCH_ASSOC)], JSON_UNESCAPED_UNICODE);
