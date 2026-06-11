<?php
// =====================================================================
// config.php — Globale Einstellungen, Datenbank-Verbindung und Helfer.
// Wird von jeder Seite und jedem API-Endpunkt als Erstes eingebunden.
// =====================================================================

// Fehler anzeigen, solange wir lokal entwickeln.
// Auf dem VPS (Produktion) stellen wir das später aus.
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Pfad zur SQLite-Datei (lokal). Auf dem VPS wird hier später MySQL konfiguriert.
define('DB_PATH', __DIR__ . '/data/progress.db');

// Cookie, über das wir Jonas ohne Login wiedererkennen
define('USER_COOKIE', 'lab_user');
define('COOKIE_LIFETIME', 60 * 60 * 24 * 365); // 1 Jahr

// Welche Lektionen es gibt (Reihenfolge = Anzeige auf dem Dashboard)
const LESSON_IDS = ['html-basics', 'css-basics'];

// Level-System: ab wie viel XP welches Level erreicht ist.
// Die ersten Level kommen schnell (Motivation!), später wird es langsamer.
const LEVELS = [
    ['level' => 1,  'min_xp' => 0,    'name' => 'Neuling'],
    ['level' => 2,  'min_xp' => 50,   'name' => 'Entdecker'],
    ['level' => 3,  'min_xp' => 120,  'name' => 'Tüftler'],
    ['level' => 4,  'min_xp' => 220,  'name' => 'Coder'],
    ['level' => 5,  'min_xp' => 350,  'name' => 'Builder'],
    ['level' => 6,  'min_xp' => 520,  'name' => 'Designer'],
    ['level' => 7,  'min_xp' => 730,  'name' => 'Debugger'],
    ['level' => 8,  'min_xp' => 1000, 'name' => 'Profi'],
    ['level' => 9,  'min_xp' => 1350, 'name' => 'Meister'],
    ['level' => 10, 'min_xp' => 1800, 'name' => 'Legende'],
];

// ---------------------------------------------------------------------
// Datenbank
// ---------------------------------------------------------------------

/**
 * Öffnet die Datenbank (einmal pro Request) und legt fehlende Tabellen an.
 * Gibt immer dieselbe PDO-Verbindung zurück.
 */
function get_db(): PDO
{
    static $db = null;
    if ($db !== null) {
        return $db;
    }

    // Den data/-Ordner anlegen, falls er noch nicht existiert
    $data_dir = dirname(DB_PATH);
    if (!is_dir($data_dir)) {
        mkdir($data_dir, 0775, true);
    }

    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tabellen anlegen, falls es sie noch nicht gibt
    $db->exec('CREATE TABLE IF NOT EXISTS users (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        token      TEXT UNIQUE NOT NULL,
        created_at TEXT NOT NULL
    )');
    $db->exec('CREATE TABLE IF NOT EXISTS completed_tasks (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id      INTEGER NOT NULL,
        task_id      TEXT NOT NULL,
        xp           INTEGER NOT NULL,
        completed_at TEXT NOT NULL,
        UNIQUE (user_id, task_id)
    )');

    return $db;
}

/**
 * Erkennt den Benutzer über sein Cookie wieder — oder legt einen neuen an.
 * Gibt die Benutzer-ID zurück. Muss VOR der ersten HTML-Ausgabe laufen,
 * weil eventuell ein Cookie gesetzt wird.
 */
