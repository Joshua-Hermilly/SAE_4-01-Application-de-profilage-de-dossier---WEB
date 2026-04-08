/*------------------------*/
/* Gestion des groupes    */
/*------------------------*/

function getSelectedCodes()
{
	const codes  = [];
	const inputs = document.querySelectorAll('input.cb');

	inputs.forEach((input) =>
	{
		if (input.id === 'cbTous' || input.disabled) { return; }
		if (!input.checked) { return; }

		const code = input.id.replace(/^cb/, '');
		if (code) { codes.push(code); }
	});

	return codes;
}

function initCreationGroupe()
{
	const btnCreerGroupe = document.getElementById('btnCreerGroupe');
	if (btnCreerGroupe)
	{
		btnCreerGroupe.addEventListener('click', () =>
		{
			const codes = getSelectedCodes();
			const info  = document.getElementById('createGroupSelectionInfo');
			if (info)
			{
				info.textContent = codes.length === 0
					? "Aucun dossier sélectionné. Sélectionnez au moins un dossier."
					: codes.length + " dossier(s) sélectionné(s).";
			}

			const modalEl = document.getElementById('createGroupModal');
			if (!modalEl || typeof bootstrap === 'undefined') { return; }
			const modal = new bootstrap.Modal(modalEl);
			modal.show();
		});
	}

	const formulaireCreationGroupe = document.getElementById('createGroupForm');
	if (formulaireCreationGroupe)
	{
		formulaireCreationGroupe.addEventListener('submit', async (evenement) =>
		{
			evenement.preventDefault();

			const errorDiv = document.getElementById('createGroupError');
			if (errorDiv)
			{
				errorDiv.classList.add('d-none');
				errorDiv.textContent = '';
			}

			const boutonCreerGroupe = document.querySelector('#createGroupModal .modal-footer button[type="submit"]');
			const definirEtatSoumission = (enCours) =>
			{
				if (!boutonCreerGroupe) { return; }
				boutonCreerGroupe.disabled = enCours;
			};

			const champNom     = document.getElementById('createGroupNom');
			const champCouleur = document.getElementById('createGroupCouleur');
			const champNote    = document.getElementById('createGroupNote');

			const nomGroupe = champNom ? champNom.value.trim() : '';
			const couleur   = champCouleur && champCouleur.value ? champCouleur.value : '#FF8800';
			const texteNote = champNote ? champNote.value : '';
			const note      = texteNote !== '' ? parseFloat(texteNote) : null;
			const codes     = getSelectedCodes();
			const filtres   = (typeof filtresCourant !== 'undefined' && Object.keys(filtresCourant).length)
				? filtresCourant
				: (typeof serialiserFiltres === 'function' ? serialiserFiltres() : {});

			if (!nomGroupe)
			{
				if (errorDiv)
				{
					errorDiv.textContent = 'Le nom du groupe est obligatoire.';
					errorDiv.classList.remove('d-none');
				}
				return;
			}

			if (note !== null && (note < 0 || note > 20))
			{
				if (errorDiv)
				{
					errorDiv.textContent = 'La note doit être comprise entre 0 et 20.';
					errorDiv.classList.remove('d-none');
				}
				return;
			}

			if (!codes.length)
			{
				if (errorDiv)
				{
					errorDiv.textContent = 'Aucun dossier sélectionné. Sélectionnez au moins un dossier pour créer un groupe.';
					errorDiv.classList.remove('d-none');
				}
				return;
			}
			definirEtatSoumission(true);

			try
			{
				const reponse = await fetch('./creerGroupe.php',
				{
					method : 'POST',
					headers: { 'Content-Type': 'application/json' },
					body   : JSON.stringify({ nom: nomGroupe, couleur, note_dossier: note, codes, filters: filtres })
				});

				const donnees = await reponse.json();
				if (!reponse.ok || !donnees.success)
				{
					if (errorDiv)
					{
						errorDiv.textContent = donnees.message || 'Erreur lors de la création du groupe.';
						errorDiv.classList.remove('d-none');
					}
					definirEtatSoumission(false);
					return;
				}

				window.location.reload();

				const modalEl = document.getElementById('createGroupModal');
				if (modalEl && typeof bootstrap !== 'undefined')
				{
					const instance = bootstrap.Modal.getInstance(modalEl);
					if (instance) { instance.hide(); }
				}
			}
			catch (erreur)
			{
				console.error('Erreur lors de la création du groupe :', erreur);
				if (errorDiv)
				{
					errorDiv.textContent = 'Erreur inattendue lors de la création du groupe.';
					errorDiv.classList.remove('d-none');
				}
				definirEtatSoumission(false);
			}
		});
	}
}

