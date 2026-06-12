// =====================================================================
// playground.js — die Spielwiese:
//  - verbindet das Eingabefeld mit der Live-Vorschau
//  - speichert Projekte über die API und lädt sie wieder
// =====================================================================

const API_BASE = document.body.dataset.api;

const codeField = document.getElementById('playground-code');
const previewFrame = document.getElementById('playground-frame');
const nameField = document.getElementById('project-name');
const saveButton = document.getElementById('save-project');
const projectList = document.getElementById('project-list');
const newButton = document.getElementById('new-project');
const statusLabel = document.getElementById('project-status');

// Welches gespeicherte Projekt gerade offen ist (null = noch keins)
let currentProjectId = null;

// ---------------------------------------------------------------------
// Live-Vorschau
// ---------------------------------------------------------------------
function updatePlayground() {
    previewFrame.srcdoc = codeField.value;
}

codeField.addEventListener('input', updatePlayground);
updatePlayground(); // einmal direkt beim Laden, damit der Start-Code erscheint

// ---------------------------------------------------------------------
// Projekte speichern & laden
// ---------------------------------------------------------------------

// Kurze Statusmeldung neben den Knöpfen anzeigen
function showStatus(text, isError) {
    statusLabel.className = 'feedback ' + (isError ? 'wrong' : 'ok');
    statusLabel.textContent = text;
    setTimeout(() => { statusLabel.textContent = ''; }, 3000);
}

// Liste der gespeicherten Projekte in das Dropdown füllen
async function refreshProjectList() {
    try {
        const response = await fetch(API_BASE + 'get-projects.php');
        const data = await response.json();
        if (!data.ok) return;

        // erste Zeile ("Gespeicherte Projekte …") behalten, Rest neu aufbauen
        projectList.length = 1;
        data.projects.forEach(project => {
            const option = document.createElement('option');
            option.value = project.id;
            option.textContent = project.name;
            projectList.appendChild(option);
        });
    } catch (error) {
        // kein Drama — Liste bleibt dann einfach leer
    }
}

// Speichern: neues Projekt anlegen oder das geöffnete aktualisieren
saveButton.addEventListener('click', async () => {
    const name = nameField.value.trim();
    if (name === '') {
        showStatus('Gib deinem Projekt erst einen Namen', true);
        nameField.focus();
        return;
    }

    let data;
    try {
        const response = await fetch(API_BASE + 'save-project.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: currentProjectId,
                name: name,
                code: codeField.value,
            }),
        });
        data = await response.json();
    } catch (error) {
        data = { ok: false, error: 'Speichern hat nicht geklappt — ist der Server an?' };
    }

    if (!data.ok) {
        showStatus(data.error, true);
        return;
    }

    currentProjectId = data.id;
    showStatus('Gespeichert ✓', false);
    await refreshProjectList();
    projectList.value = String(currentProjectId);
});

// Auswahl im Dropdown: Projekt laden
projectList.addEventListener('change', async () => {
    if (projectList.value === '') return;

    try {
        const response = await fetch(API_BASE + 'get-projects.php?id=' + projectList.value);
        const data = await response.json();
        if (!data.ok) {
            showStatus(data.error, true);
            return;
        }

        currentProjectId = data.project.id;
        nameField.value = data.project.name;
        codeField.value = data.project.code;
        updatePlayground();
        showStatus('Geladen ✓', false);
    } catch (error) {
        showStatus('Laden hat nicht geklappt', true);
    }
});

// "Neu": ab jetzt wird als neues Projekt gespeichert (Code bleibt stehen)
newButton.addEventListener('click', () => {
    currentProjectId = null;
    nameField.value = '';
    projectList.value = '';
    nameField.focus();
    showStatus('Neues Projekt — vergib einen Namen und speichere', false);
});

refreshProjectList();
