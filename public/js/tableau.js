/*------------------------*/
/* CONSTANTES             */
/*------------------------*/
// Tableau
const tableau    = document.getElementById( "tableau"         );
const dvErreur   = document.getElementById( "erreur"          );
const page       = document.getElementById( "page"            );
const trHeader   = document.getElementById( "trHead"          );
const tBody      = document.getElementById( "tBody"           );
const filterForm = document.getElementById( 'filterForm'      );

// Bouton
const btnPrc   = document.getElementById( "btnPrc"          );
const btnSvt   = document.getElementById( "btnSvt"          );
const pageAct  = document.getElementById( "pageAct"         );
const btnDeb   = document.getElementById( "btnDeb"          );
const btnFin   = document.getElementById( "btnFin"          );

// Infos page
const infos    = document.getElementById( "pagination-info" );

// Détection du type de page
const currentPath     = window.location.pathname.toLowerCase();
const isGroupePage    = currentPath.includes("groupes.php"   );
const isFormationPage = currentPath.includes("formations.php");
const isDossiersPage  = currentPath.includes("dossiers.php"  );

let filtresCourant = {};


/*------------------------*/
/* Fonctions              */
/*------------------------*/
function creerHeader( headers, isAdmin )
{
	trHeader.innerHTML = "";

	for ( let cpt = 0; cpt < headers.length-1; cpt++ )
	{
		th = `<th scope="col" >${headers[cpt]}</th>`;
		trHeader.innerHTML+= th;
	}

	if (isAdmin)
	{
		const th    = document.createElement( 'th'    );
		const input = document.createElement( 'input' );

		input.classList.add( "form-check-input" );
		input.classList.add( "border-dark"      );
		input.classList.add( "rounded-1"        );
		input.classList.add( "cb"               );
		input.id          = "cbTous";
		input.type        = "checkbox";
		th   .textContent = "Tout sélectionner";

		if ( sessionStorage.getItem( input.id ) === "selectionner") { input.checked = true; }

		th      .appendChild( input );
		trHeader.appendChild( th    );
	}

	if (isGroupePage)
	{
		const th = document.createElement('th');
		th.textContent = "Détail";
		trHeader.appendChild(th);
	}
}

function creerTableau( headers, dossiers, isAdmin )
{
	tBody.innerHTML  = "";

	for ( let cptD = 0; cptD < dossiers.length; cptD++ )
	{
		const tr = document.createElement( 'tr' );
		tr.classList.add( 'ligne' );

		// Informations de groupe pour la ligne courante
		const couleurBrute      = (dossiers[cptD]['Couleur'] || dossiers[cptD]['groupe_couleur'] || '').toLowerCase();
		const ligneGroupeId     = dossiers[cptD]['groupe_id'] ?? null;
		const isEditGroupMode   = !!window.editGroupMode;
		const currentEditGroup  = typeof window.editGroupId !== 'undefined' ? window.editGroupId : null;
		const hasAnyGroupColor  = couleurBrute !== '' && couleurBrute !== '#dedede';
		const dansGroupeCourant = isDossiersPage && ligneGroupeId !== null && String(ligneGroupeId) === String(currentEditGroup);

		for ( let cptH = 0; cptH < headers.length-1; cptH++ )
		{
			let valeur = dossiers[cptD][ headers[cptH] ];
			if ( valeur ==    -1 ) { valeur = "NaN"         ;}
			if ( valeur === null ) { valeur = "Non définie" ;}

			let th;

			if ( cptH === 0 )
			{
				let color = dossiers[cptD]['Couleur'] || dossiers[cptD]['groupe_couleur'];
				th = `<th>
					      <span class="badge border border-dark text-dark rounded-2 p-2" style="background-color: ${color}" >${valeur}</span>
				     </th>`;
			}
			else if ( valeur === "NaN" || valeur === "Non définie" ) { th = `<th style="color: gray">${valeur}</th>`; }
			else                                                     { th = `<th>${valeur}</th>`;                     }

			tr.innerHTML += th;
		}

		if (isAdmin)
		{
			const th    = document.createElement( 'th'    );
			const input = document.createElement( 'input' );
			input.classList.add( "form-check-input" );
			input.classList.add( "border-dark"      );
			input.classList.add( "rounded-1"        );
			input.classList.add( "cb"               );
			input.id   =  "cb"+dossiers[cptD][headers[0]];
			input.type = "checkbox";

			if (isEditGroupMode) { input.checked = dansGroupeCourant; }
			else
			{
				if ( sessionStorage.getItem( input.id ) === "selectionner"                                         ) { input.checked = true; }
				if ( sessionStorage.getItem( "cbTous" ) &&  sessionStorage.getItem( input.id ) !== "désélectionner") { input.checked = true; }
			}

			th.appendChild( input );
			tr.appendChild( th    );
		}

		if (isGroupePage)
		{
			const thEye = document.createElement('th');
			const container = document.createElement('div');
			container.className = 'd-flex gap-1 justify-content-center';

			const idGroupe   = dossiers[cptD]['Id groupe'];
			const nomGroupe  = dossiers[cptD]['Nom groupe']  || '';
			const couleurGrp = dossiers[cptD]['Couleur']      || '';
			const noteGroupe = (dossiers[cptD]['Note Dossier'] ?? dossiers[cptD]['Note dossier']) ?? '';

			const iconVoir = document.createElement('i');
			iconVoir.className = "bi bi-eye-fill border border-dark rounded-1 p-1 text-center";
			iconVoir.style.cursor = 'pointer';
			iconVoir.dataset.action       = 'voir-groupe';
			iconVoir.dataset.groupeId     = idGroupe;
			iconVoir.dataset.groupeNom    = nomGroupe;
			iconVoir.dataset.groupeCouleur= couleurGrp;
			iconVoir.dataset.groupeNote   = noteGroupe;

			container.appendChild(iconVoir);
			
			if (isAdmin)
			{
				const iconEdit = document.createElement('i');
				iconEdit.className = "bi bi-pencil-square border border-dark rounded-1 p-1 text-center";
				iconEdit.style.cursor = 'pointer';
				iconEdit.dataset.action        = 'modifier-groupe';
				iconEdit.dataset.groupeId      = idGroupe;
				iconEdit.dataset.groupeNom     = nomGroupe;
				iconEdit.dataset.groupeCouleur = couleurGrp;
				iconEdit.dataset.groupeNote    = noteGroupe;
				container.appendChild(iconEdit);
			}
			thEye.appendChild(container);
			tr.appendChild(thEye);
		}

		tBody.appendChild( tr );
	}
}

