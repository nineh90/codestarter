<?php
// =====================================================================
// admin/index.php — Adminbereich für Nils: Fortschritt und Lernstatistik
// ansehen. Bewusst NUR lesen — Inhalte werden direkt in den
// Lektionsdateien gepflegt (kein Web-CMS, keine unnötige Komplexität).
//
// Zugang: Passwort aus config.local.php (ADMIN_PASSWORD).
// Die Seite ist nirgends verlinkt — Jonas stolpert nicht darüber.
// =====================================================================

require_once __DIR__ . '/../config.php';

session_start();

// Redirect-Ziel: die aktuell aufgerufene URL ohne Query-String.
// Ein relatives "Location: index.php" wäre falsch, wenn die Seite als
// /admin (ohne Slash) geöffnet wurde — der eingebaute PHP-Server leitet
// nicht auf /admin/ um, und der Browser landet dann auf /index.php,
// also im Nutzerbereich statt im Adminbereich.
$self_url = strtok($_SERVER['REQUEST_URI'], '?');

// ---- Logout ----
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: ' . $self_url);
    exit;
}

// ---- Login-Versuch ----
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    // hash_equals vergleicht in konstanter Zeit (kein Timing-Raten möglich)
    if (ADMIN_PASSWORD !== '' && hash_equals(ADMIN_PASSWORD, (string) $_POST['password'])) {
        $_SESSION['is_admin'] = true;
        header('Location: ' . $self_url);
        exit;
    }
    $login_error = 'Falsches Passwort.';
}

$is_admin = !empty($_SESSION['is_admin']);

// null = keine XP-Anzeige in der Topbar (das hier ist Nils' Bereich)
render_page_start('Adminbereich', '../', null);
?>
<main class="container">

<?php if (ADMIN_PASSWORD === ''): ?>

    <header class="lesson-head">
        <h1>🔧 Adminbereich</h1>
        <p>Der Adminbereich ist noch gesperrt, weil kein Passwort gesetzt ist.</p>
    </header>
    <article class="task">
        <p class="theory">So schaltest du ihn frei:</p>
        <p class="instruction">1. <code>config.local.example.php</code> als <code>config.local.php</code> kopieren<br>
           2. Dort <code>ADMIN_PASSWORD</code> setzen<br>
           3. Diese Seite neu laden</p>
    </article>

<?php elseif (!$is_admin): ?>

    <header class="lesson-head">
        <h1>🔧 Adminbereich</h1>
        <p>Bitte einloggen.</p>
    </header>
    <article class="task">
        <form method="post" class="task-form">
            <input type="password" name="password" placeholder="Passwort" autofocus>
            <div class="task-actions">
                <button type="submit" class="btn">Einloggen</button>
            </div>
        </form>
        <?php if ($login_error !== ''): ?>
            <p class="feedback wrong">❌ <?= h($login_error) ?></p>
        <?php endif; ?>
    </article>

