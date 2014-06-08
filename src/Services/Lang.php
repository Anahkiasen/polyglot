<?php
namespace Polyglot\Services;

use Illuminate\Container\Container;
use Illuminate\Translation\Translator;
use Illuminate\Support\Str;

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
	 * The translation domain
	 *
	 * @var string
	 */
	protected $domain;

	/**
	 * The localization encoding
	 *
	 * @var string
	 */
	protected $encoding = 'UTF-8';

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

		$this->domain = $app['config']->get('polyglot::domain');
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// DOMAIN ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the translation domain
	 *
	 * @return string
	 */
	public function getDomain()
	{
		return $this->domain;
	}

	/**
	 * Get the folder where the translations reside
	 *
	 * @param string $subfolder
	 *
	 * @return string
	 */
	public function getTranslationsFolder($subfolder = null)
	{
		$subfolder = $subfolder ? '/'.$subfolder : $subfolder;

		return $this->app['config']->get('polyglot::folder').$subfolder;
	}

	/**
	 * Get the folder where a locale's translations reside
	 *
	 * @param string $locale
	 *
	 * @return string
	 */
	public function getLocaleFolder($locale)
	{
		$folder = sprintf('%s.%s/LC_MESSAGES', $this->shortToLongLocale($locale), $this->getEncoding(true));

		return $this->getTranslationsFolder($folder);
	}

	/**
	 * Get the encoding
	 *
	 * @param boolean $slug
	 *
	 * @return string
	 */
	public function getEncoding($slug = false)
	{
		return $slug ? Str::slug($this->encoding, '') : $this->encoding;
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////// TRANSLATIONS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the translation for the given key, or fallback to fallback locale
	 *
	 * @param  string $key
	 * @param  array  $replace
	 * @param  string $locale
	 * @return string
	 */
	public function get($key, array $replace = array(), $locale = null)
	{
		// Get translation and fallback
		$fallback    = $this->fallbackLocale();
		$translation = parent::get($key, $replace, $locale);
		if ($translation == $key and $fallback !== $this->locale) {
			return parent::get($key, $replace, $fallback);
		}

		return $translation;
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// LOCALES //////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Whether a given language is the current one
	 *
	 * @param string $locale The language to check
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
	 * Get the fallback locale
	 *
	 * @return string
	 */
	public function fallbackLocale()
	{
		return $this->app['config']->get('polyglot::fallback') ?: $this->app['config']->get('polyglot::default');
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
	 * Get the internal locale
	 *
	 * @return string
	 */
	public function getInternalLocale()
	{
		return setlocale(LC_ALL, 0);
	}

	/**
	 * Sets the locale according to the current language
	 *
	 * @param string $locale A language string to use
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
		$this->app->setLocale($locale);

		$locale = $this->shortToLongLocale($locale).'.'.$this->getEncoding(true);

		// Set locale
		putenv('LC_ALL='.$locale);
		setlocale(LC_ALL, $locale);

		// Specify the location of the translation tables
		bindtextdomain($this->domain, $this->getTranslationsFolder());
		textdomain($this->domain);

		return $this->getInternalLocale();
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
	 * @param string $locale
	 *
	 * @return string
	 */
	public function sanitize($locale = null)
	{
		$fallback = $this->defaultLocale();

		return $this->valid($locale) ? $locale : $fallback;
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// HELPERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Translate a short locale to long (en => en_US)
	 *
	 * @param string $locale
	 *
	 * @return string
	 */
	public function shortToLongLocale($locale)
	{
		$locales = array(
			'en' => 'en_US',
			'zh' => 'zh_CN',
		);

		// Get correct locale
		$fallback = $locale.'_'.strtoupper($locale);
		$locale   = array_get($locales, $locale, $fallback);

		return $locale;
	}
}
