/*------------------------*/
/* CONSTANTES             */
/*------------------------*/
// Tableau
const tableau  = document.getElementById( "tableau"         );
const dvErreur = document.getElementById( "erreur"          );
const page     = document.getElementById( "page"            );
const trHeader = document.getElementById( "trHead"          );
const tBody    = document.getElementById( "tBody"           );

// Bouton
const btnPrc   = document.getElementById( "btnPrc"          );
const btnSvt   = document.getElementById( "btnSvt"          );
const pageAct  = document.getElementById( "pageAct"          );
const btnDeb   = document.getElementById( "btnDeb"          );
const btnFin   = document.getElementById( "btnFin"          );

// Infos page
const infos    = document.getElementById( "pagination-info" );


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
		const input    = document.createElement( 'input' );
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
			else if ( valeur === "NaN" || valeur === "Non définie" )
			{
				th = `<th style="color: gray">${valeur}</th>`;

			}
			else
			{
				th = `<th>${valeur}</th>`;
			}

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
	btnSvt.disabled     =  btnPrc.disabled = btnFin.disabled = btnDeb.disabled = false;
	btnSvt.style.color = "#FFFFFFFF";
	btnPrc.style.color = "#FFFFFFFF";

	if ( maxPage <= actPage+1  ) {btnSvt.disabled = true; btnSvt.style.color =  "#3f3f3f;"; }
	if ( actPage ==         1  ) {btnPrc.disabled = true; btnPrc.style.color =  "#3d3b3b;"; }
}

function afficherErreur( erreur )
{
	dvErreur.textContent   = erreur;
	dvErreur.style.display = "block";
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
					lstCb[cpt].checked = true;
				}
			}
			else
			{
				sessionStorage.removeItem( event.target.id );
				for ( let cpt = 0; cpt < lstCb.length; cpt++ )
				{
					lstCb[cpt].checked = false;
				}
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
async function getDossierCandidat( indexPage )
{
	try
	{
		const response = await fetch('./dossierGet.php', {
			method : 'POST',
			headers:
				{
					'Token'       : 'SAE-4.01_WEB_TOKEN',
					'Content-Type': 'application/json'
				},
			body: JSON.stringify({ page:indexPage })
		});

		const donnees = await response.json();

		if (!response.ok) { throw new Error(`Erreur ${response.status}: ${donnees}`); }

		if ( donnees['erreur'] )
		{
			afficherErreur( donnees['erreur'] );
			return;
		}

		if ( donnees['dossiers'] !== null )
		{
			tableau.style.display = "";
		}
		else
		{
			document.getElementById( "vide"    ).style.display = "block";
		}
		creerHeader ( donnees['headers' ], donnees['isAdmin' ]                     );
		creerTableau( donnees['headers' ], donnees['dossiers'], donnees['isAdmin'] );
		creerBtnPage( donnees['maxPage' ], donnees['actPage' ]                     );

	} catch (error) { console.error('Erreur :', error); }
}


async function getGroupes( indexPage )
{
	try
	{
		const response = await fetch('./GroupeGet.php', {
			method : 'POST',
			headers:
				{
					'Token'       : "SAE-4.01_WEB_TOKEN",
					'Content-Type': 'application/json'
				},
			body: JSON.stringify({ page:indexPage })
		});

		const donnees = await response.json();

		if (!response.ok) { throw new Error(`Erreur ${response.status}: ${donnees}`); }

		if ( donnees['erreur'] )
		{
			afficherErreur( donnees['erreur'] );
			return;
		}

		if ( donnees['groupes'] !== null )
		{
			tableau.style.display = "";
		}
		else
		{
			document.getElementById( "vide"    ).style.display = "block";
		}
		creerHeader ( donnees['headers' ], donnees['isAdmin' ]                     );
		creerTableau( donnees['headers' ], donnees['groupes' ], donnees['isAdmin'] );
		creerBtnPage( donnees['maxPage' ], donnees['actPage' ]                     );

	} catch (error) { console.error('Erreur :', error); }
}



const isGroupePage = window.location.pathname.toLowerCase().includes("groupe");

if (isGroupePage)
{
	getGroupes(1);
	btnPrc.addEventListener( "click", ()    => getGroupes(+pageAct.value - 1) );
	btnSvt.addEventListener( "click", ()    => getGroupes(+pageAct.value + 1) );
	btnDeb.addEventListener( "click", ()    => getGroupes(   1) );
	btnFin.addEventListener( "click", ()    => getGroupes(   btnFin.value) );
}
else
{
	getDossierCandidat(1);
	btnPrc.addEventListener( "click", ()    => getDossierCandidat(+pageAct.value - 1) );
	btnSvt.addEventListener( "click", ()    => getDossierCandidat(+pageAct.value + 1) );
	btnDeb.addEventListener( "click", ()    => getDossierCandidat(   1) );
	btnFin.addEventListener( "click", ()    => getDossierCandidat(   btnFin.value) );
}

/*------------------------*/
/* Event                  */
/*------------------------*/
tableau.addEventListener( "click",(event) => selectionFaite(event) );