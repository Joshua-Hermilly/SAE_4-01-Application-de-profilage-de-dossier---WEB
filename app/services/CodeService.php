<?php

require_once '../app/repositories/DossierCandidatRepository.php';
require_once '../app/repositories/GroupeRepository.php';

class CodeService
{
	private function analyserCodesBruts(array $codesBruts): array
	{
		$modeTous      = in_array('*', $codesBruts, true);
		$codesPositifs = [];
		$codesNegatifs = [];

		foreach ($codesBruts as $code)
		{
			if (!is_string($code)) { continue; }
			if ($code === '' || $code === '*') { continue; }

			$estNegatif = ($code[0] === '!');
			$val        = $estNegatif ? substr($code, 1) : $code;
			if ($val === '') { continue; }
			if (strncmp($val, 'cb', 2) === 0) { $val = substr($val, 2); }
			if ($val === '') { continue; }
			$valInt = (int) $val;

			if ($estNegatif) { $codesNegatifs[] = $valInt; }
			else             { $codesPositifs[] = $valInt; }
		}

		$codesPositifs = array_values(array_unique($codesPositifs));
		$codesNegatifs = array_values(array_unique($codesNegatifs));

		return [
			'modeTous'      => $modeTous,
			'codesPositifs' => $codesPositifs,
			'codesNegatifs' => $codesNegatifs,
		];
	}

	public function resoudreCodesCandidats(array $codesBruts, array $filtres = []): array
	{
		$analyse = $this->analyserCodesBruts($codesBruts);
		if (empty($analyse['codesPositifs']) && !$analyse['modeTous']) { return []; }

		if (!$analyse['modeTous']) { return $analyse['codesPositifs']; }

		$codesNegatifs = $analyse['codesNegatifs'];
		$repo          = new DossierCandidatRepository();
		$tousCodes     = $repo->findCodesByFilters($filtres);

		if (empty($codesNegatifs)) { return array_values(array_unique(array_map('intval', $tousCodes))); }

		$exclus  = array_flip($codesNegatifs);
		$result = [];
		foreach ($tousCodes as $code)
		{
			$codeInt = (int) $code;
			if (!isset($exclus[$codeInt])) { $result[] = $codeInt; }
		}

		return array_values(array_unique($result));
	}

	public function resoudreCodesGroupes(array $codesBruts, array $filtres = []): array
	{
		$analyse = $this->analyserCodesBruts($codesBruts);
		if (empty($analyse['codesPositifs']) && !$analyse['modeTous']) { return []; }

		if (!$analyse['modeTous']) { return $analyse['codesPositifs']; }

		$idsNegatifs = $analyse['codesNegatifs'];
		$repo        = new GroupeRepository();
		$tousIds     = $repo->findIdsByFilters($filtres);

		if (empty($idsNegatifs)) { return array_values(array_unique(array_map('intval', $tousIds))); }

		$exclus  = array_flip($idsNegatifs);
		$result = [];
		foreach ($tousIds as $id)
		{
			$idInt = (int) $id;
			if (!isset($exclus[$idInt])) { $result[] = $idInt; }
		}

		return array_values(array_unique($result));
	}
}