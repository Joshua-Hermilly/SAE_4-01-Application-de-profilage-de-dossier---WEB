/*------------------------*/
/* CONSTANTES             */
/*------------------------*/
const btnMap = document.getElementById( "btnMap"   );
const divMap = document.getElementById( "map"      );
const divDst = document.getElementById( "distance" );

let localisations;

/*------------------------*/
/* Fonctions              */
/*------------------------*/

/*------------------------*/
/* Fetch                  */
/*------------------------*/
async function setLocalisation()
{
	try
	{
		const response = await fetch('./carte.php', {
			method : 'POST',
			headers:
			{
				'Token'       : 'SAE-4.01_WEB_TOKEN',
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({ localisations:localisations })
		});

		const donnees = await response.json();
		console.log(donnees       )

		if (!response.ok) { throw new Error(`Erreur ${response.status}: ${donnees}`); }

		if ( donnees['erreur'] )
		{
			afficherErreur( donnees['erreur'] );
			return;
		}

		await getCarte();

	} catch (error) { console.error('Erreur :', error); }
}

async function getCarte()
{
	try
	{
		const response = await fetch('./carte.php', {
			method : 'GET',
			headers:
			{
				'Token'       : 'SAE-4.01_WEB_TOKEN',
				'Content-Type': 'application/json'
			}
		});

		const donnees = await response.json();
		console.log(donnees       )

		if (!response.ok) { throw new Error(`Erreur ${response.status}: ${donnees}`); }

		if ( donnees['erreur'] )
		{
			afficherErreur( donnees['erreur'] );
			return;
		}

		//majCarte();

	} catch (error) { console.error('Erreur :', error); }
}

async function trouverPos()
{
	try
	{

	} catch (error) { console.error('Erreur :', error); }
}

/*------------------------*/
/* Event                  */
/*------------------------*/
window.addEventListener( 'load', async () =>
{
	//await getMaxDst ();
	await getCarte  ();
	//await trouverPos();
});