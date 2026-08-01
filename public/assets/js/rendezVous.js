/**
 * rendezVous.js
 * ---------------
 * Comportements JS specifiques a la prise de rendez-vous
 * (prendre.php) et a la gestion des disponibilites (disponibilites.php).
 */

document.addEventListener('DOMContentLoaded', function () {

    // --- Page "Prendre rendez-vous" ---------------------------------
    const selectCreneau = document.getElementById('id_dispo');
    if (selectCreneau) {
        const formulairePrendre = selectCreneau.closest('form');

        formulairePrendre.addEventListener('submit', function (event) {
            if (selectCreneau.value === '') {
                event.preventDefault();
                alert('Veuillez selectionner un creneau avant de continuer.');
            }
        });
    }

    // --- Page "Mes disponibilites" (medecin) ------------------------
    const formulaireDispo = document.querySelector('.dispo-form');
    if (formulaireDispo) {
        const heureDebut = document.getElementById('heure_debut');
        const heureFin = document.getElementById('heure_fin');
        const champJour = document.getElementById('jour');

        // Empeche de creer un creneau dans le passe
        const aujourdHui = new Date().toISOString().split('T')[0];
        champJour.setAttribute('min', aujourdHui);

        formulaireDispo.addEventListener('submit', function (event) {
            if (heureFin.value <= heureDebut.value) {
                event.preventDefault();
                alert("L'heure de fin doit etre apres l'heure de debut.");
            }
        });
    }

});
