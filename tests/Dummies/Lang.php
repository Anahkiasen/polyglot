<?php
class Lang
{
	protected $locale;

	public function setLocale($locale)
	{
		$this->locale = $locale;
	}

	public function getLocale()
	{
		return $this->locale;
	}
}