# Jonas Coding Lab — CLAUDE.md

## Projektübersicht

Eine interaktive Lernsoftware für Programmieranfänger, gebaut für Jonas (~17, hat schon etwas selbst ausprobiert).
Entwickelt von Nils gemeinsam mit Claude (Fable 5) als Coding-Partner.

Ziel: Jonas soll motiviert Webentwicklung lernen — HTML, CSS, JavaScript, später PHP —
durch sofort sichtbare Ergebnisse, kleine Aufgaben und echte Projekte.

> **Stand (2026-06-11):** Noch kein Code — Phase 1 (MVP) startet als Nächstes.
> Dieses Dokument ist die Spezifikation, nicht die Beschreibung von Bestehendem.

---

## Stack

- **Backend:** PHP 8.4 (lokal via eingebautem PHP-Server, Produktion auf Hostinger VPS)
- **Frontend:** HTML, CSS, Vanilla JavaScript
- **Datenbank:** SQLite (lokal, einfach) → MySQL (VPS, Produktion)
- **KI-Tutor:** Kostenloses/günstiges Modell (z. B. Gemini Flash, GPT-4o-mini) — wird später eingebaut
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
├── config.php             # DB-Verbindung, globale Settings
├── .htaccess              # URL-Routing (mod_rewrite)
│
├── /lessons/              # Lerneinheiten als PHP-Dateien
│   ├── html-basics.php
│   ├── css-basics.php
│   └── js-basics.php
│
├── /api/                  # AJAX-Endpunkte (XP speichern, Fortschritt etc.)
│   ├── save-progress.php
│   └── get-progress.php
│
├── /assets/
│   ├── /css/
│   ├── /js/
│   └── /img/
│
├── /admin/                # Später: Adminbereich für Nils
│   └── index.php
│
└── /data/                 # SQLite-Datei (lokal)
    └── progress.db
```

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
- [ ] Dashboard mit Fortschrittsanzeige
- [ ] Lerneinheit: HTML Basics (5 Aufgaben)
- [ ] Lerneinheit: CSS Basics (5 Aufgaben)
- [ ] XP-System (via AJAX + SQLite/MySQL)
- [ ] Fortschritt speichern (kein Login nötig, Cookie/Session)

### Phase 2
- [ ] Lerneinheit: JavaScript Basics
- [ ] Inline Code-Editor (CodeMirror oder ähnliches)
- [ ] Live-Vorschau direkt im Browser
- [ ] Kleine Challenges / Mini-Projekte

### Phase 3
- [ ] KI-Tutor einbauen (günstiges Modell, erklärt Fehler)
- [ ] Adminbereich für Nils (Inhalte pflegen, Fortschritt sehen)
- [ ] Lernstatistiken
- [ ] Eigene Projekte von Jonas speicherbar

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