function current_user_id(PDO $db): int
{
    $token = (string) ($_COOKIE[USER_COOKIE] ?? '');

    if ($token !== '') {
        $stmt = $db->prepare('SELECT id FROM users WHERE token = ?');
        $stmt->execute([$token]);
        $id = $stmt->fetchColumn();
        if ($id !== false) {
            return (int) $id;
        }
    }

    // Unbekannt → neuen Benutzer anlegen und Cookie setzen
    $token = bin2hex(random_bytes(16));
    $stmt = $db->prepare('INSERT INTO users (token, created_at) VALUES (?, ?)');
    $stmt->execute([$token, date('c')]);

    setcookie(USER_COOKIE, $token, [
        'expires'  => time() + COOKIE_LIFETIME,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    return (int) $db->lastInsertId();
}

/**
 * Summiert alle bisher gesammelten XP eines Benutzers.
 */
function get_total_xp(PDO $db, int $user_id): int
{
    $stmt = $db->prepare('SELECT COALESCE(SUM(xp), 0) FROM completed_tasks WHERE user_id = ?');
    $stmt->execute([$user_id]);
    return (int) $stmt->fetchColumn();
}

/**
 * Gibt die IDs aller Aufgaben zurück, die der Benutzer schon geschafft hat.
 */
function get_completed_task_ids(PDO $db, int $user_id): array
{
    $stmt = $db->prepare('SELECT task_id FROM completed_tasks WHERE user_id = ?');
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Speichert eine geschaffte Aufgabe.
 * Gibt true zurück, wenn sie NEU war — false, wenn sie schon erledigt war
 * (dank UNIQUE-Constraint gibt es nie doppelte XP).
 */
function save_task_result(PDO $db, int $user_id, string $task_id, int $xp): bool
{
    $stmt = $db->prepare(
        'INSERT OR IGNORE INTO completed_tasks (user_id, task_id, xp, completed_at)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$user_id, $task_id, $xp, date('c')]);
    return $stmt->rowCount() > 0;
}

// ---------------------------------------------------------------------
// Level & XP
// ---------------------------------------------------------------------

/**
 * Rechnet aus den Gesamt-XP alles aus, was die Anzeige braucht:
 * aktuelles Level, Level-Name, Fortschritt in Prozent bis zum nächsten Level.
 */
function xp_stats(int $total_xp): array
{
    $current = LEVELS[0];
    $next = null;

    foreach (LEVELS as $lvl) {
        if ($total_xp >= $lvl['min_xp']) {
            $current = $lvl;
        } elseif ($next === null) {
            $next = $lvl;
            break;
        }
    }

    if ($next === null) {
        // Höchstes Level erreicht
        $percent = 100;
        $xp_to_next = 0;
        $next_level_name = null;
    } else {
        $span = $next['min_xp'] - $current['min_xp'];
        $percent = (int) round(($total_xp - $current['min_xp']) / $span * 100);
        $xp_to_next = $next['min_xp'] - $total_xp;
        $next_level_name = $next['name'];
    }

    return [
        'level'           => $current['level'],
        'name'            => $current['name'],
        'percent'         => $percent,
        'xp_to_next'      => $xp_to_next,
        'next_level_name' => $next_level_name,
    ];
}

// ---------------------------------------------------------------------
// Lektionen
// ---------------------------------------------------------------------

/**
 * Lädt nur die DATEN einer Lektion (das $lesson-Array), ohne die Seite
 * zu rendern. Wird vom Dashboard und der API benutzt.
 */
function load_lesson(string $lesson_id): ?array
{
    // basename() verhindert, dass jemand per "../" fremde Dateien lädt
    $file = __DIR__ . '/lessons/' . basename($lesson_id) . '.php';
    if (!is_file($file)) {
        return null;
    }

    // Signal an die Lektionsdatei: nur Daten liefern, keine Seite ausgeben
    if (!defined('LESSON_DATA_ONLY')) {
        define('LESSON_DATA_ONLY', true);
    }

    $lesson = null;
    require $file; // setzt $lesson
    return $lesson;
}

/**
 * Lädt alle Lektionen (für das Dashboard).
 */
function get_all_lessons(): array
{
    $lessons = [];
    foreach (LESSON_IDS as $id) {
        $lesson = load_lesson($id);
        if ($lesson !== null) {
            $lessons[] = $lesson;
        }
    }
    return $lessons;
}

/**
 * Sucht eine Aufgabe anhand ihrer ID in einer Lektion.
 */
function find_task(array $lesson, string $task_id): ?array
{
    foreach ($lesson['tasks'] as $task) {
        if ($task['id'] === $task_id) {
            return $task;
        }
    }
    return null;
}

/**
 * Prüft, ob eine Antwort richtig ist.
 * Immer case-insensitive und mit trim() — Tippstil soll keine Rolle spielen.
 * WICHTIG: Diese Funktion ist die "Wahrheit" — der Browser prüft zwar auch
 * (für sofortiges Feedback), aber XP gibt es nur, wenn der Server zustimmt.
 */
function check_answer(array $task, string $answer): bool
{
    $cleaned = mb_strtolower(trim($answer));
    $check = $task['check'];

    if ($check['type'] === 'contains_all') {
        // Alle geforderten Schnipsel müssen in der Antwort vorkommen
        foreach ($check['values'] as $needle) {
            if (!str_contains($cleaned, mb_strtolower($needle))) {
                return false;
            }
        }
        return true;
    }

    if ($check['type'] === 'equals') {
        return $cleaned === mb_strtolower(trim($check['value']));
    }

    return false;
}

// ---------------------------------------------------------------------
// HTML-Ausgabe
// ---------------------------------------------------------------------

/**
 * Kurzform für htmlspecialchars — macht Text sicher für die HTML-Ausgabe.
 */
function h(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Gibt den Seitenanfang aus: <head>, Topbar mit Logo und XP-Anzeige.
 * $base_path ist der relative Weg zum Projektroot ('' oder '../').
 * $body_attrs sind fertige (bereits escapte) Attribute fürs <body>-Tag.
 */
function render_page_start(string $title, string $base_path, int $total_xp, string $body_attrs = ''): void
{
    $stats = xp_stats($total_xp);
    ?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> · Jonas Coding Lab</title>
    <link rel="stylesheet" href="<?= h($base_path) ?>assets/css/style.css">
</head>
<body <?= $body_attrs ?>>
<header class="topbar">
    <a class="logo" href="<?= h($base_path) ?>index.php">⚡ Jonas Coding Lab</a>
    <div class="xp-pill">
        <span class="level-label" id="level-pill">Lv. <?= $stats['level'] ?> · <?= h($stats['name']) ?></span>
        <span class="xp-bar"><!-- width ist dynamisch, deshalb ausnahmsweise inline -->
            <span class="xp-bar-fill" id="xp-bar" style="width: <?= $stats['percent'] ?>%"></span>
        </span>
        <span class="xp-label"><span id="xp-value"><?= $total_xp ?></span> XP</span>
    </div>
</header>
<?php
}

/**
 * Gibt das Seitenende aus und bindet die gewünschten JS-Dateien ein.
 */
function render_page_end(string $base_path, array $script_files = []): void
{
    foreach ($script_files as $file) {
        echo '<script src="' . h($base_path) . 'assets/js/' . h($file) . '"></script>' . "\n";
    }
    echo "</body>\n</html>\n";
}
