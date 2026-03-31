const btnImport  = document.getElementById("btnImport");
const btnExport  = document.getElementById("btnExport");

btnImport.addEventListener   ("click", () => changerPage("import.php"   ) );
btnExport.addEventListener   ("click", () => changerPage("export.php"   ) );


function changerPage(page)
{
	this.window.location.href = page;
}