function creerBtnPage( maxPage, actPage )
{
	infos.textContent = `Affichage de la page ${actPage} sur ${maxPage}`;

	pageAct.textContent = actPage;
	pageAct.value       = actPage;
	btnFin.value        = maxPage;
	btnSvt.disabled     = btnPrc.disabled = btnFin.disabled = btnDeb.disabled = false;
	btnSvt.style.color  = "#FFFFFFFF";
	btnPrc.style.color  = "#FFFFFFFF";

	if ( actPage >= maxPage    ) { btnSvt.disabled = true; btnSvt.style.color =  "#3f3f3f;"; }
	if ( actPage ==         1  ) { btnPrc.disabled = true; btnPrc.style.color =  "#3d3b3b;"; }
}

function afficherErreur( erreur )
{
	tableau.style.display  = "none";

	dvErreur.textContent   = erreur;
	dvErreur.style.display = "block";
}

function serialiserFiltres()
{
	if (!filterForm) { return {}; }

	const formData = new FormData(filterForm);
	const filters = {};

	for (const [key, value] of formData.entries())
	{
		if (key === 'page') { continue; }

		const normalizedKey = key.endsWith('[]') ? key.slice(0, -2) : key;
		if (filters[normalizedKey] === undefined      ) { filters[normalizedKey] = value;                           }
		else if (Array.isArray(filters[normalizedKey])) { filters[normalizedKey].push(value);                       }
		else                                            { filters[normalizedKey] = [filters[normalizedKey], value]; }
	}

	return filters;
}

function selectionFaite(event)
{
	if ( event.target.tagName === 'INPUT')
	{
		if ( event.target.id === "cbTous" )
		{
			const lstCb = document.getElementsByClassName( "cb" );
			sessionStorage.clear();

			if ( event.target.checked )
			{
				sessionStorage.setItem( "cbTous" , 'selectionner' );
				for ( let cpt = 0; cpt < lstCb.length; cpt++ )
				{
					if (lstCb[cpt].disabled) { continue; }
					lstCb[cpt].checked = true;
				}
			}
			else
			{
				sessionStorage.removeItem( event.target.id );
				for ( let cpt = 0; cpt < lstCb.length; cpt++ ) { lstCb[cpt].checked = false; }
			}
		}
		else if ( sessionStorage.getItem( "cbTous" ) === 'selectionner' )
		{
			if ( event.target.checked )
			{
				sessionStorage.setItem( event.target.id, 'selectionnner' );
				event.target.checked = true;
			}
			else
			{
				sessionStorage.setItem( event.target.id, 'désélectionner' );
				sessionStorage.setItem( "cbTous"       , 'désélectionner' );

				document.getElementById( "cbTous" ).checked = false;
				event.target.checked                        = false;
			}
		}
		else
		{
			if ( event.target.checked ) { sessionStorage.setItem   ( event.target.id, 'selectionner'); event.target.checked = true ; }
			else                        { sessionStorage.removeItem( event.target.id                ); event.target.checked = false; }
		}
	}
}

