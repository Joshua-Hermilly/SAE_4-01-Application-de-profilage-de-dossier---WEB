document.addEventListener("DOMContentLoaded", function ()
{
	const collapseBtn = document.getElementById("map_collapse_btn");
	const expandBtn   = document.getElementById("map_expand_btn"  );
	const sidebar     = document.getElementById("map_sidebar"     );
	const closeBar    = document.getElementById("map_close_bar"   );

	function initializeMap()
	{
		if (sidebar ) { sidebar .classList.add   ("d-none"); }
		if (closeBar) { closeBar.classList.remove("d-none"); }
	}

	initializeMap();

	if (collapseBtn)
	{
		collapseBtn.addEventListener("click", function (e)
		{
			e.preventDefault();
			if (sidebar ) sidebar .classList.add   ("d-none");
			if (closeBar) closeBar.classList.remove("d-none");
		});
	}

	if (expandBtn)
	{
		expandBtn.addEventListener("click", function (e)
		{
			e.preventDefault();
			if (sidebar ) sidebar .classList.remove("d-none");
			if (closeBar) closeBar.classList.add   ("d-none");

			setTimeout(function ()
			{
				if (typeof map !== "undefined" && map !== null) { map.invalidateSize(); }
			}, 350);
		});
	}
});