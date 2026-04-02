const btnDossier    = document.getElementById("btnDossier"   );
const btnGroupes    = document.getElementById("btnGroupes"   );
const btnFormations = document.getElementById("btnFormations");

btnDossier.addEventListener   ("click", () => changerPage("index.php"     ) );
btnGroupes.addEventListener   ("click", () => changerPage("groupes.php"   ) );
btnFormations.addEventListener("click", () => changerPage("formations.php") );


function changerPage(page)
{
	this.window.location.href = page;
}