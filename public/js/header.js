const btnDossier     = document.getElementById("btnDossier"     );
const btnGroupes     = document.getElementById("btnGroupes"     );
const btnFormations  = document.getElementById("btnFormations"  );
const btnCreerCompte = document.getElementById("btn-creerCompte");
const btnDeconnexion = document.getElementById("btn-deconnexion");

btnDossier    .addEventListener("click", () => changerPage("index.php"     ) );
btnGroupes    .addEventListener("click", () => changerPage("groupes.php"   ) );
btnFormations .addEventListener("click", () => changerPage("formations.php") );
btnDeconnexion.addEventListener("click", () => changerPage("deconnexion.php") );

if (btnCreerCompte) btnCreerCompte.addEventListener("click", () => changerPage("creerCompte.php") );


function changerPage(page)
{
	window.location.href = page;
}