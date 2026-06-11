// =====================================================================
// playground.js — verbindet das Eingabefeld der Spielwiese mit der
// Live-Vorschau: Was du tippst, ist sofort die Seite im Rahmen rechts.
// =====================================================================

const codeField = document.getElementById('playground-code');
const previewFrame = document.getElementById('playground-frame');

function updatePlayground() {
    previewFrame.srcdoc = codeField.value;
}

codeField.addEventListener('input', updatePlayground);
updatePlayground(); // einmal direkt beim Laden, damit der Start-Code erscheint
