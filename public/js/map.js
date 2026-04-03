/*------------------------*/
/* CONSTANTES             */
/*------------------------*/
const divMap = document.getElementById("map");
const latHavre   = 49.51627358707744;
const lonHavre   = 0.1625817429307139
const vue        = 6;

/*------------------------*/
/* VARIABLES              */
/*------------------------*/
let   map            = null;
let   localisations = [];
let   makCluster     = null;

/*------------------------*/
/* INITIALISER LA CARTE    */
/*------------------------*/
function initMap()
{
    if (map !== null) {	return; }

    // MAP
    map = L.map('map').setView([latHavre, lonHavre], vue);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',
	{
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
}

/*------------------------*/
/* CHARGER LES DONNÉES     */
/*------------------------*/
async function getCarte()
{
    try
	{
        const response = await fetch('./carte.php',
		{
            method: 'GET',
            headers:
			{
                'Token'       : 'SAE-4.01_WEB_TOKEN',
                'Content-Type': 'application/json'
            }
        });

        const data = await response;
        console.log(data)
        const donnees = await response.json();
        console.log(donnees);

		//Erreur
        if (!response.ok  ) { throw new Error(`Erreur ${response.status}`  );           }
        if (donnees.erreur) { console  .error('Erreur API:', donnees.erreur);  return;  }


        // Point
            makCluster = L.markerClusterGroup().addTo(map);
        if (Array.isArray(donnees) && donnees.length > 0)
		{
            donnees.forEach(etablissement => { ajouterMarqueur(etablissement); });
        }

    } catch (error) {  console.error('Erreur api -- getCarte:', error); }
}

/*------------------------*/
/* AJOUTER MARQUEUR       */
/*------------------------*/
function ajouterMarqueur(etablissement)
{
    const lat = etablissement.localisation_latitude;
    const lon = etablissement.localisation_longitude;

    // Coordonnées ?
    if (lat === null || lon === null)
	{
        console.warn(`Pas de coordonnées pour: ${etablissement.etablissement_nom}`);
        return;
    }

    // Markeur
    const marker     = L.marker([lat, lon]);
	const nomEtab    = etablissement.etablissement_nom                    ;
    const codePostal = etablissement.localisation_code_postal;
    const commune    = etablissement.localisation_commune    ;

    makCluster.addLayer(marker);
    //marker.bindPopup(`<strong>${nomEtab}</strong><br/>${commune}${codePostal}`);
}

/*------------------------*/
/* ÉVÉNEMENTS              */
/*------------------------*/
document.addEventListener('DOMContentLoaded', async () =>
{
    initMap();
    //await getCarte();
});
