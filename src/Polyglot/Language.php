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
    return $this->app['lang']->getLocale();
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
    if (!$this->valid($locale)) {
      return false;
    }

    return $this->app['lang']->setLocale($locale);
  }

  /**
   * Get all available languages
   *
   * @return array An array of languages
   */
  public function getAvailable()
  {
    return $this->app['config']->get('app.languages');
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

    // Base table of languages
    $locales = array(
      'de' => array('de_DE.UTF8','de_DE@euro','de_DE','de','ge'),
      'fr' => array('fr_FR.UTF8','fr_FR','fr'),
      'es' => array('es_ES.UTF8','es_ES','es'),
      'it' => array('it_IT.UTF8','it_IT','it'),
      'pt' => array('pt_PT.UTF8','pt_PT','pt'),
      'zh' => array('zh_CN.UTF8','zh_CN','zh'),
      'en' => array('en_US.UTF8','en_US','en'),
    );

    // Set new locale
    setlocale(LC_ALL, array_get($locales, $locale, array('en_US.UTF8','en_US','en')));

    return setlocale(LC_ALL, 0);
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
