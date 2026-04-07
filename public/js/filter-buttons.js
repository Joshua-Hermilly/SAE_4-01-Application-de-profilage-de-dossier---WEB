document.addEventListener("DOMContentLoaded", () =>
{
	const btnClose = document.getElementById("filter_collapse_btn");
	const btnOpen  = document.getElementById("filter_expand_btn"  );
	const sidebar  = document.getElementById("filter_sidebar"     );
	const closeBar = document.getElementById("filter_close_bar"   );

	if (btnClose) btnClose.addEventListener("click", (e) => { e.preventDefault(); GestionBtnClose(); });
	if (btnOpen)  btnOpen.addEventListener ("click", (e) => { e.preventDefault(); GestionBtnOpen (); });

	initFiltre();

	function initFiltre()
	{
		if (sidebar ) sidebar.classList.add    ("d-none");

		if (closeBar) closeBar.classList.remove("d-none");
	}

	function GestionBtnClose()
	{
		if (sidebar)  sidebar.classList.add    ("d-none");

		if (closeBar) closeBar.classList.remove("d-none");
	}

	function GestionBtnOpen()
	{
		if (sidebar)  sidebar.classList.remove("d-none");

		if (closeBar) closeBar.classList.add("d-none");
	}
});
