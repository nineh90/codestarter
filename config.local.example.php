<?php
// =====================================================================
// config.local.example.php — VORLAGE für deine lokalen Geheimnisse.
//
// So geht's:
//   1. Diese Datei kopieren und als config.local.php speichern
//   2. Die Werte unten eintragen
// config.local.php ist gitignored und landet NIE im Repository.
// =====================================================================

// ---- KI-Tutor -------------------------------------------------------
// Funktioniert mit jedem OpenAI-kompatiblen Chat-Endpunkt.
//
// Beispiel Google Gemini (günstig/kostenloses Kontingent):
//   URL:    https://generativelanguage.googleapis.com/v1beta/openai/chat/completions
//   Modell: gemini-2.0-flash
//
// Beispiel OpenAI:
//   URL:    https://api.openai.com/v1/chat/completions
//   Modell: gpt-4o-mini

define('AI_API_URL', '');
define('AI_API_KEY', '');
define('AI_MODEL', '');

// ---- Adminbereich ---------------------------------------------------
// Passwort für /admin/ — leer lassen = Adminbereich bleibt gesperrt.
define('ADMIN_PASSWORD', '');
