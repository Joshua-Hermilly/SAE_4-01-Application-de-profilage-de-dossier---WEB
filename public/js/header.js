const btnDossier    = document.getElementById("btnDossier"   );
const btnGroupes    = document.getElementById("btnGroupes"   );
const btnFormations = document.getElementById("btnFormations");

btnDossier.addEventListener("click", () => changerPage("Dossiers.php") );
btnDossier.addEventListener("click", () => changerPage("Groupes.php" ) );
btnDossier.addEventListener("click", () => changerPage("Dossiers.php") );


function changerPage(page)
{
	this.window.location.href = page;
}


