# Jonas Coding Lab — CLAUDE.md

## Projektübersicht

Eine interaktive Lernsoftware für Programmieranfänger, gebaut für Jonas (~17, hat schon etwas selbst ausprobiert).
Entwickelt von Nils gemeinsam mit Claude (Fable 5) als Coding-Partner.

Ziel: Jonas soll motiviert Webentwicklung lernen — HTML, CSS, JavaScript, später PHP —
durch sofort sichtbare Ergebnisse, kleine Aufgaben und echte Projekte.

> **Stand (2026-06-11):** Phase 1–3 sind umgesetzt — siehe Features.
> Als Nächstes: Backlog-Themen nach Absprache (oder VPS-Deployment).

---

## Stack

- **Backend:** PHP 8.4 (lokal via eingebautem PHP-Server, Produktion auf Hostinger VPS)
- **Frontend:** HTML, CSS, Vanilla JavaScript
- **Datenbank:** SQLite (lokal, einfach) → MySQL (VPS, Produktion)
- **KI-Tutor:** beliebiger OpenAI-kompatibler Chat-Endpunkt (z. B. Gemini Flash, GPT-4o-mini) — URL/Key/Modell in `config.local.php`, ohne Konfiguration blendet sich das Feature einfach aus
- **Keine Frameworks** — alles von Hand, damit Jonas versteht was passiert

---

## Entwicklung & Befehle

```bash
# Lokalen Dev-Server starten (aus dem Projektroot):
php -S localhost:8000

# Sobald .htaccess-Routing genutzt wird: index.php als Router-Script angeben,
# denn der eingebaute PHP-Server ignoriert .htaccess/mod_rewrite:
php -S localhost:8000 index.php

# SQLite-DB inspizieren (kein sqlite3-CLI installiert, aber PHP hat pdo_sqlite):
php -r '$db = new PDO("sqlite:data/progress.db"); var_dump($db->query("SELECT * FROM progress")->fetchAll(PDO::FETCH_ASSOC));'
```

Hinweise:
- Lokal gibt es kein Apache/XAMPP — alles läuft über den eingebauten PHP-Server.
  `.htaccess` greift erst auf dem VPS; lokal muss Routing über `index.php` funktionieren.
- Es gibt (noch) keine Tests, keinen Linter und keinen Build-Schritt — bewusst einfach gehalten.

---

## Projektstruktur

Die Struktur liegt direkt im Projektroot (`codestarter/`):

```
codestarter/
├── index.php              # Startseite / Dashboard
├── playground.php         # Spielwiese: freier Editor + Live-Vorschau, Projekte speicherbar
├── config.php             # DB-Verbindung, Helfer (XP, Lektionen, KI, Rendering)
├── config.local.example.php # Vorlage für config.local.php (KI-Key, Admin-Passwort — gitignored)
├── .htaccess              # URL-Routing (mod_rewrite) — kommt später, erst auf dem VPS relevant
│
├── /lessons/              # Lerneinheiten: je Datei nur ein $lesson-Array
│   ├── _lesson-template.php   # gemeinsames Grundgerüst aller Lektionsseiten
│   ├── html-basics.php
│   ├── css-basics.php
│   ├── js-basics.php
│   └── mini-challenges.php    # kombinierte Aufgaben (HTML+CSS+JS)
│
├── /api/                  # AJAX-Endpunkte (XP speichern, Fortschritt etc.)
│   ├── save-progress.php  # prüft Antworten serverseitig nochmal (Anti-Schummel)
│   ├── get-progress.php
│   ├── explain-error.php  # KI-Tutor: erklärt falsche Antworten (ohne Lösung)
│   ├── save-project.php   # Spielwiesen-Projekte speichern
│   └── get-projects.php   # Spielwiesen-Projekte auflisten/laden
│
├── /assets/
│   ├── /css/style.css     # Dark Theme, alle Farben als CSS-Variablen
│   ├── /js/               # editor.js, lesson.js, playground.js
│   └── /img/
│
├── /admin/                # Adminbereich für Nils (read-only, Passwort in config.local.php)
│   └── index.php          # Login + Fortschritt/Statistik, nirgends verlinkt
│
└── /data/                 # SQLite-Datei (lokal, gitignored)
    └── progress.db
```

