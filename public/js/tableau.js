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

// Bouton création de groupe (présent sur la page dossiers)
const btnCreerGroupe = document.getElementById('btnCreerGroupe');

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
		th = `<th scope="col" class="border-end">${headers[cpt]}</th>`;
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
}

function creerTableau( headers, dossiers, isAdmin )
{
	tBody.innerHTML  = "";

	for ( let cptD = 0; cptD < dossiers.length; cptD++ )
	{
		const tr = document.createElement( 'tr' );
		tr.classList.add( 'ligne' );

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
			const input    = document.createElement( 'input' );
			input.classList.add( "form-check-input" );
			input.classList.add( "border-dark"      );
			input.classList.add( "rounded-1"        );
			input.classList.add( "cb"               );
			input.id          =  "cb"+dossiers[cptD][headers[0]];
			input.type        = "checkbox";

			if ( sessionStorage.getItem( input.id         ) === "selectionner"                                         ) { input.checked = true; }
			if ( sessionStorage.getItem( "cbTous"    ) &&  sessionStorage.getItem( input.id ) !== "désélectionner") { input.checked = true; }

			th.appendChild( input );
			tr.appendChild( th    );
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

	//console.log(maxPage  )
	//console.log(actPage+1)

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
				for ( let cpt = 0; cpt < lstCb.length; cpt++ ) { lstCb[cpt].checked = true; }
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
				event.target.checked                                 = false;
			}
		}
		else
		{
			if ( event.target.checked ) { sessionStorage.setItem   ( event.target.id, 'selectionner'); event.target.checked = true ; }
			else                        { sessionStorage.removeItem( event.target.id                ); event.target.checked = false; }
		}
	}
}

function getSelectedCodes()
{
	const codes = [];
	const inputs = document.querySelectorAll('input.cb');
	inputs.forEach((input) =>
	{
		if (input.id === 'cbTous') { return; }
		if (input.checked)
		{
			const code = input.id.replace(/^cb/, '');
			if (code) { codes.push(code); }
		}
	});
	return codes;
}

/*------------------------*/
/* Fetch                  */
/*------------------------*/
async function getData( indexPage, lien, filters = filtresCourant )
{
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
		//console.log(donnees)
		if (!response.ok) { throw new Error(`Erreur ${response.status}: ${donnees}`); }

		if ( donnees['erreur'] )
		{
			afficherErreur( donnees['erreur'] );
			return;
		}

		dvErreur.style.display = "none";
		tableau .style.display = "";
		creerHeader ( donnees['headers'], donnees['isAdmin']                     );
		creerTableau( donnees['headers'], donnees['data'   ], donnees['isAdmin'] );
		creerBtnPage( donnees['maxPage'], donnees['actPage']                     );

	} catch (error) { console.error('Erreur :', error); }
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

function initialiserTableau() { chargerPage(1); }
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
tableau.addEventListener( "click", (event) => selectionFaite (event) );

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

if (btnCreerGroupe && isDossiersPage)
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
if (createGroupForm && isDossiersPage)
{
	createGroupForm.addEventListener('submit', async (event) =>
	{
		event.preventDefault();

		const errorDiv   = document.getElementById('createGroupError');
		if (errorDiv)
		{
			errorDiv.classList.add('d-none');
			errorDiv.textContent = '';
		}

		const nomInput    = document.getElementById('createGroupNom');
		const couleurInput= document.getElementById('createGroupCouleur');
		const noteInput   = document.getElementById('createGroupNote');

		const nom      = nomInput ? nomInput.value.trim() : '';
		const couleur  = couleurInput && couleurInput.value ? couleurInput.value : '#FF8800';
		const noteStr  = noteInput ? noteInput.value : '';
		const note     = noteStr !== '' ? parseFloat(noteStr) : null;
		const codes    = getSelectedCodes();
		const filters  = Object.keys(filtresCourant).length ? filtresCourant : serialiserFiltres();

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
				headers:
				{
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(
				{
					nom,
					couleur,
					note_dossier: note,
					codes,
					filters
				})
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
