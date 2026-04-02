/**
 * Gestion des boutons de collapse/expand de la barre de filtres
 */

document.addEventListener("DOMContentLoaded", function () {

    // ==========================================================
    // RÉCUPÉRATION DES ÉLÉMENTS
    // ==========================================================

    const collapseBtn = document.getElementById("filter_collapse_btn");
    const expandBtn = document.getElementById("filter_expand_btn");
    const sidebar = document.getElementById("filter_sidebar");
    const closeBar = document.getElementById("filter_close_bar");


    // ==========================================================
    // INITIALISATION AU CHARGEMENT
    // ==========================================================
    // Par défaut : sidebar caché, closeBar visible
    function initializeFilter() {
        if (sidebar) {
            sidebar.classList.add("d-none");
        }
        if (closeBar) {
            closeBar.classList.remove("d-none");
        }
    }

    // Initialiser au chargement
    initializeFilter();


    // ==========================================================
    // ÉVÉNEMENTS : COLLAPSE/EXPAND
    // ==========================================================

    // Bouton de collapse (fermer le filtre et afficher closeBar)
    if (collapseBtn) {
        collapseBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (sidebar) sidebar.classList.add("d-none");
            if (closeBar) closeBar.classList.remove("d-none");
        });
    }

    // Bouton d'expand (afficher le filtre et cacher closeBar)
    if (expandBtn) {
        expandBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (sidebar) sidebar.classList.remove("d-none");
            if (closeBar) closeBar.classList.add("d-none");
        });
    }

});
