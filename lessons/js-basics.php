<?php
// =====================================================================
// Lektion: JavaScript Basics
// Diese Datei enthält NUR die Inhalte (das $lesson-Array).
// Die Darstellung übernimmt _lesson-template.php.
// Format der Aufgaben: siehe Kommentar in html-basics.php
//
// preview 'js' führt den Code in der Vorschau wirklich aus und zeigt
// alles, was per console.log ausgegeben wird — Fehler inklusive.
// =====================================================================

$lesson = [
    'id'          => 'js-basics',
    'title'       => 'JavaScript Basics',
    'icon'        => '⚡',
    'description' => 'Mit JavaScript wird deine Seite lebendig: Hier schreibst du echte Programme — und die Vorschau führt sie sofort aus.',
    'tasks'       => [
        [
            'id'          => 'js-1-console',
            'title'       => 'Sag Hallo zur Konsole',
            'theory'      => 'JavaScript kann Dinge ausgeben: console.log(...) schreibt alles, was in den Klammern steht, in die Konsole — hier direkt in die Vorschau. Text braucht Anführungszeichen.',
            'instruction' => 'Gib mit console.log einen Gruß aus — zum Beispiel "Hallo Welt!".',
            'placeholder' => 'console.log("...");',
            'check'       => ['type' => 'contains_all', 'values' => ['console.log', '(', ')']],
            'hint'        => 'console.log("Hallo Welt!"); — Klammern und Anführungszeichen nicht vergessen.',
            'preview'     => 'js',
            'xp'          => 25,
        ],
        [
            'id'          => 'js-2-variable',
            'title'       => 'Deine erste Variable',
            'theory'      => 'Mit let legst du eine Variable an — eine Box mit Namen, in der ein Wert steckt: let name = "Jonas"; Danach kannst du die Box überall benutzen, einfach über ihren Namen.',
            'instruction' => 'Speichere deinen Namen in einer Variable und gib sie mit console.log aus.',
            'placeholder' => "let name = \"...\";\nconsole.log(name);",
            'check'       => ['type' => 'contains_all', 'values' => ['let ', '=', 'console.log']],
            'hint'        => 'Zwei Zeilen: erst let name = "Jonas"; — dann console.log(name); (hier OHNE Anführungszeichen, du willst ja den Inhalt der Box).',
            'preview'     => 'js',
            'xp'          => 25,
        ],
        [
            'id'          => 'js-3-math',
            'title'       => 'Rechnen lassen',
            'theory'      => 'JavaScript ist auch ein Taschenrechner: + - * / funktionieren direkt. Zahlen schreibst du OHNE Anführungszeichen — sonst sind es Texte und 7 * 6 geht schief.',
            'instruction' => 'Lass JavaScript 7 mal 6 ausrechnen und gib das Ergebnis aus.',
            'placeholder' => "let ergebnis = ...;\nconsole.log(ergebnis);",
            'check'       => ['type' => 'contains_all', 'values' => ['7', '6', '*', 'console.log']],
            'hint'        => 'Der Stern * ist das Mal-Zeichen: let ergebnis = 7 * 6; und dann console.log(ergebnis);',
            'preview'     => 'js',
            'xp'          => 30,
        ],
        [
            'id'          => 'js-4-if',
            'title'       => 'Entscheidungen treffen',
            'theory'      => 'Mit if läuft Code nur, WENN etwas stimmt: if (xp > 100) { ... } — die Bedingung steht in runden Klammern, der Code, der dann laufen soll, in geschweiften.',
            'instruction' => 'Lege eine Variable xp mit einem Wert über 100 an. Wenn xp größer als 100 ist, soll "Level Up!" ausgegeben werden.',
            'placeholder' => "let xp = ...;\nif (...) {\n  ...\n}",
            'check'       => ['type' => 'contains_all', 'values' => ['let ', 'if', '>', 'console.log']],
            'hint'        => 'let xp = 150; if (xp > 100) { console.log("Level Up!"); } — probier danach mal einen Wert unter 100 und schau, was passiert.',
            'preview'     => 'js',
            'rows'        => 5,
            'xp'          => 30,
        ],
        [
            'id'          => 'js-5-function',
            'title'       => 'Deine erste Funktion',
            'theory'      => 'Funktionen sind Code-Maschinen: einmal bauen, beliebig oft benutzen. function gruss(name) { console.log("Hi " + name); } baut die Maschine — gruss("Jonas") startet sie. Das + klebt Texte zusammen.',
            'instruction' => 'Schreibe eine Funktion, die jemanden begrüßt, und rufe sie mit einem Namen auf.',
            'placeholder' => "function gruss(name) {\n  ...\n}\ngruss(\"...\");",
            'check'       => ['type' => 'contains_all', 'values' => ['function', '(', ')', '{', '}']],
            'hint'        => 'Erst die Funktion bauen (mit function), dann darunter aufrufen: gruss("Jonas"); — ohne den Aufruf passiert nichts!',
            'preview'     => 'js',
            'rows'        => 6,
            'xp'          => 40,
        ],
    ],
];

// Wenn diese Datei nur als Daten-Quelle geladen wird (Dashboard, API),
// rendern wir keine Seite — load_lesson() in config.php setzt dieses Signal.
if (!defined('LESSON_DATA_ONLY')) {
    require __DIR__ . '/_lesson-template.php';
}
