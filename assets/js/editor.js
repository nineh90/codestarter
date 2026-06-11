// =====================================================================
// editor.js — kleine Editor-Verbesserungen für alle Code-Eingabefelder.
// Bewusst selbst gebaut statt CodeMirror: weniger Magie, und Jonas
// kann jede Zeile davon lesen und verstehen.
//
// Was es kann:
//   Tab          → rückt zwei Leerzeichen ein (statt das Feld zu verlassen)
//   Enter        → übernimmt die Einrückung der aktuellen Zeile
//   Strg+Enter   → schickt das Formular ab (= "Prüfen" klicken)
// =====================================================================

/**
 * Fügt Text an der Cursor-Position ein und meldet die Änderung,
 * damit z. B. die Live-Vorschau sofort mitbekommt, dass sich was tat.
 */
function insertAtCursor(field, text) {
    field.setRangeText(text, field.selectionStart, field.selectionEnd, 'end');
    field.dispatchEvent(new Event('input', { bubbles: true }));
}

document.querySelectorAll('textarea').forEach(field => {
    field.addEventListener('keydown', event => {

        // Strg+Enter (oder Cmd+Enter am Mac) = Formular absenden
        if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
            const form = field.closest('form');
            if (form) {
                event.preventDefault();
                form.requestSubmit();
            }
            return;
        }

        // Tab = zwei Leerzeichen einfügen
        if (event.key === 'Tab') {
            event.preventDefault();
            insertAtCursor(field, '  ');
            return;
        }

        // Enter = neue Zeile MIT der Einrückung der aktuellen Zeile
        if (event.key === 'Enter') {
            const cursor = field.selectionStart;
            const lineStart = field.value.lastIndexOf('\n', cursor - 1) + 1;
            const currentLine = field.value.slice(lineStart, cursor);
            const indent = currentLine.match(/^[ \t]*/)[0];

            if (indent.length > 0) {
                event.preventDefault();
                insertAtCursor(field, '\n' + indent);
            }
        }
    });
});
