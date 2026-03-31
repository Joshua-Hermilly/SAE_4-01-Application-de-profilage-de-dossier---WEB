/*------------------------*/
/* CONSTANTES             */
/*------------------------*/
const page     = document.getElementById( "page"   );
const trHeader = document.getElementById( "trHead" );
const tBody    = document.getElementById( "tBody"  );

const thHeader = document.createElement( 'th' );
thHeader.setAttribute ( 'scope', "col" );
thHeader.classList.add( "border-end"                     );

const thBody = document.createElement( 'th' );

const color = document.createElement( 'span' );
color.className = 'badge border border-dark text-dark rounded-2 p-2';


/*------------------------*/
/* Fetch                  */
/*------------------------*/
async function getDossierCandidat()
{
    try
    {
        const response = await fetch('./dossierGet.php', {
            method : 'POST',
            headers: {
                'Token': 'SAE-4.01_WEB_TOKEN',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ page: 1 }),
        });

        // Récupérer la réponse en texte d'abord
        const text = await response.text();
        console.log('Réponse brute du serveur:', text);

        if (!response.ok) {
            throw new Error(`Erreur ${response.status}: ${text}`);
        }

        // Ensuite la parser en JSON
        const data = JSON.parse(text);
        console.log('Données JSON:', data);

    } catch (error) {
        console.error('Erreur :', error);
    }
}

getDossierCandidat();
