// =====================================================================
// lesson.js — macht die Lektionsseiten interaktiv:
//  - prüft Antworten sofort im Browser (ohne Neuladen)
//  - speichert XP über die API
//  - zeigt eine Live-Vorschau des eingegebenen Codes
//  - animiert XP-Zähler und zeigt Level-Up-Meldungen
//
// Ohne JavaScript funktioniert alles trotzdem über normale Formulare —
// dieses Script macht es nur schöner und schneller.
// =====================================================================

const API_BASE = document.body.dataset.api;
const LESSON_ID = document.body.dataset.lesson;

// Grund-Styling für die Vorschau, damit sie zum dunklen Theme passt
const PREVIEW_BASE = `<style>
    body { font-family: system-ui, sans-serif; background: #ffffff; color: #1a1a2e;
           margin: 0; padding: 16px; }
    .demo { background: #7c6cf7; color: #fff; padding: 16px 24px;
            display: inline-block; transition: all 0.3s; }
</style>`;

// Prüft eine Antwort nach den GLEICHEN Regeln wie der Server (config.php).
// Immer kleingeschrieben und ohne Leerzeichen am Rand vergleichen.
function checkAnswer(check, answer) {
    const cleaned = answer.trim().toLowerCase();

    if (check.type === 'contains_all') {
        return check.values.every(part => cleaned.includes(part.toLowerCase()));
    }
    if (check.type === 'equals') {
        return cleaned === check.value.trim().toLowerCase();
    }
    return false;
}

// Baut den Inhalt für die Live-Vorschau, je nach Aufgaben-Typ
function buildPreview(mode, answer) {
    if (mode === 'html') {
        // Die Antwort IST der HTML-Code
        return PREVIEW_BASE + answer;
    }
    if (mode === 'css') {
        // Die Antwort sind CSS-Eigenschaften für die Demo-Box
        return PREVIEW_BASE
            + '<style>.demo {' + answer + '}</style>'
            + '<div class="demo">Ich bin die Demo-Box ✨</div>';
    }
    if (mode === 'css-full') {
        // Die Antwort ist eine komplette CSS-Regel
        return PREVIEW_BASE
            + '<style>' + answer + '</style>'
            + '<div class="demo">Fahr mit der Maus über mich! 🖱️</div>';
    }
    return '';
}

// Lässt eine Zahl sichtbar hochzählen (z. B. den XP-Wert oben rechts)
function animateNumber(element, from, to) {
    const duration = 600; // Millisekunden
    const start = performance.now();

    function step(now) {
        const progress = Math.min((now - start) / duration, 1);
        element.textContent = Math.round(from + (to - from) * progress);
        if (progress < 1) {
            requestAnimationFrame(step);
        }
    }
    requestAnimationFrame(step);
}

// Zeigt eine Meldung unten rechts (z. B. beim Level-Up)
function showToast(text) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = text;
    document.body.appendChild(toast);

    // kurz warten, damit die Einblend-Animation greift
    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}

// Aktualisiert die XP-Anzeige in der Topbar mit den Daten vom Server
function updateXpHeader(data) {
    const xpValue = document.getElementById('xp-value');
    animateNumber(xpValue, parseInt(xpValue.textContent, 10) || 0, data.total_xp);

    document.getElementById('xp-bar').style.width = data.percent + '%';
    document.getElementById('level-pill').textContent =
        'Lv. ' + data.level + ' · ' + data.level_name;
}

// Zählt die "x von y geschafft"-Anzeige der Lektion hoch
function bumpLessonProgress() {
    const counter = document.getElementById('lesson-done-count');
    counter.textContent = document.querySelectorAll('.task.done').length;
}

// Schreibt eine Rückmeldung unter die Aufgabe
function showFeedback(taskElement, state, text) {
    const feedback = taskElement.querySelector('[data-feedback]');
    feedback.className = 'feedback ' + state;
    feedback.textContent = text;
}

// ---------------------------------------------------------------------
// Jede Aufgabe auf der Seite verkabeln
// ---------------------------------------------------------------------
document.querySelectorAll('.task-form').forEach(form => {
    const task = form.closest('.task');
    const textarea = form.querySelector('textarea');
    const check = JSON.parse(form.dataset.check);
    const previewMode = form.dataset.preview;
    const previewBox = task.querySelector('.preview');
    const previewFrame = previewBox.querySelector('iframe');

    // ---- Live-Vorschau: bei jeder Eingabe aktualisieren ----
    if (previewMode) {
        const updatePreview = () => {
            previewFrame.srcdoc = buildPreview(previewMode, textarea.value);
        };
        textarea.addEventListener('input', updatePreview);
        updatePreview(); // einmal direkt beim Laden
    } else {
        previewBox.hidden = true;
    }

    // ---- Absenden: erst lokal prüfen, dann beim Server speichern ----
    form.addEventListener('submit', async event => {
        event.preventDefault(); // kein Neuladen — wir machen das per fetch()

        const answer = textarea.value;

        if (!checkAnswer(check, answer)) {
            showFeedback(task, 'wrong', '❌ Noch nicht ganz — probier\'s nochmal oder schau in den Tipp.');
            return;
        }

        // Der Server prüft nochmal und speichert die XP
        let data;
        try {
            const response = await fetch(API_BASE + 'save-progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    lesson_id: LESSON_ID,
                    task_id: form.dataset.taskId,
                    answer: answer,
                }),
            });
            data = await response.json();
        } catch (error) {
            showFeedback(task, 'wrong', '⚠️ Speichern hat nicht geklappt — ist der Server an?');
            return;
        }

        if (!data.ok) {
            showFeedback(task, 'wrong', '❌ ' + (data.error || 'Da hat etwas nicht geklappt.'));
            return;
        }

        // Geschafft! Aufgabe als erledigt markieren
        task.classList.add('done');
        task.querySelector('.xp-badge').textContent = '✓ erledigt';
        bumpLessonProgress();

        if (data.already_done) {
            showFeedback(task, 'ok', '✅ Richtig! Die Aufgabe hattest du aber schon geschafft.');
        } else {
            showFeedback(task, 'ok', '✅ Stark! +' + data.xp_gained + ' XP');
            updateXpHeader(data);
        }

        if (data.level_up) {
            showToast('🎉 Level Up! Du bist jetzt Level ' + data.level + ' — ' + data.level_name);
        }
    });
});