/*------------------------*/
/* Fetch                  */
/*------------------------*/
async function getData( indexPage, lien, filters = filtresCourant )
{
	const distance = sessionStorage.getItem('distance');
	if (distance) { filters.distance = distance; }

	try
	{
		const response = await fetch(lien,
		{
			method : 'POST',
			headers:
			{
				'Token'       : 'SAE-4.01_WEB_TOKEN',
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({ page:indexPage, ...filters })
		});

		const donnees = await response.json();
		if (!response.ok) { throw new Error(`Erreur ${response.status}: ${donnees}`); }

		if ( donnees['erreur'] ) { afficherErreur( donnees['erreur'] ); return; }

		dvErreur.style.display = "none";
		tableau .style.display = "";
		creerHeader ( donnees['headers'], donnees['isAdmin']                     );
		creerTableau( donnees['headers'], donnees['data'   ], donnees['isAdmin'] );
		creerBtnPage( donnees['maxPage'], donnees['actPage']                     );

		if (isDossiersPage && donnees.nbEtu !== undefined)
		{
			const infoRightNbEtu = document.getElementById('nbEtu');
			if (infoRightNbEtu)
			{
				infoRightNbEtu.textContent = donnees.nbEtu;
			}
		}
		if (isDossiersPage && donnees.annees !== undefined)
		{
			const infoDates = document.getElementById('AnneesSelect');
			if (infoDates)
			{
				infoDates.textContent = donnees.annees.length > 0 ? donnees.annees.join("-") : 'Aucune';
			}
		}

	}
	catch (error)
	{
		console.error('Erreur :', error);
		afficherErreur('Erreur lors du chargement des données (formations).');
	}
}

function getLienCourant()
{
	if      (isGroupePage   ) { return './GroupeGet.php'   ; }
	else if (isFormationPage) { return './FormationGet.php'; }
	else                      { return './dossierGet.php'  ; }
}

function chargerPage(indexPage, filters = filtresCourant)
{
	const lien = getLienCourant();
	return getData(indexPage, lien, filters);
}

function initialiserTableau()
{
	const preset = (typeof window.defaultFilters !== 'undefined' && window.defaultFilters) ? window.defaultFilters : null;
	if (preset && Object.keys(preset).length > 0)
	{
		filtresCourant = { ...filtresCourant, ...preset };

		if (filterForm)
		{
			for (const [key, value] of Object.entries(preset))
			{
				if (Array.isArray(value))
				{
					const inputs = filterForm.querySelectorAll(`[name="${key}[]"]`);
					if (inputs.length > 0)
					{
						inputs.forEach((input) => { input.checked = value.includes(input.value); });
						continue;
					}
				}
				else
				{
					const field = filterForm.querySelector(`[name="${key}"]`);
					if (field) { field.value = value; continue; }
				}
			}
		}

		chargerPage(1, filtresCourant);
		return;
	}

	if (isDossiersPage)
	{
		const params    = new URLSearchParams(window.location.search);
		const nomGroupe = params.get('nom_groupe') || params.get('groupe');
		if (nomGroupe)
		{
			filtresCourant = { ...filtresCourant, nom_groupe: nomGroupe };
			if (filterForm)
			{
				const select = filterForm.querySelector('[name="nom_groupe"]');
				if (select) { select.value = nomGroupe; }
			}
			chargerPage(1, filtresCourant);
			return;
		}
	}

	chargerPage(1);
}

document.addEventListener('DOMContentLoaded', initialiserTableau);

function attacherPagination()
{
	if (!btnPrc || !btnSvt || !btnDeb || !btnFin || !pageAct) { return; }

	btnPrc.addEventListener( "click", () => chargerPage(+pageAct.value - 1) );
	btnSvt.addEventListener( "click", () => chargerPage(+pageAct.value + 1) );
	btnDeb.addEventListener( "click", () => chargerPage(1                 ) );
	btnFin.addEventListener( "click", () => chargerPage(+btnFin.value     ) );
}
attacherPagination();

/*------------------------*/
/* Event                  */
/*------------------------*/
tableau.addEventListener("click", (event) =>
{
	const target = event.target;

	// Gestion des actions liées aux groupes (page groupes)
	if (target instanceof HTMLElement && target.dataset && target.dataset.action)
	{
		const action        = target.dataset.action;
		const groupeId      = target.dataset.groupeId     || null;
		const groupeNom     = target.dataset.groupeNom    || '';
		const groupeCouleur = target.dataset.groupeCouleur || '';
		const groupeNote    = target.dataset.groupeNote   || '';

		if (action === 'voir-groupe' && groupeNom)
		{
			const params = new URLSearchParams({
				mode      : 'view_groupe',
				groupe_id : groupeId,
				groupe    : groupeNom
			});
			window.location.href = 'dossiers.php?' + params.toString();
			return;
		}

		if (action === 'modifier-groupe' && groupeId)
		{
			const params = new URLSearchParams({
				mode      : 'edit_groupe',
				groupe_id : groupeId,
				groupe    : groupeNom
			});
			window.location.href = 'dossiers.php?' + params.toString();
			return;
		}
	}

	selectionFaite(event);
});

if (filterForm)
{
	filterForm.addEventListener('submit', function (event)
	{
		const bouton = event.submitter;
		if (bouton && bouton.dataset && bouton.dataset.action === 'creer-groupe') { return; }

		event.preventDefault();
		filtresCourant = serialiserFiltres();
		chargerPage(1, filtresCourant);
	});
}

document.getElementById( "btnDst" ).addEventListener( "click", () =>
{
	const distance = sessionStorage.getItem('distance');
	event.preventDefault();
	filtresCourant = serialiserFiltres();
	chargerPage(1, filtresCourant);
})