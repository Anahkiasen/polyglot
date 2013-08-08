<?php
namespace Polyglot;

use Illuminate\Container\Container;
use Illuminate\Translation\Translator;

/**
 * General localization helpers
 */
class Lang extends Translator
{
	/**
	 * The IoC Container
	 *
	 * @var Container
	 */
	protected $app;

	/**
	 * Build the language class
	 *
	 * @param Container $app
	 */
	public function __construct(Container $app)
	{
		$this->app    = $app;
		$this->loader = $app['translation.loader'];
		$this->locale = $app['config']->get('app.locale');
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// LOCALES //////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Whether a given language is the current one
	 *
	 * @param  string $locale The language to check
	 *
	 * @return boolean
	 */
	public function active($locale)
	{
		return $locale == $this->getLocale();
	}

	/**
	 * Get the default locale
	 *
	 * @return string
	 */
	public function defaultLocale()
	{
		return $this->app['config']->get('polyglot::default');
	}

	/**
	 * Change the current language
	 *
	 * @param string $locale The language to change to
	 *
	 * @return string
	 */
	public function setLocale($locale)
	{
		$this->locale = $this->sanitize($locale);
	}

	/**
	 * Sets the locale according to the current language
	 *
	 * @param  string $locale A language string to use
	 * @return
	 */
	public function setInternalLocale($locale = false)
	{
		// If nothing was given, just use current language
		if (!$locale) {
			$locale = $this->getLocale();
		}

		// Base table of locales
		$this->locale = $locale;
		$locales = array(
			'en' => 'en_US',
			'zh' => 'zh_CN',
		);

		// Get correct locale
		$fallback = $locale.'_'.strtoupper($locale);
		$locale   = array_get($locales, $locale, $fallback);

		setlocale(LC_ALL, $locale);

		return setlocale(LC_ALL, 0);
	}

	/**
	 * Get all available languages
	 *
	 * @return array An array of languages
	 */
	public function getAvailable()
	{
		return $this->app['config']->get('polyglot::locales');
	}

	/**
	 * Check whether a language is valid or not
	 *
	 * @param string $locale The language
	 *
	 * @return boolean
	 */
	public function valid($locale)
	{
		return in_array($locale, $this->getAvailable());
	}

	/**
	 * Sanitize a locale
	 *
	 * @param  string $locale
	 *
	 * @return string
	 */
	public function sanitize($locale)
	{
		$fallback = $this->defaultLocale();

		return $this->valid($locale) ? $locale : $fallback;
	}
}
