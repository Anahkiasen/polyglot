<?php
namespace Polyglot;

use Illuminate\Container\Container;
use Illuminate\Support\Str;

/**
 * General localization helpers
 */
class Language
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
    $this->app = $app;
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
    return $locale == $this->current();
  }

  /**
   * Returns the current language being used
   *
   * @return string A language index
   */
  public function current()
  {
    return $this->app['translator']->getLocale();
  }

  /**
   * Change the current language
   *
   * @param string $locale The language to change to
   *
   * @return string
   */
  public function set($locale)
  {
    $locale = $this->sanitize($locale);

    if (!method_exists($this->app, 'setLocale')) {
      return $this->app['translator']->setLocale($locale);
    }

    return $this->app->setLocale($locale);
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
  public function isValid($locale)
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
    $fallback = $this->app['config']->get('polyglot::default');

    return $this->isValid($locale) ? $locale : $fallback;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////////// HELPERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the correct route prefix to use
   *
   * @return array
   */
  public function getRoutesPrefix($group = array())
  {
    $locale = $this->getLocaleFromUrl();
    $this->set($locale);

    // Return group untouched if default
    if ($locale == $this->app['config']->get('polyglot::default')) {
      return $group;
    }

    return array_merge($group, array('prefix' => $locale));
  }

  /**
   * Get the locale in an URL
   *
   * @param  string $url
   *
   * @return string
   */
  public function getLocaleFromUrl($url = null)
  {
    if (!$url) {
      $locale = $this->app['request']->segment(1);
    }

    return $this->sanitize($locale);
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////////// TASKS //////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Sets the locale according to the current language
   *
   * @param  string $locale A language string to use
   * @return
   */
  public function locale($locale = false)
  {
    // If nothing was given, just use current language
    if(!$locale) {
      $locale = $this->current();
    }

    // Base table of locales
    $locales = array(
      'en' => 'en_US',
      'zh' => 'zh_CN',
    );

    // Get correct locale
    $fallback = $locale.'_'.strtoupper($locale);
    $locale   = array_get($locales, $locale, $fallback);

    return setlocale(LC_ALL, $locale);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// ELOQUENT ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Apply the correct language constraint to an array of eager load relationships
   *
   * @return array An array of relationships
   */
  public function eager()
  {
    $locale = $this->current();
    $relationships = array();

    // Get arguments
    $eager = func_get_args();
    if (sizeof($eager) == 1 and is_array($eager[0])) $eager = $eager[0];

    foreach ($eager as $r) {
      if (!Str::contains($r, 'lang')) $relationships[] = $r;
      else {
        $relationships[$r] = function($query) use ($locale) {
          $query->where_lang($locale);
        };
      }
    }

    return $relationships;
  }
}
