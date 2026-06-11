<?php
// =====================================================================
// playground.php — Die Spielwiese: ein freier Code-Editor mit
// Live-Vorschau. Keine Aufgaben, keine Punkte — einfach ausprobieren.
// =====================================================================

require_once __DIR__ . '/config.php';

$db = get_db();
$user_id = current_user_id($db);
$total_xp = get_total_xp($db, $user_id);

// Start-Code, damit das Feld nicht leer und einschüchternd ist
$starter_code = <<<'HTML'
<h1>Meine Seite</h1>
<p>Bau hier, was du willst — alles ist erlaubt!</p>

<style>
  body { font-family: sans-serif; }
  h1 { color: #7c6cf7; }
</style>
HTML;

render_page_start('Spielwiese', '', $total_xp);
?>
<main class="container">
    <a class="back-link" href="index.php">← Zurück zum Dashboard</a>

    <header class="lesson-head">
        <h1>🧪 Spielwiese</h1>
        <p>Hier gibt es keine Aufgaben und keine Punkte. Schreib links HTML, CSS und JavaScript — rechts siehst du sofort, was passiert. Kaputt machen kannst du nichts!</p>
    </header>

    <noscript>
        <p class="feedback wrong">Die Spielwiese braucht JavaScript im Browser — sonst bleibt die Vorschau leer.</p>
    </noscript>

    <div class="playground-grid">
        <textarea id="playground-code" rows="20" spellcheck="false" autocomplete="off"><?= h($starter_code) ?></textarea>
        <div class="preview">
            <span class="preview-label">Live-Vorschau</span>
            <!-- Scripte und alert() sind hier erlaubt, alles andere bleibt im Sandkasten -->
            <iframe id="playground-frame" sandbox="allow-scripts allow-modals" title="Vorschau der Spielwiese"></iframe>
        </div>
    </div>
</main>
<?php render_page_end('', ['editor.js', 'playground.js']); ?>