Neue Lektion anlegen: Datei in `/lessons/` nach dem Muster der bestehenden
(`$lesson`-Array + Template-Require am Ende) und die ID in `LESSON_IDS`
in `config.php` eintragen — mehr ist nicht nötig.

---

## Architektur-Prinzipien

- **Modular aufgebaut** — jede Lerneinheit ist eine eigene PHP-Datei, leicht erweiterbar
- **Keine unnötige Komplexität** — kein MVC, kein ORM, kein Composer im ersten Schritt
- **Progressive Enhancement** — funktioniert ohne JS, wird mit JS besser
- **API-ready** — `/api/`-Endpunkte sind sauber getrennt für spätere KI-Integration
- **Kommentierter Code** — jede Funktion erklärt, warum sie existiert

---

## Design-Vorgaben

- Dunkles Theme (z. B. `#0f1117` Hintergrund, `#1e2130` Cards)
- Akzentfarbe: leuchtendes Lila/Cyan (`#7c6cf7` / `#00d4ff`)
- Schrift: System-Font-Stack oder `Inter` via Google Fonts
- Motivierende Micro-Animations (XP-Counter, Level-Up)
- Mobile-first, aber Desktop ist primär

---

## Lernprinzip

- Kurze Theorie → direkt Aufgabe → sofortiges Ergebnis sichtbar
- Fehler sind okay, Fehlermeldungen werden erklärt (nicht versteckt)
- XP-System: jede abgeschlossene Aufgabe gibt Punkte
- Level-System: Level 1–10, sichtbarer Fortschritt
- Sprachton: locker, auf Augenhöhe — kein Schul-Feeling

---

## Features

### Phase 1 — MVP
- [x] Dashboard mit Fortschrittsanzeige
- [x] Lerneinheit: HTML Basics (5 Aufgaben)
- [x] Lerneinheit: CSS Basics (5 Aufgaben)
- [x] XP-System (via AJAX + SQLite/MySQL)
- [x] Fortschritt speichern (kein Login nötig, Cookie/Session)

### Phase 2
- [x] Lerneinheit: JavaScript Basics
- [x] Inline Code-Editor (selbst gebaut: Tab, Auto-Einrückung, Strg+Enter — bewusst ohne CodeMirror, damit Jonas den Code lesen kann)
- [x] Live-Vorschau direkt im Browser (HTML/CSS-Rendering + JS-Ausführung mit Konsolen-Ausgabe, sandboxed iframe)
- [x] Kleine Challenges / Mini-Projekte (eigene Lerneinheit + freie Spielwiese)

### Phase 3
- [x] KI-Tutor einbauen (günstiges Modell, erklärt Fehler — Button „🤖 Erklär mir das" bei falschen Antworten, verrät nie die Lösung)
- [x] Adminbereich für Nils (Fortschritt sehen — bewusst read-only: Inhalte werden direkt in den Lektionsdateien gepflegt, kein Web-CMS)
- [x] Lernstatistiken (Aktivität pro Tag, Fortschritt je Lektion, letzte Abschlüsse)
- [x] Eigene Projekte von Jonas speicherbar (Spielwiese: speichern, laden, weiterbauen)

---

## Coding-Regeln

- PHP-Dateien immer mit `<?php` öffnen, niemals `?>` am Ende
- Variablennamen auf Englisch, sprechend (`$user_xp` nicht `$x`)
- Funktionen kommentieren: was macht sie, welche Parameter, was gibt sie zurück
- Kein Inline-CSS, kein Inline-JS (außer kleine Ausnahmen mit Kommentar)
- Jede neue Funktion erst in `config.php` oder eigenem Helper, nie doppelt schreiben
- SQL: immer Prepared Statements, nie direkte String-Interpolation

---

## Zusammenarbeit Nils ↔ Claude

- Claude schlägt Struktur vor, Nils entscheidet
- Änderungen immer inkrementell — eine Sache nach der anderen
- Vor jeder neuen Phase: kurze Abstimmung was als nächstes kommt
- Code wird erklärt, nicht nur geliefert — Jonas soll es später lesen können

---

## Später möglich (Backlog)

- Multiplayer-Challenges (Jonas vs. Freunde)
- GitHub-Integration (Jonas commitet seine Lösungen)
- Eigene kleine Spiele als Coding-Aufgabe
- Zertifikat nach abgeschlossenem Modul