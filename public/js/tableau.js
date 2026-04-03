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

let filtresCourant = {};

// Type de page courante
const currentPath     = window.location.pathname.toLowerCase();
const isGroupePage    = currentPath.includes("groupes.php"   );
const isFormationPage = currentPath.includes("formations.php");


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

	console.log(maxPage  )
	console.log(actPage+1)

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
		console.log(donnees)
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

function initialiserTableau()
{
	if      (isGroupePage   ) { getData(1, './GroupeGet.php'   ); }
	else if (isFormationPage) { getData(1, './FormationGet.php'); }
	else                      { getData(1, './dossierGet.php'  ); }
}
initialiserTableau();

/*------------------------*/
/* Event                  */
/*------------------------*/
// tBody.addEventListener ( "click", () => getDossierCandidat(             1) );
tableau.addEventListener( "click", (event) => selectionFaite    (event                             ) );

if (filterForm)
{
	filterForm.addEventListener('submit', function (event) {
		const bouton = event.submitter;
		if (bouton && bouton.dataset && bouton.dataset.action === 'creer-groupe') { return; }

		event.preventDefault();
		filtresCourant = serialiserFiltres();

		if      (isGroupePage   ) { getData(1, './GroupeGet.php'   ,  filtresCourant); }
		else if (isFormationPage) { getData(1, './FormationGet.php',  filtresCourant); }
		else                      { getData(1, './dossierGet.php'  ,  filtresCourant); }
	});
}
