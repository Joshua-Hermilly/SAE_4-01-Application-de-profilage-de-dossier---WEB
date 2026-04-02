/**
 * Gestion du filtrage des spécialités et options selon le type/série du bac
 */

document.addEventListener("DOMContentLoaded", function () {

	// ==========================================================
	// 1) RÉCUPÉRATION DES ÉLÉMENTS HTML
	// ==========================================================

	const serieBacSelect       = document.getElementById("filter_serie_bac");
	const typeBacSelect        = document.getElementById("filter_type_bac");
	const specialiteCheckboxes = document.querySelectorAll(".specialite-checkbox");


	// ==========================================================
	// 2) FONCTION POUR CACHER/AFFICHER LA SECTION DES SPÉCIALITÉS
	// ==========================================================

	function hideSpecialiteSection() {
		// Cacher le label de spécialités
		const speLabel = document.querySelector('[data-filter-name="specialite_spe"] label#text');
		if (speLabel) speLabel.style.display = "none";

		// Cacher le conteneur des checkboxes
		const speBlock = document.getElementById("specialite-checkboxes-specialite_spe");
		if (speBlock) speBlock.style.display = "none";

		// Décocher et cacher chaque ligne de spécialité
		specialiteCheckboxes.forEach(cb => {
			if (cb.name === "specialite_spe[]") {
				cb.checked = false;
				const container = cb.closest("label") || cb.parentElement;
				if (container) container.style.display = "none";
			}
		});
	}

	function showSpecialiteSection() {
		// Afficher le label de spécialités
		const speLabel = document.querySelector('[data-filter-name="specialite_spe"] label#text');
		if (speLabel) speLabel.style.display = "block";

		// Afficher le conteneur des checkboxes
		const speBlock = document.getElementById("specialite-checkboxes-specialite_spe");
		if (speBlock) speBlock.style.display = "block";
	}


	// ==========================================================
	// 3) FONCTION POUR CACHER/AFFICHER LA SECTION DES OPTIONS
	// ==========================================================

	function hideOptionSection() {
		// Cacher le label des options
		const optLabel = document.querySelector('[data-filter-name="specialite_opt"] label#text');
		if (optLabel) optLabel.style.display = "none";

		// Cacher le conteneur des checkboxes
		const optBlock = document.getElementById("specialite-checkboxes-specialite_opt");
		if (optBlock) optBlock.style.display = "none";

		// Décocher et cacher chaque ligne d'option
		specialiteCheckboxes.forEach(cb => {
			if (cb.name === "specialite_opt[]") {
				cb.checked = false;
				const container = cb.closest("label") || cb.parentElement;
				if (container) container.style.display = "none";
			}
		});
	}

	function showOptionSection() {
		// Afficher le label des options
		const optLabel = document.querySelector('[data-filter-name="specialite_opt"] label#text');
		if (optLabel) optLabel.style.display = "block";

		// Afficher le conteneur des checkboxes
		const optBlock = document.getElementById("specialite-checkboxes-specialite_opt");
		if (optBlock) optBlock.style.display = "block";
	}


	// ==========================================================
	// 4) INITIALISATION AU CHARGEMENT
	// ==========================================================
	hideSpecialiteSection();
	hideOptionSection();


	// ==========================================================
	// 5) VÉRIFIER SI UNE SÉRIE A DES SPÉCIALITÉS/OPTIONS DISPONIBLES
	// ==========================================================

	function serieHasSpecialites(serieValue) {
		if (!serieValue) return false;

		let found = false;
		specialiteCheckboxes.forEach(checkbox => {
			if (checkbox.getAttribute("data-code") === serieValue && checkbox.name === "specialite_spe[]") {
				found = true;
			}
		});
		return found;
	}

	function serieHasOptions(serieValue) {
		if (!serieValue) return false;

		let found = false;
		specialiteCheckboxes.forEach(checkbox => {
			if (checkbox.getAttribute("data-code") === serieValue && checkbox.name === "specialite_opt[]") {
				found = true;
			}
		});
		return found;
	}


	// ==========================================================
	// 6) FILTRER LES SPÉCIALITÉS ET OPTIONS SELON LA SÉRIE CHOISIE
	// ==========================================================

	function filterSpecialites() {
		const selectedSerieCode = serieBacSelect ? serieBacSelect.value : "";

		// Gérer les spécialités
		if (!selectedSerieCode || !serieHasSpecialites(selectedSerieCode)) {
			hideSpecialiteSection();
		} else {
			showSpecialiteSection();
			specialiteCheckboxes.forEach(checkbox => {
				if (checkbox.name === "specialite_spe[]") {
					const speCode = checkbox.getAttribute("data-code");
					const container = checkbox.closest("label") || checkbox.parentElement;
					if (!container) return;

					if (speCode === selectedSerieCode) {
						container.style.display = "flex";
					} else {
						container.style.display = "none";
						checkbox.checked = false;
					}
				}
			});
		}

		// Gérer les options
		if (!selectedSerieCode || !serieHasOptions(selectedSerieCode)) {
			hideOptionSection();
		} else {
			showOptionSection();
			specialiteCheckboxes.forEach(checkbox => {
				if (checkbox.name === "specialite_opt[]") {
					const optCode = checkbox.getAttribute("data-code");
					const container = checkbox.closest("label") || checkbox.parentElement;
					if (!container) return;

					if (optCode === selectedSerieCode) {
						container.style.display = "flex";
					} else {
						container.style.display = "none";
						checkbox.checked = false;
					}
				}
			});
		}
	}


	// ==========================================================
	// 7) GESTION DU CHANGEMENT DE TYPE DE BAC
	// ==========================================================

	function onTypeBacChange() {
		const val = typeBacSelect ? typeBacSelect.value : "";

		// Reset de la série bac
		if (serieBacSelect) serieBacSelect.value = "";

		// Cache les spécialités et options
		hideSpecialiteSection();
		hideOptionSection();

		// Récupère tous les blocs ayant la classe .bac-filter
		const blocs = document.querySelectorAll(".bac-filter");
		blocs.forEach(bloc => {
			bloc.style.display = "none";
			resetBloc(bloc);
		});

		if (val) {
			const blocToShow = document.querySelector(`[data-bac-type="${val}"]`);
			if (blocToShow) blocToShow.style.display = "block";
		}
	}


	// ==========================================================
	// 8) FONCTION POUR RESET UN BLOC (vider les champs)
	// ==========================================================

	function resetBloc(bloc) {
		const inputs = bloc.querySelectorAll("input, select, textarea");
		inputs.forEach(input => {
			if (input.type === "checkbox" || input.type === "radio") {
				input.checked = false;
			} else {
				input.value = "";
			}
		});
	}


	// ==========================================================
	// 9) ÉVÉNEMENTS : CHANGEMENT DE TYPE/SÉRIE DE BAC
	// ==========================================================

	if (typeBacSelect) typeBacSelect.addEventListener("change", onTypeBacChange);
	if (serieBacSelect) serieBacSelect.addEventListener("change", filterSpecialites);

});
