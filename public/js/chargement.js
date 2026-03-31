const GIF_CHARGEMENT = `<img src="/image/chargement.gif" width="200" height="200" />`;

const bloc = document.getElementById( "bloc"          );
const div  = document.getElementById( "chargement"    );
const btn  = document.getElementById( "btnChargement" );

btn.addEventListener('click', function (event)
{
	const annee     = document.getElementById( 'annee_promotion' ).value;
	const fichier   = document.getElementById( 'file'            ).value;
	const tabFile   = fichier.split('.');
	const extention = tabFile[tabFile.length - 1];

	if (annee    && /^\d$/.test(annee)                         &&
		fichier  && ['xlsx', 'xls', 'csv'].includes(extention)    )
	{
		bloc.style.display = "none";
		div.innerHTML      = GIF_CHARGEMENT;
	}
});
