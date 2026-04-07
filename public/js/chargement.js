const GIF_CHARGEMENT = `<img src="/image/chargement.gif" width="200" height="200" />`;

const bloc = document.getElementById( "card-block"    );
const div  = document.getElementById( "chargement"    );
const btn  = document.getElementById( "btn-action"    );

btn.addEventListener('click', function (event)
{
	const annee     = document.getElementById( 'annee_promotion' ).value;
	const fichier   = document.getElementById( 'file'            ).value;
	const tabFile   = fichier.split('.');
	const extension = tabFile[tabFile.length - 1];

	if (annee    && /^\d{4}$/.test(annee)                         &&
		fichier  && ['xlsx', 'xls', 'csv'].includes(extension)    )
	{
		bloc.style.display = "none";
		div.innerHTML      = GIF_CHARGEMENT;
	}
});
