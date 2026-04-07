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
	// Bouton création de groupe (présent sur la page dossiers)
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

	const createGroupForm = document.getElementById('createGroupForm');
	if (createGroupForm)
	{
		createGroupForm.addEventListener('submit', async (event) =>
		{
			event.preventDefault();

			const errorDiv = document.getElementById('createGroupError');
			if (errorDiv)
			{
				errorDiv.classList.add('d-none');
				errorDiv.textContent = '';
			}

			const nomInput     = document.getElementById('createGroupNom');
			const couleurInput = document.getElementById('createGroupCouleur');
			const noteInput    = document.getElementById('createGroupNote');

			const nom     = nomInput ? nomInput.value.trim() : '';
			const couleur = couleurInput && couleurInput.value ? couleurInput.value : '#FF8800';
			const noteStr = noteInput ? noteInput.value : '';
			const note    = noteStr !== '' ? parseFloat(noteStr) : null;
			const codes   = getSelectedCodes();
			const filters = (typeof filtresCourant !== 'undefined' && Object.keys(filtresCourant).length)
				? filtresCourant
				: (typeof serialiserFiltres === 'function' ? serialiserFiltres() : {});

			if (!nom)
			{
				if (errorDiv)
				{
					errorDiv.textContent = 'Le nom du groupe est obligatoire.';
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

			try
			{
				const response = await fetch('./creerGroupe.php',
				{
					method : 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({ nom, couleur, note_dossier: note, codes, filters })
				});

				const data = await response.json();
				if (!response.ok || !data.success)
				{
					if (errorDiv)
					{
						errorDiv.textContent = data.message || 'Erreur lors de la création du groupe.';
						errorDiv.classList.remove('d-none');
					}
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
			catch (e)
			{
				console.error('Erreur lors de la création du groupe :', e);
				if (errorDiv)
				{
					errorDiv.textContent = 'Erreur inattendue lors de la création du groupe.';
					errorDiv.classList.remove('d-none');
				}
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

document.addEventListener('DOMContentLoaded', () =>
{
	if (typeof isDossiersPage !== 'undefined' && isDossiersPage) { initCreationGroupe   (); }
	if (typeof isGroupePage   !== 'undefined' && isGroupePage  ) { initSuppressionGroupe(); }
});

