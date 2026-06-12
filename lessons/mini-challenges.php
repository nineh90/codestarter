<?php
// =====================================================================
// Lektion: Mini-Challenges
// Diese Datei enthält NUR die Inhalte (das $lesson-Array).
// Die Darstellung übernimmt _lesson-template.php.
// Format der Aufgaben: siehe Kommentar in html-basics.php
//
// Hier werden HTML, CSS und JS kombiniert — größere Eingabefelder
// (rows) und bei der letzten Challenge preview 'html-js', damit
// Buttons in der Vorschau wirklich klickbar sind.
// =====================================================================

$lesson = [
    'id'          => 'mini-challenges',
    'title'       => 'Mini-Challenges',
    'icon'        => '🚀',
    'description' => 'Zeit für echte Projekte: Hier kombinierst du HTML, CSS und JavaScript zu kleinen Seiten, die wirklich was können.',

    // Die Intro-Karte wird als erste Karte angezeigt und erklärt,
    // worum es in dieser Lektion überhaupt geht.
    'intro'       => [
        'heading'    => 'Alles zusammen!',
        'paragraphs' => [
            'Jetzt kommt der beste Teil: HTML, CSS und JavaScript arbeiten zusammen — genau so entstehen echte Webseiten. HTML liefert die Struktur, CSS das Aussehen, JavaScript das Verhalten.',
            'Die Challenges hier sind größer als die Aufgaben vorher. Nimm dir Zeit, probier in der Vorschau herum — und wenn du festhängst: Tipp aufklappen oder den KI-Tutor fragen.',
        ],
        'facts'      => [
            'Jede Webseite der Welt besteht aus genau diesen drei Bausteinen.',
            'Profis googeln ständig — niemand kennt alle Tags und Befehle auswendig.',
            'Wenn etwas nicht klappt: kleiner anfangen, Stück für Stück erweitern. So arbeiten echte Entwickler auch.',
        ],
    ],

    'tasks'       => [
        [
            'id'          => 'ch-1-card',
            'title'       => 'Dein Steckbrief',
            'theory'      => 'Eine richtige Seite besteht aus mehreren Bausteinen untereinander. Alles, was du in HTML Basics gelernt hast, kommt jetzt zusammen — du baust deine erste komplette Mini-Seite.',
            'instruction' => 'Bau einen Steckbrief über dich: eine Überschrift mit deinem Namen, ein Bild (nimm https://picsum.photos/200), ein Absatz über dich und eine Liste mit deinen Hobbys.',
            'placeholder' => "<h1>...</h1>\n<img src=\"...\">\n<p>...</p>\n<ul>\n  <li>...</li>\n</ul>",
            'check'       => ['type' => 'contains_all', 'values' => ['<h1>', '<img', '<p>', '<ul>', '<li>']],
            'hint'        => 'Einfach alle Bausteine untereinander schreiben: erst <h1>, dann <img src="...">, dann <p>, dann die <ul> mit ein paar <li> drin.',
            'preview'     => 'html',
            'rows'        => 10,
            'xp'          => 50,
        ],
        [
            'id'          => 'ch-2-style',
            'title'       => 'Style deinen Steckbrief',
            'theory'      => 'Mit einem <style>-Block stylst du HTML direkt auf der Seite: <style> h1 { color: red; } </style> — vor der geschweiften Klammer steht, WAS gestylt wird. Alles aus CSS Basics funktioniert hier.',
            'instruction' => 'Nimm deinen Steckbrief von eben und gib ihm Style: mindestens eine Textfarbe, eine Hintergrundfarbe und runde Ecken.',
            'placeholder' => "<style>\n  body {\n    ...\n  }\n</style>\n\n<h1>...</h1>\n...",
            'check'       => ['type' => 'contains_all', 'values' => ['<style>', 'color:', 'background-color:', 'border-radius:', '</style>']],
            'hint'        => 'Kopier deinen Steckbrief hier rein und setz einen <style>-Block davor, z. B.: <style> body { color: white; background-color: #1e2130; } img { border-radius: 12px; } </style>',
            'preview'     => 'html',
            'rows'        => 14,
            'xp'          => 60,
        ],
        [
            'id'          => 'ch-3-button',
            'title'       => 'Der magische Knopf',
            'theory'      => 'Jetzt alle drei zusammen: HTML, CSS und JavaScript! Ein <button> reagiert auf Klicks — bei onclick steht JavaScript, das beim Klicken läuft: <button onclick="alert(\'Hi!\')">Klick mich</button>. alert(...) öffnet ein Hinweis-Fenster.',
            'instruction' => 'Bau einen Button, der beim Klicken eine Nachricht anzeigt — und klick ihn in der Vorschau an!',
            'placeholder' => '<button onclick="alert(\'...\')">...</button>',
            'check'       => ['type' => 'contains_all', 'values' => ['<button', 'onclick', 'alert']],
            'hint'        => 'Achte auf die Anführungszeichen: außen doppelte für onclick="...", innen einfache für alert(\'...\') — sonst kommen sie sich in die Quere.',
            'preview'     => 'html-js',
            'rows'        => 4,
            'xp'          => 70,
        ],
    ],
];

// Wenn diese Datei nur als Daten-Quelle geladen wird (Dashboard, API),
// rendern wir keine Seite — load_lesson() in config.php setzt dieses Signal.
if (!defined('LESSON_DATA_ONLY')) {
    require __DIR__ . '/_lesson-template.php';
}
