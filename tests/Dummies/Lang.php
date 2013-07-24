<?php
/**
 * Mock of the Translator class
 */
class Lang
{
	/**
	 * The current locale
	 *
	 * @var string
	 */
	protected $locale;

	/**
	 * Set a new locale
	 *
	 * @param string $locale
	 */
	public function setLocale($locale)
	{
		$this->locale = $locale;
	}

	/**
	 * Get the current locale
	 *
	 * @return string
	 */
	public function getLocale()
	{
		return $this->locale;
	}
}