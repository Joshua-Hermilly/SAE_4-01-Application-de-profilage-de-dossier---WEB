/*------------------------*/
/* CONSTANTES             */
/*------------------------*/
const divMap = document.getElementById("map"   );
const scDist = document.getElementById("scDist");
const txtDst = document.getElementById("txtDst");
const btnDst = document.getElementById("btnDst");

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
/* INITIALISER LA CARTE   */
/*------------------------*/
function initMap()
{
    if (map !== null) {	return; }

    // MAP
    map = L.map('map').setView([latHavre, lonHavre], vue);
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
        maxZoom: 19
    }).addTo(map);
}

function initSide( max )
{
    scDist.min = 1;
    scDist.max = max;
}

/*------------------------*/
/* CHARGER LES DONNÉES    */
/*------------------------*/
async function getCarte(distance)
{
    try
    {
        const response = await fetch('./carte.php?distance=' + distance,
        {
            method: 'GET',
            headers:
            {
                'Token'       : 'SAE-4.01_WEB_TOKEN',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {  throw new Error(`Erreur HTTP ${response.status}`);  }

        const donnees = await response.json();
        if (donnees.erreur) { console.error('Erreur API:', donnees.erreur); return; }

        console.log(donnees);

        initSide(donnees.max_distance);

        if (makCluster)
        {
            makCluster.clearLayers();
            map.removeLayer(makCluster);
        }
        makCluster = L.markerClusterGroup().addTo(map);

        if (Array.isArray(donnees.etablissements) && donnees.etablissements.length > 0)
        {
            donnees.etablissements.forEach(etablissement =>
            {
                ajouterMarqueur(etablissement);
            });
        }

    } catch (error) {  console.error('Erreur api -- getCarte:', error); }
}

/*------------------------*/
/* AJOUTER MARQUEUR       */
/*------------------------*/
function ajouterMarqueur(etablissement)
{
    const lat = etablissement.etablissement_latitude;
    const lon = etablissement.etablissement_longitude;

    // Coordonnées ?
    if (lat === null || lon === null || (lat == 0 && lon == 0))
    {
        //console.warn(`Pas de coordonnées pour: ${etablissement.etablissement_nom}`);
        return;
    }

    const marker      = L.marker([lat, lon]);
    const nomEtab     = etablissement.etablissement_nom        ;
    const codePostal  = etablissement.etablissement_code_postal;
    const commune     = etablissement.etablissement_commune    ;
    const nb_candidat = etablissement.nb_candidats             ;

    makCluster.addLayer(marker);
    marker.bindPopup
    (`
        <p><strong>${nomEtab}</strong></p>
        <p>${codePostal} ${commune}</p>
        <p><strong>Nombre d'étudiants : </strong>${nb_candidat}</p>
    `);
}

/*------------------------*/
/* ÉVÉNEMENTS             */
/*------------------------*/
initMap ();
getCarte(-1);


btnDst.addEventListener('click', () =>
{
    sessionStorage.setItem('distance', scDist.value);
    getCarte(scDist.value);
})

scDist.addEventListener('input', () =>
{
   txtDst.textContent = scDist.value;
});