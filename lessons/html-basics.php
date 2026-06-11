<?php
// =====================================================================
// Lektion: HTML Basics
// Diese Datei enthält NUR die Inhalte (das $lesson-Array).
// Die Darstellung übernimmt _lesson-template.php.
//
// Aufbau einer Aufgabe:
//   id          eindeutige ID (wird in der Datenbank gespeichert)
//   title       Überschrift der Aufgabe
//   theory      kurze Erklärung (1-3 Sätze, kein Schulbuch!)
//   instruction was Jonas tun soll
//   placeholder Beispieltext im Eingabefeld
//   check       wie die Antwort geprüft wird (contains_all = alle
//               Schnipsel müssen vorkommen, Groß/Klein egal)
//   hint        Tipp, falls man nicht weiterkommt
//   preview     Art der Live-Vorschau: 'html', 'css' oder 'css-full'
//   xp          wie viele XP es gibt
// =====================================================================

$lesson = [
    'id'          => 'html-basics',
    'title'       => 'HTML Basics',
    'icon'        => '🌐',
    'description' => 'HTML ist das Skelett jeder Webseite. Hier baust du deine ersten eigenen Bausteine — und siehst sofort, was passiert.',
    'tasks'       => [
        [
            'id'          => 'html-1-heading',
            'title'       => 'Deine erste Überschrift',
            'theory'      => 'Webseiten bestehen aus Tags. Ein Tag ist wie eine Verpackung für Inhalt: <h1> startet eine große Überschrift, </h1> beendet sie. Alles dazwischen wird groß und fett angezeigt.',
            'instruction' => 'Schreibe eine große Überschrift, in der dein Name steht.',
            'placeholder' => '<h1>Dein Text hier</h1>',
            'check'       => ['type' => 'contains_all', 'values' => ['<h1>', '</h1>']],
            'hint'        => 'So sieht es aus: <h1>Jonas</h1> — vergiss den Schrägstrich im End-Tag nicht!',
            'preview'     => 'html',
            'xp'          => 20,
        ],
        [
            'id'          => 'html-2-paragraph',
            'title'       => 'Ein Absatz Text',
            'theory'      => 'Normaler Text gehört in <p>-Tags (p wie "paragraph", also Absatz). Jedes <p> bekommt automatisch etwas Abstand nach oben und unten.',
            'instruction' => 'Schreibe einen Absatz, der erzählt, was du gerade lernst.',
            'placeholder' => '<p>Dein Text hier</p>',
            'check'       => ['type' => 'contains_all', 'values' => ['<p>', '</p>']],
            'hint'        => 'Genau wie bei der Überschrift: <p>Ich lerne HTML!</p>',
            'preview'     => 'html',
            'xp'          => 20,
        ],
        [
            'id'          => 'html-3-link',
            'title'       => 'Dein erster Link',
            'theory'      => 'Links machen das Web zum Web! Ein Link braucht das <a>-Tag und ein Attribut: href sagt, WOHIN der Link führt. Beispiel: <a href="https://google.com">Klick mich</a>',
            'instruction' => 'Baue einen Link zu deiner Lieblingsseite. Der Text dazwischen ist das, worauf man klickt.',
            'placeholder' => '<a href="https://...">Linktext</a>',
            'check'       => ['type' => 'contains_all', 'values' => ['<a', 'href=', '</a>']],
            'hint'        => 'Drei Dinge braucht\'s: <a am Anfang, href="..." mit der Adresse und </a> am Ende.',
            'preview'     => 'html',
            'xp'          => 25,
        ],
        [
            'id'          => 'html-4-image',
            'title'       => 'Ein Bild einbauen',
            'theory'      => 'Bilder kommen mit dem <img>-Tag. Das Attribut src ("source") sagt, wo das Bild liegt. Besonderheit: <img> hat KEIN End-Tag — es verpackt ja nichts.',
            'instruction' => 'Baue ein Bild ein. Du kannst diese Adresse benutzen: https://picsum.photos/300/200',
            'placeholder' => '<img src="https://...">',
            'check'       => ['type' => 'contains_all', 'values' => ['<img', 'src=']],
            'hint'        => 'So geht\'s: <img src="https://picsum.photos/300/200"> — kein </img> nötig!',
            'preview'     => 'html',
            'xp'          => 25,
        ],
        [
            'id'          => 'html-5-list',
            'title'       => 'Eine Liste bauen',
            'theory'      => 'Listen bestehen aus zwei Tags: <ul> ist die Liste selbst ("unordered list"), und jedes <li> ist ein Punkt darin ("list item"). Die <li>-Tags kommen IN das <ul>.',
            'instruction' => 'Baue eine Liste mit mindestens zwei Dingen, die du magst.',
            'placeholder' => "<ul>\n  <li>...</li>\n  <li>...</li>\n</ul>",
            'check'       => ['type' => 'contains_all', 'values' => ['<ul>', '<li>', '</li>', '</ul>']],
            'hint'        => 'Gerüst: <ul> <li>Pizza</li> <li>Gaming</li> </ul> — erst die Liste öffnen, dann die Punkte rein.',
            'preview'     => 'html',
            'xp'          => 30,
        ],
    ],
];

// Wenn diese Datei nur als Daten-Quelle geladen wird (Dashboard, API),
// rendern wir keine Seite — load_lesson() in config.php setzt dieses Signal.
if (!defined('LESSON_DATA_ONLY')) {
    require __DIR__ . '/_lesson-template.php';
}
