/**
 * message.js
 * ------------
 * Comportements JS specifiques a la messagerie (conversation.php).
 */

document.addEventListener('DOMContentLoaded', function () {

    const formulaireMessage = document.querySelector('.message-form');

    if (!formulaireMessage) {
        return; // on n'est pas sur la page de conversation
    }

    const textarea = formulaireMessage.querySelector('textarea[name="contenu"]');
    const boutonEnvoyer = formulaireMessage.querySelector('button[type="submit"]');

    // Empeche l'envoi d'un message vide ou uniquement compose d'espaces
    formulaireMessage.addEventListener('submit', function (event) {
        if (textarea.value.trim() === '') {
            event.preventDefault();
            textarea.focus();
            return;
        }

        // Evite le double-clic pendant l'envoi (double soumission du formulaire)
        boutonEnvoyer.disabled = true;
        boutonEnvoyer.textContent = 'Envoi...';
    });

    // Permet d'envoyer avec Entree (Maj+Entree pour aller a la ligne)
    textarea.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            formulaireMessage.requestSubmit();
        }
    });

    // Place le curseur directement dans le champ de saisie a l'ouverture
    textarea.focus();

});
