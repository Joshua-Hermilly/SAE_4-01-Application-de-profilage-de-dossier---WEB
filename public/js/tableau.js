/*------------------------*/
/* CONSTANTES             */
/*------------------------*/
// Tableau
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
function creerHeader( headers, dossiers )
{
	trHeader.innerHTML = "";

	for ( let cpt = 0; cpt < headers.length-1; cpt++ )
	{
		th = `<th scope="col" class="border-end">${headers[cpt]}</th>`;

		trHeader.innerHTML+= th;
	}

	creerTableau( headers, dossiers );
}

function creerTableau( headers, dossiers )
{
	tBody.innerHTML = "";
	for ( let cptD = 0; cptD < dossiers.length; cptD++ )
	{
		const tr = document.createElement( 'tr' );
		tr.classList.add( 'ligne' );

		for ( let cptH = 0; cptH < headers.length-1; cptH++ )
		{
			const valeur = dossiers[cptD][ headers[cptH] ];
			let th;

			if ( cptH === 0 )
			{
				th = `<th>
					      <span class="badge border border-dark text-dark rounded-2 p-2">${valeur}</span>
				      </th>`;
			}
			else
			{
				th = `<th>${valeur}</th>`;
			}

			tr.innerHTML += th;
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
	btnSvt.disabled     = false;
	btnPrc.disabled     = false;
	btnFin.disabled     = false;
	btnDeb.disabled     = false;

	if ( maxPage === actPage ) {btnSvt.disabled = true;}
	if ( actPage ===       1 ) {btnPrc.disabled = true;}
}

function afficherErreur( erreur )
{
	dvErreur.textContent   = erreur;
	dvErreur.style.display = "block";
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
		console.log(donnees)

		if (!response.ok) { throw new Error(`Erreur ${response.status}: ${donnees}`); }

		if ( donnees['erreur'] )
		{
			afficherErreur( donnees['erreur'] );
			return;
		}

		if ( donnees['dossiers'] !== null )
		{
			document.getElementById("tableau").style.display = "";
		}
		else
		{
			document.getElementById( "vide"    ).style.display = "block";
		}
		creerHeader ( donnees['headers' ], donnees['dossiers'] );
		creerBtnPage( donnees['maxPage' ], donnees['actPage' ] );

	} catch (error) { console.error('Erreur :', error); }
}
getDossierCandidat(1);


/*------------------------*/
/* Event                  */
/*------------------------*/
// tBody.addEventListener ( "click", () => getDossierCandidat(             1) );
btnPrc.addEventListener( "click", () => getDossierCandidat(+pageAct.value - 1) );
btnSvt.addEventListener( "click", () => getDossierCandidat(+pageAct.value + 1) );
btnDeb.addEventListener( "click", () => getDossierCandidat(   1) );
btnFin.addEventListener( "click", () => getDossierCandidat(   btnFin.value) );