<?php else: ?>

    <?php
    // ---- Daten für die Statistik zusammensammeln ----
    $db = get_db();
    $lessons = get_all_lessons();

    // Zuordnung Aufgabe → Lektion und Aufgaben-Anzahl je Lektion
    $task_to_lesson = [];
    $lesson_task_count = [];
    foreach ($lessons as $lesson) {
        $lesson_task_count[$lesson['id']] = count($lesson['tasks']);
        foreach ($lesson['tasks'] as $task) {
            $task_to_lesson[$task['id']] = $lesson['id'];
        }
    }

    // Alle Benutzer mit XP, Aufgaben- und Projektzahl
    $users = $db->query(
        'SELECT u.id, u.created_at,
                COALESCE(SUM(c.xp), 0)  AS xp,
                COUNT(c.id)             AS done_tasks,
                (SELECT COUNT(*) FROM projects p WHERE p.user_id = u.id) AS projects
         FROM users u
         LEFT JOIN completed_tasks c ON c.user_id = u.id
         GROUP BY u.id
         ORDER BY xp DESC'
    )->fetchAll(PDO::FETCH_ASSOC);

    // Erledigte Aufgaben je Benutzer und Lektion
    $per_lesson = []; // user_id => lesson_id => count
    foreach ($db->query('SELECT user_id, task_id FROM completed_tasks') as $row) {
        $lesson_id = $task_to_lesson[$row['task_id']] ?? null;
        if ($lesson_id !== null) {
            $per_lesson[$row['user_id']][$lesson_id] = ($per_lesson[$row['user_id']][$lesson_id] ?? 0) + 1;
        }
    }

    // Aktivität: erledigte Aufgaben pro Tag (letzte 14 Tage mit Aktivität)
    $activity = $db->query(
        'SELECT substr(completed_at, 1, 10) AS day, COUNT(*) AS cnt, SUM(xp) AS xp
         FROM completed_tasks GROUP BY day ORDER BY day DESC LIMIT 14'
    )->fetchAll(PDO::FETCH_ASSOC);
    $max_per_day = max(1, ...array_map(fn ($row) => (int) $row['cnt'], $activity ?: [['cnt' => 1]]));

    // Letzte Abschlüsse
    $recent = $db->query(
        'SELECT user_id, task_id, xp, completed_at
         FROM completed_tasks ORDER BY completed_at DESC LIMIT 10'
    )->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <header class="lesson-head admin-head">
        <h1>🔧 Adminbereich</h1>
        <p>Lesen ja, anfassen nein — Inhalte werden in den Lektionsdateien gepflegt.</p>
        <a class="back-link" href="?logout">Ausloggen</a>
    </header>

    <article class="task">
        <h2>👤 Benutzer & Fortschritt</h2>
        <?php if (count($users) === 0): ?>
            <p class="theory">Noch keine Benutzer — sobald jemand die Seite öffnet, taucht er hier auf.</p>
        <?php else: ?>
            <table class="admin-table">
                <tr>
                    <th>Benutzer</th><th>Level</th><th>XP</th>
                    <?php foreach ($lessons as $lesson): ?>
                        <th><?= h($lesson['icon']) ?></th>
                    <?php endforeach; ?>
                    <th>Projekte</th><th>Dabei seit</th>
                </tr>
                <?php foreach ($users as $user): ?>
                    <?php $stats = xp_stats((int) $user['xp']); ?>
                    <tr>
                        <td>#<?= (int) $user['id'] ?></td>
                        <td>Lv. <?= $stats['level'] ?> · <?= h($stats['name']) ?></td>
                        <td><?= (int) $user['xp'] ?></td>
                        <?php foreach ($lessons as $lesson): ?>
                            <td><?= (int) ($per_lesson[$user['id']][$lesson['id']] ?? 0) ?>/<?= $lesson_task_count[$lesson['id']] ?></td>
                        <?php endforeach; ?>
                        <td><?= (int) $user['projects'] ?></td>
                        <td><?= h(substr((string) $user['created_at'], 0, 10)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </article>

    <article class="task">
        <h2>📈 Aktivität (Aufgaben pro Tag)</h2>
        <?php if (count($activity) === 0): ?>
            <p class="theory">Noch keine Aktivität.</p>
        <?php else: ?>
            <?php foreach ($activity as $day): ?>
                <div class="stat-row">
                    <span class="stat-day"><?= h($day['day']) ?></span>
                    <span class="stat-bar"><!-- width ist dynamisch, deshalb ausnahmsweise inline -->
                        <span class="stat-bar-fill" style="width: <?= (int) round((int) $day['cnt'] / $max_per_day * 100) ?>%"></span>
                    </span>
                    <span class="stat-value"><?= (int) $day['cnt'] ?> Aufgaben · <?= (int) $day['xp'] ?> XP</span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </article>

    <article class="task">
        <h2>🕘 Letzte Abschlüsse</h2>
        <?php if (count($recent) === 0): ?>
            <p class="theory">Noch nichts abgeschlossen.</p>
        <?php else: ?>
            <table class="admin-table">
                <tr><th>Wann</th><th>Benutzer</th><th>Aufgabe</th><th>XP</th></tr>
                <?php foreach ($recent as $row): ?>
                    <tr>
                        <td><?= h(str_replace('T', ' ', substr((string) $row['completed_at'], 0, 16))) ?></td>
                        <td>#<?= (int) $row['user_id'] ?></td>
                        <td><?= h($row['task_id']) ?></td>
                        <td>+<?= (int) $row['xp'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </article>

    <article class="task">
        <h2>🤖 KI-Tutor</h2>
        <p class="theory">
            Status: <?= ai_is_configured()
                ? '✅ eingerichtet (Modell: ' . h(AI_MODEL) . ')'
                : '❌ nicht eingerichtet — AI_API_URL, AI_API_KEY und AI_MODEL in config.local.php setzen' ?>
        </p>
    </article>

<?php endif; ?>

</main>
<?php render_page_end('../'); ?>
