<?php
// =====================================================================
// _lesson-template.php — Gemeinsames Grundgerüst für ALLE Lektionsseiten.
// Erwartet, dass die Lektionsdatei vorher das Array $lesson definiert hat.
// Lektionsinhalte gehören NICHT hierher, nur Darstellung und Ablauf.
// =====================================================================

require_once __DIR__ . '/../config.php';

$db = get_db();
$user_id = current_user_id($db);

// ---- Abgabe ohne JavaScript (Fallback per normalem Formular-POST) ----
// Mit JavaScript übernimmt lesson.js das Prüfen und Speichern ohne Neuladen.
$feedback = [];     // task_id => 'ok' oder 'wrong'
$old_answers = [];  // damit die Eingabe bei falscher Antwort nicht verloren geht

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = (string) ($_POST['task_id'] ?? '');
    $answer = (string) ($_POST['answer'] ?? '');
    $task = find_task($lesson, $task_id);

    if ($task !== null) {
        if (check_answer($task, $answer)) {
            save_task_result($db, $user_id, $task_id, (int) $task['xp']);
            // Redirect verhindert doppeltes Absenden beim Neuladen der Seite
            $self = basename((string) $_SERVER['PHP_SELF']);
            header('Location: ' . $self . '?ok=' . urlencode($task_id) . '#task-' . urlencode($task_id));
            exit;
        }
        $feedback[$task_id] = 'wrong';
        $old_answers[$task_id] = $answer;
    }
}

// Nach erfolgreichem POST landet man hier mit ?ok=<task_id>
if (isset($_GET['ok'])) {
    $feedback[(string) $_GET['ok']] = 'ok';
}

$total_xp = get_total_xp($db, $user_id);
$completed = get_completed_task_ids($db, $user_id);

// Wie viele Aufgaben DIESER Lektion sind geschafft?
$lesson_task_ids = array_column($lesson['tasks'], 'id');
$done_count = count(array_intersect($lesson_task_ids, $completed));

render_page_start(
    $lesson['title'],
    '../',
    $total_xp,
    'data-api="../api/" data-lesson="' . h($lesson['id']) . '"'
    . ' data-ai="' . (ai_is_configured() ? '1' : '0') . '"'
);
?>
<main class="container">
    <a class="back-link" href="../index.php">← Zurück zum Dashboard</a>

    <header class="lesson-head">
        <h1><?= h($lesson['icon']) ?> <?= h($lesson['title']) ?></h1>
        <p><?= h($lesson['description']) ?></p>
        <p class="lesson-progress" id="lesson-progress" data-total="<?= count($lesson['tasks']) ?>">
            <span id="lesson-done-count"><?= $done_count ?></span> von <?= count($lesson['tasks']) ?> Aufgaben geschafft
        </p>
    </header>

    <?php foreach ($lesson['tasks'] as $i => $task): ?>
        <?php
        $is_done = in_array($task['id'], $completed, true);
        $state = $feedback[$task['id']] ?? null;
        ?>
        <article class="task<?= $is_done ? ' done' : '' ?>" id="task-<?= h($task['id']) ?>">
            <header class="task-head">
                <span class="task-number"><?= $i + 1 ?></span>
                <h2><?= h($task['title']) ?></h2>
                <span class="xp-badge"><?= $is_done ? '✓ erledigt' : '+' . (int) $task['xp'] . ' XP' ?></span>
            </header>

            <p class="theory"><?= h($task['theory']) ?></p>
            <p class="instruction">👉 <?= h($task['instruction']) ?></p>

            <form method="post" class="task-form"
                  data-task-id="<?= h($task['id']) ?>"
                  data-check="<?= h(json_encode($task['check'], JSON_UNESCAPED_UNICODE)) ?>"
                  data-preview="<?= h($task['preview'] ?? '') ?>">
                <input type="hidden" name="task_id" value="<?= h($task['id']) ?>">
                <textarea name="answer" rows="<?= (int) ($task['rows'] ?? 3) ?>" spellcheck="false" autocomplete="off"
                          placeholder="<?= h($task['placeholder'] ?? '') ?>"><?= h($old_answers[$task['id']] ?? '') ?></textarea>
                <div class="task-actions">
                    <button type="submit" class="btn">Prüfen</button>
                    <details class="hint">
                        <summary>💡 Tipp anzeigen</summary>
                        <p><?= h($task['hint']) ?></p>
                    </details>
                </div>
            </form>

            <p class="feedback<?= $state !== null ? ' ' . $state : '' ?>" data-feedback>
                <?php if ($state === 'ok'): ?>✅ Stark! Aufgabe geschafft.<?php endif; ?>
                <?php if ($state === 'wrong'): ?>❌ Noch nicht ganz — probier's nochmal oder schau in den Tipp.<?php endif; ?>
            </p>

            <!-- KI-Tutor: wird von lesson.js eingeblendet, wenn eine Antwort
                 falsch war und der Tutor in config.local.php eingerichtet ist -->
            <div class="ai-help" hidden>
                <button type="button" class="btn-ghost ai-button">🤖 Erklär mir das</button>
                <p class="ai-answer" hidden></p>
            </div>

            <div class="preview">
                <span class="preview-label">Live-Vorschau</span>
                <!-- sandbox="" = die Vorschau darf keine Scripte ausführen -->
                <iframe sandbox="" title="Vorschau: <?= h($task['title']) ?>"></iframe>
            </div>
        </article>
    <?php endforeach; ?>
</main>
<?php render_page_end('../', ['editor.js', 'lesson.js']); ?>
