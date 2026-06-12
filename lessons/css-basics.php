<?php
// =====================================================================
// Lektion: CSS Basics
// Diese Datei enthält NUR die Inhalte (das $lesson-Array).
// Die Darstellung übernimmt _lesson-template.php.
// Format der Aufgaben: siehe Kommentar in html-basics.php
//
// Bei preview 'css' landet die Antwort als Eigenschaften in der Demo-Box,
// bei 'css-full' ist die Antwort eine komplette CSS-Regel.
// =====================================================================

$lesson = [
    'id'          => 'css-basics',
    'title'       => 'CSS Basics',
    'icon'        => '🎨',
    'description' => 'CSS macht aus grauen Seiten echte Hingucker. Du stylst hier eine Demo-Box — jede Änderung siehst du sofort in der Vorschau.',

    // Die Intro-Karte wird als erste Karte angezeigt und erklärt,
    // worum es in dieser Lektion überhaupt geht.
    'intro'       => [
        'heading'    => 'Was ist CSS?',
        'paragraphs' => [
            'CSS steht für Cascading Style Sheets — die Sprache fürs Aussehen. HTML baut das Skelett, CSS zieht ihm etwas Schickes an: Farben, Schriften, Abstände, runde Ecken, sogar Animationen.',
            'Das Grundmuster ist immer gleich: eigenschaft: wert; — zum Beispiel color: red; für rote Schrift. Der Doppelpunkt trennt, das Semikolon beendet. Mehr Syntax brauchst du am Anfang nicht.',
            'Auch diese Lernseite ist komplett mit CSS gestylt: das dunkle Theme, die Karten, das Lila — alles nur CSS. In den nächsten Karten stylst du selbst eine Demo-Box.',
        ],
        'facts'      => [
            'Es gibt über 500 CSS-Eigenschaften — aber mit etwa 20 baust du schon richtig gute Seiten.',
            'Farben kannst du als Name (red), Hex-Code (#7c6cf7) oder rgb(...) schreiben.',
            'CSS verzeiht Fehler: Was der Browser nicht versteht, ignoriert er einfach stillschweigend.',
        ],
    ],

    'tasks'       => [
        [
            'id'          => 'css-1-color',
            'title'       => 'Textfarbe ändern',
            'theory'      => 'CSS funktioniert immer gleich: eigenschaft: wert; — zum Beispiel color: red; für rote Schrift. Der Doppelpunkt trennt, das Semikolon beendet.',
            'instruction' => 'Mach den Text der Demo-Box rot.',
            'placeholder' => 'color: ...;',
            'check'       => ['type' => 'contains_all', 'values' => ['color:', 'red']],
            'hint'        => 'color: red; — mehr braucht es nicht. Tippe es ins Feld und schau auf die Vorschau!',
            'preview'     => 'css',
            'xp'          => 20,
        ],
        [
            'id'          => 'css-2-background',
            'title'       => 'Hintergrundfarbe',
            'theory'      => 'Für den Hintergrund gibt es background-color. Farben kannst du als Namen schreiben (black, hotpink) oder als Hex-Code (#0f1117 — so wie diese Seite!).',
            'instruction' => 'Gib der Demo-Box einen schwarzen Hintergrund.',
            'placeholder' => 'background-color: ...;',
            'check'       => ['type' => 'contains_all', 'values' => ['background-color:', 'black']],
            'hint'        => 'background-color: black; — achte auf den Bindestrich in der Mitte.',
            'preview'     => 'css',
            'xp'          => 25,
        ],
        [
            'id'          => 'css-3-fontsize',
            'title'       => 'Schriftgröße',
            'theory'      => 'Mit font-size steuerst du die Textgröße. Größen brauchen eine Einheit — meistens px (Pixel). Ohne Einheit ignoriert der Browser den Wert einfach!',
            'instruction' => 'Mach die Schrift der Demo-Box 24px groß.',
            'placeholder' => 'font-size: ...;',
            'check'       => ['type' => 'contains_all', 'values' => ['font-size:', '24px']],
            'hint'        => 'font-size: 24px; — Zahl und Einheit zusammen, ohne Leerzeichen dazwischen.',
            'preview'     => 'css',
            'xp'          => 25,
        ],
        [
            'id'          => 'css-4-radius',
            'title'       => 'Runde Ecken',
            'theory'      => 'border-radius rundet die Ecken ab — das lässt fast alles sofort moderner aussehen. Je größer der Wert, desto runder.',
            'instruction' => 'Gib der Demo-Box runde Ecken mit 12px.',
            'placeholder' => 'border-radius: ...;',
            'check'       => ['type' => 'contains_all', 'values' => ['border-radius:', '12px']],
            'hint'        => 'border-radius: 12px; — probier danach mal 50px aus und schau, was passiert 😄',
            'preview'     => 'css',
            'xp'          => 30,
        ],
        [
            'id'          => 'css-5-hover',
            'title'       => 'Hover-Effekt',
            'theory'      => 'Bisher hast du nur Eigenschaften geschrieben. Eine komplette CSS-Regel hat auch einen Selektor: .demo:hover { ... } bedeutet "wenn die Maus über der Demo-Box ist". Die geschweiften Klammern umschließen die Eigenschaften.',
            'instruction' => 'Schreibe eine komplette Regel: Die Demo-Box soll beim Drüberfahren mit der Maus ihre Farbe ändern. Teste es in der Vorschau!',
            'placeholder' => ".demo:hover {\n  ...\n}",
            'check'       => ['type' => 'contains_all', 'values' => [':hover', '{', '}']],
            'hint'        => 'Zum Beispiel: .demo:hover { background-color: hotpink; } — Selektor, Klammer auf, Eigenschaft, Klammer zu.',
            'preview'     => 'css-full',
            'xp'          => 30,
        ],
    ],
];

// Wenn diese Datei nur als Daten-Quelle geladen wird (Dashboard, API),
// rendern wir keine Seite — load_lesson() in config.php setzt dieses Signal.
if (!defined('LESSON_DATA_ONLY')) {
    require __DIR__ . '/_lesson-template.php';
}
