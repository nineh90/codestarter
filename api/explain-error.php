<?php
// =====================================================================
// api/explain-error.php — Der KI-Tutor: erklärt, warum eine Antwort
// noch nicht stimmt, ohne die Lösung zu verraten.
// Wird von lesson.js aufgerufen, wenn Jonas auf "Erklär mir das" klickt.
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

if (!ai_is_configured()) {
    json_out(['ok' => false, 'error' => 'Der KI-Tutor ist noch nicht eingerichtet (config.local.php)'], 503);
}

$input = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($input)) {
    json_out(['ok' => false, 'error' => 'Ungültige Anfrage'], 400);
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

// Sehr lange Eingaben kappen — schützt vor Unsinn und spart API-Kosten
$answer = trim(mb_substr($answer, 0, 2000));

$system_prompt =
    'Du bist ein freundlicher Coding-Tutor für Jonas, 17 Jahre alt, Programmieranfänger. '
    . 'Antworte auf Deutsch, locker und auf Augenhöhe, maximal 4 kurze Sätze. '
    . 'Erkläre, was an seiner Lösung noch nicht passt, und gib einen Schubs in die '
    . 'richtige Richtung. Hat Jonas noch gar nichts geschrieben, erkläre die Aufgabe '
    . 'in eigenen Worten und gib ihm einen Startpunkt. Verrate NIEMALS die komplette '
    . 'Lösung — Jonas soll selbst draufkommen. Fehler sind okay, mach ihm Mut.';

$user_message =
    "Aufgabe: " . $task['instruction'] . "\n"
    . "Theorie dazu: " . $task['theory'] . "\n"
    . "Die Prüfung erwartet, dass diese Bausteine vorkommen (nur für dich, nicht wörtlich verraten): "
    . implode(', ', $task['check']['values'] ?? []) . "\n\n"
    . ($answer === ''
        ? "Jonas hat noch nichts geschrieben — er möchte die Aufgabe erklärt bekommen."
        : "Jonas' bisherige Antwort:\n" . $answer);

$explanation = ask_ai_tutor($system_prompt, $user_message);

if ($explanation === null) {
    json_out(['ok' => false, 'error' => 'Der KI-Tutor antwortet gerade nicht — probier es später nochmal'], 502);
}

json_out(['ok' => true, 'explanation' => trim($explanation)]);
