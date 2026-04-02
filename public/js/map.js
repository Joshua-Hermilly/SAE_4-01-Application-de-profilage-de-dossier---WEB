/*------------------------*/
/* VARIABLES GLOBALES      */
/*------------------------*/
let map = null;
let localisations = [];
const TOKEN = 'SAE-4.01_WEB_TOKEN';
const mapContainer = document.getElementById('map');

/*------------------------*/
/* INITIALISER LA CARTE    */
/*------------------------*/
function initMap() {
    // Éviter double initialisation
    if (map !== null) return;

    // Vérifier que le DOM est prêt
    if (!mapContainer) {
        console.error('Conteneur #map introuvable');
        return;
    }

    // Créer la carte (centré sur la France)
    map = L.map('map').setView([46.2276, 2.2137], 6);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    console.log('✓ Carte initialisée');
}

/*------------------------*/
/* CHARGER LES DONNÉES     */
/*------------------------*/
async function getCarte() {
    try {
        const response = await fetch('./carte.php', {
            method: 'GET',
            headers: {
                'Token': TOKEN,
                'Content-Type': 'application/json'
            }
        });

        const donnees = await response.json();
        console.log('Données reçues:', donnees);

        if (!response.ok) {
            throw new Error(`Erreur ${response.status}`);
        }

        if (donnees.erreur) {
            console.error('Erreur API:', donnees.erreur);
            return;
        }

        // Afficher les marqueurs sur la carte
        if (Array.isArray(donnees) && donnees.length > 0) {
            donnees.forEach(etablissement => {
                ajouterMarqueur(etablissement);
            });
            console.log(`✓ ${donnees.length} établissements affichés`);
        } else {
            console.warn('Aucun établissement trouvé');
        }

    } catch (error) {
        console.error('Erreur getCarte:', error);
    }
}

/*------------------------*/
/* AJOUTER MARQUEUR        */
/*------------------------*/
function ajouterMarqueur(etablissement) {
    // Vérifier que la localisation a des coordonnées
    if (!etablissement.localisation) {
        console.warn('Pas de localisation pour:', etablissement);
        return;
    }

    const lat = etablissement.localisation.localisation_latitude;
    const lon = etablissement.localisation.localisation_longitude;

    // Si pas de coordonnées, on skip
    if (lat === null || lon === null) {
        console.warn(`Pas de coordonnées pour: ${etablissement.etablissement_nom}`);
        return;
    }

    // Créer le marqueur
    const marker = L.marker([lat, lon]).addTo(map);

    // Ajouter une popup avec le nom de l'établissement
    const nomEtab = etablissement.etablissement_nom || 'Établissement';
    const codePostal = etablissement.localisation.localisation_code_postal || '';
    const commune = etablissement.localisation.localisation_commune || '';

    marker.bindPopup(`
        <strong>${nomEtab}</strong><br/>
        ${commune} ${codePostal}
    `);
}

/*------------------------*/
/* ÉVÉNEMENTS              */
/*------------------------*/
document.addEventListener('DOMContentLoaded', async () => {
    console.log('DOMContentLoaded - Initialisation de la carte...');
    initMap();
    //await getCarte();
});
