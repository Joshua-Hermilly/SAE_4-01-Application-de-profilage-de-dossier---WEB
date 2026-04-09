<?php

trait FormTrait
{
	/**
	 * Nettoie une chaîne pour éviter les failles XSS
	 */
	private function sanitizeInput($input)
	{
		return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Récupère un paramètre POST en le nettoyant
	 */
	protected function getPostParam($key, $default = null)
	{
		if (!isset($_POST[$key]))
		{
			return $default;
		}
		$value = $_POST[$key];
		if (is_array($value))
		{
			$clean = array();
			foreach ($value as $v)
			{
				$clean[] = is_string($v) ? $this->sanitizeInput($v) : $v;
			}
			return $clean;
		}
		return is_string($value) ? $this->sanitizeInput($value) : $value;
	}

	/**
	 * Récupère un paramètre GET en le nettoyant
	 */
	protected function getQueryParam($key, $default = null)
	{
		if (!isset($_GET[$key]))
		{
			return $default;
		}
		$value = $_GET[$key];
		if (is_array($value))
		{
			$clean = array();
			foreach ($value as $v)
			{
				$clean[] = is_string($v) ? $this->sanitizeInput($v) : $v;
			}
			return $clean;
		}
		return is_string($value) ? $this->sanitizeInput($value) : $value;
	}

	/**
	 * Récupère et nettoie l'ensemble des paramètres POST
	 */
	protected function getAllPostParams()
	{
		$clean = array();
		foreach ($_POST as $k => $v)
		{
			if (is_array($v))
			{
				$sub = array();
				foreach ($v as $vv)
				{
					$sub[] = is_string($vv) ? $this->sanitizeInput($vv) : $vv;
				}
				$clean[$k] = $sub;
			} else
			{
				$clean[$k] = is_string($v) ? $this->sanitizeInput($v) : $v;
			}
		}
		return $clean;
	}

	/**
	 * Récupère et nettoie l'ensemble des paramètres GET
	 */
	protected function getAllQueryParams()
	{
		$clean = array();
		foreach ($_GET as $k => $v)
		{
			if (is_array($v))
			{
				$sub = array();
				foreach ($v as $vv)
				{
					$sub[] = is_string($vv) ? $this->sanitizeInput($vv) : $vv;
				}
				$clean[$k] = $sub;
			} else
			{
				$clean[$k] = is_string($v) ? $this->sanitizeInput($v) : $v;
			}
		}
		return $clean;
	}
}