function initSuppressionGroupe()
{
	const btnSupr = document.getElementById('btnSupr');
	if (!btnSupr) { return; }

	btnSupr.addEventListener('click', async () =>
	{
		const groupesId = getSelectedCodes();

		if (groupesId.length === 0) { alert('Aucun élément sélectionné.'); return; }

		if (!confirm('Voulez-vous vraiment supprimer les éléments sélectionnés ?')) { return; }

		try
		{
			await fetch('./supprimerGroupe.php',
			{
				method : 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ groupesId })
			});

			window.location.reload();
		}
		catch (error) { console.error('Erreur lors de la suppression :', error); }
	});
}

function initEditionGroupe()
{
	const btnSave    = document.getElementById('btnEditGroupSave');
	const btnCancel  = document.getElementById('btnEditGroupCancel');
	const nameInput  = document.getElementById('editGroupNom');
	const colorInput = document.getElementById('editGroupCouleur');
	const noteInput  = document.getElementById('editGroupNote');
	const errorDiv   = document.getElementById('editGroupError');

	// Si les éléments d'édition ne sont pas présents, on ne fait rien
	if (!btnSave || !btnCancel || !nameInput || !colorInput || !noteInput) { return; }

	if (btnCancel)
	{
		btnCancel.addEventListener('click', () => { window.location.href = 'groupes.php'; });
	}

	if (btnSave)
	{
		btnSave.addEventListener('click', async () =>
		{
			if (errorDiv)
			{
				errorDiv.classList.add('d-none');
				errorDiv.textContent = '';
			}

			const nom     = nameInput  ? nameInput.value.trim() : '';
			const couleur = colorInput && colorInput.value ? colorInput.value : '#FF8800';
			const noteStr = noteInput  ? noteInput.value : '';
			const note    = noteStr !== '' ? parseFloat(noteStr) : null;
			const codes   = getSelectedCodes();
			const filters = (typeof filtresCourant !== 'undefined' && Object.keys(filtresCourant).length)
				? filtresCourant
				: (typeof serialiserFiltres === 'function' ? serialiserFiltres() : {});
			const hiddenIdEl = document.getElementById('editGroupId');
			const groupeId   = hiddenIdEl && hiddenIdEl.value ? Number(hiddenIdEl.value) : (typeof window.editGroupId !== 'undefined' ? Number(window.editGroupId) : 0);

			if (!nom)
			{
				if (errorDiv)
				{
					errorDiv.textContent = 'Le nom du groupe est obligatoire.';
					errorDiv.classList.remove('d-none');
				}
				return;
			}

			if (note !== null && (note < 0 || note > 20))
			{
				if (errorDiv)
				{
					errorDiv.textContent = 'La note doit être comprise entre 0 et 20.';
					errorDiv.classList.remove('d-none');
				}
				return;
			}

			try
			{
				const response = await fetch('./modifierGroupe.php',
				{
					method : 'POST',
					headers: { 'Content-Type': 'application/json' },
					body   : JSON.stringify({
						groupe_id   : groupeId,
						nom         : nom,
						couleur     : couleur,
						note_dossier: note,
						codes       : codes,
						filters     : filters
					})
				});

				const data = await response.json();
				if (!response.ok || !data.success)
				{
					if (errorDiv)
					{
						errorDiv.textContent = data.message || 'Erreur lors de la mise à jour du groupe.';
						errorDiv.classList.remove('d-none');
					}
					return;
				}

				window.location.href = 'groupes.php';
			}
			catch (e)
			{
				console.error('Erreur lors de la mise à jour du groupe :', e);
				if (errorDiv)
				{
					errorDiv.textContent = 'Erreur inattendue lors de la mise à jour du groupe.';
					errorDiv.classList.remove('d-none');
				}
			}
		});
	}
}

document.addEventListener('DOMContentLoaded', () =>
{
	if (typeof isDossiersPage !== 'undefined' && isDossiersPage)
	{
		initCreationGroupe();
		initEditionGroupe();
	}
	if (typeof isGroupePage   !== 'undefined' && isGroupePage  ) { initSuppressionGroupe(); }
});

