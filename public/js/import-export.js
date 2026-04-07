const btnImport  = document.getElementById("btnImport");
const btnExport  = document.getElementById("btnExport");

if (btnImport) btnImport.addEventListener   ("click", () => changerPage("import.php"   ) );
if (btnExport) btnExport.addEventListener   ("click", () => changerPage("export.php"   ) );


function changerPage(page)
{
	this.window.location.href = page;
}