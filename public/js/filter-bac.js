document.addEventListener("DOMContentLoaded", function ()
{
	const serieBacSelect       = document.getElementById  ("filter_serie_bac"    );
	const typeBacSelect        = document.getElementById  ("filter_type_bac"     );
	const specialiteCheckboxes = document.querySelectorAll(".specialite-checkbox");

	hideSpecialiteSection();
	hideOptionSection();

	function hideSpecialiteSection()
	{
		const speLabel = document.querySelector('[data-filter-name="specialite_spe"] > p');
		const speBlock = document.getElementById("specialite-checkboxes-specialite_spe");

		console.log(speLabel)
		if (speLabel) speLabel.style.display = "none";
		if (speBlock) speBlock.style.display = "none";

		for (const cb of specialiteCheckboxes)
		{
			if (cb.name === "specialite_spe[]")
			{
				cb.checked = false;
				const container = cb.closest("p") || cb.parentElement;
				if (container) { container.style.display = "none"; }
			}
		}
	}

	function showSpecialiteSection()
	{
		const speLabel = document.querySelector('[data-filter-name="specialite_spe"] > p');
		const speBlock = document.getElementById("specialite-checkboxes-specialite_spe");

		if (speLabel) speLabel.style.display = "block";
		if (speBlock) speBlock.style.display = "block";
	}

	function hideOptionSection()
	{
		const optLabel = document.querySelector('[data-filter-name="specialite_opt"] > p');
		const optBlock = document.getElementById("specialite-checkboxes-specialite_opt");

		if (optLabel) optLabel.style.display = "none";
		if (optBlock) optBlock.style.display = "none";

		for (const cb of specialiteCheckboxes)
		{
			if (cb.name === "specialite_opt[]")
			{
				cb.checked = false;
				const container = cb.closest("p") || cb.parentElement;

				if (container) container.style.display = "none";
			}
		}
	}

	function showOptionSection()
	{
		const optLabel = document.querySelector('[data-filter-name="specialite_opt"] > p');
		const optBlock = document.getElementById("specialite-checkboxes-specialite_opt");

		if (optLabel) optLabel.style.display = "block";
		if (optBlock) optBlock.style.display = "block";
	}

	function serieHasSpecialites(serieValue)
	{
		if (!serieValue) return false;

		let trouve = false;
		for (let sc of specialiteCheckboxes)
		{
			if (sc.getAttribute("data-code") === serieValue && sc.name === "specialite_spe[]") { trouve = true; }
		}
		return trouve;
	}

	function serieHasOptions(serieValue)
	{
		if (!serieValue) { return false; }

		let trouve = false;
		for (let checkbox of specialiteCheckboxes)
		{
			if (checkbox.getAttribute("data-code") === serieValue && checkbox.name === "specialite_opt[]") { trouve = true; }
		}
		return trouve;
	}

	function filterSpecialites()
	{
		const selectedSerieCode = serieBacSelect ? serieBacSelect.value : "";
		if (!selectedSerieCode || !serieHasSpecialites(selectedSerieCode)) { hideSpecialiteSection(); }
		else
		{
			showSpecialiteSection();
			for (let checkbox of specialiteCheckboxes)
			{
				if (checkbox.name === "specialite_spe[]")
				{
					const speCode   = checkbox.getAttribute("data-code");
					const container = checkbox.closest("p") || checkbox.parentElement;

					if (!container                   ) { return;                           }
					if (speCode === selectedSerieCode) { container.style.display = "flex"; }
					else
					{
						container.style.display = "none";
						checkbox.checked = false;
					}
				}
			}
		}
		if (!selectedSerieCode || !serieHasOptions(selectedSerieCode)) { hideOptionSection(); }
		else
		{
			showOptionSection();
			specialiteCheckboxes.forEach(checkbox =>
			{
				if (checkbox.name === "specialite_opt[]")
				{
					const optCode   = checkbox.getAttribute("data-code");
					const container = checkbox.closest("p") || checkbox.parentElement;

					if (!container                   ) { return;                           }
					if (optCode === selectedSerieCode) { container.style.display = "flex"; }
					else
					{
						container.style.display = "none";
						checkbox.checked = false;
					}
				}
			});
		}
	}

	function resetBloc(bloc)
	{
		const inputs = bloc.querySelectorAll("input, select, textarea");
		for (let input of inputs)
		{
			if (input.type === "checkbox" || input.type === "radio") { input.checked = false; }
			else                                                     { input.value   = "";    }
		}
	}

	function onTypeBacChange()
	{
		const val = typeBacSelect ? typeBacSelect.value : "";

		if (serieBacSelect) { serieBacSelect.value = ""; }

		hideSpecialiteSection();
		hideOptionSection();

		const blocs = document.querySelectorAll(".bac-filter");
		for (let bloc of blocs)
		{
			bloc.style.display = "none";
			resetBloc(bloc);
		}

		if (val)
		{
			const blocToShow = document.querySelector(`[data-bac-type="${val}"]`);
			if (blocToShow) { blocToShow.style.display = "block"; }
		}
	}

	if (typeBacSelect ) { typeBacSelect .addEventListener("change", onTypeBacChange  ); }
	if (serieBacSelect) { serieBacSelect.addEventListener("change", filterSpecialites); }
});