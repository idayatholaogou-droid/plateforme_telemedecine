/**
 * main.js
 * ---------
 * Comportements JS communs a toutes les pages du site
 * (menu, confirmations generiques, petites ameliorations UX).
 */

document.addEventListener('DOMContentLoaded', function () {

    // Fait defiler automatiquement la fenetre de messages vers le bas
    // (utile sur la page de conversation, pour voir le dernier message)
    const messagesListe = document.querySelector('.messages-liste');
    if (messagesListe) {
        messagesListe.scrollTop = messagesListe.scrollHeight;
    }

    // Fait disparaitre automatiquement les alertes de succes apres 5 secondes
    const alertesSucces = document.querySelectorAll('.alerte-succes');
    alertesSucces.forEach(function (alerte) {
        setTimeout(function () {
            alerte.style.transition = 'opacity 0.5s ease';
            alerte.style.opacity = '0';
            setTimeout(function () {
                alerte.remove();
            }, 500);
        }, 5000);
    });

});
