<?php
/**
 * Language
 *
 * General localization helpers
 */
namespace Polyglot;

use \Config;
use \Lang;
use \Section;
use \Underscore\Types\Arrays;
use \Underscore\Parse;
use \Underscore\Types\String;
use \URL;

class Language
{
  ////////////////////////////////////////////////////////////////////
  ////////////////////////// TRANSLATIONS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Injects a translated title into the page
   *
   * @param  string $title A page or a title
   *
   * @return string        A title Blade section
   */
  public static function title($title = null)
  {
    $title = Lang::line($title, null)->get();

    return Section::inject('title', $title);
  }

  /**
   * Translates a string with various fallbacks points
   *
   * @param  string $key      The key/string to translate
   * @param  string $fallback A fallback to display
   *
   * @return string           A translated string
   */
  public static function translate($key, $fallback = null)
  {
    if (!$fallback) $fallback = $key;

    // Search for the key itself
    $translation = Lang::line($key)->get(null, '');

    // If not found, search in the field attributes
    if (!$translation) {
      $translation = Lang::line('validation.attributes.'.$key)
        ->get(null, $fallback);
    }

    // If we found a translations array
    if (is_array($translation)) $translation = $fallback;

    return ucfirst($translation);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Whether a given language is the current one
   *
   * @param  string $language The language to check
   *
   * @return boolean
   */
  public static function isActive($language)
  {
    return $language == Language::current();
  }

  /**
   * Returns the current language being used
   *
   * @return string A language index
   */
  public static function current()
  {
    return Config::get('application.language');
  }

  /**
   * Change the current language
   *
   * @param string $language The language to change to
   *
   * @return string
   */
  public static function set($language)
  {
    if (!static::valid($language)) return false;

    return Config::set('application.language', $language);
  }

  /**
   * Get all available languages
   *
   * @return array An array of languages
   */
  public static function available()
  {
    return Config::get('application.languages');
  }

  /**
   * Check whether a language is valid or not
   *
   * @param string $language The language
   * @return boolean
   */
  public static function valid($language)
  {
    return in_array($language, static::available());
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// URLS //////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the URL to switch language, keeping the current page or not
   *
   * @param  string  $lang  The new language
   * @param  boolean $reset Whether navigation should be reset
   * @return string         An URL
   */
  public static function to($lang, $reset = false)
  {
    // Reset path or not
    if($reset) return URL::base().'/'.$lang;

    // Check for invalid languages
    if(!static::valid($lang)) $lang = static::current();

    // Compute base URL with language added
    $base    = trim(URL::base(), '/');
    $base   .= '/'.$lang.'/';
    $current = URL::current();

    // Replace base with localized base
    $final = preg_replace('#' .URL::base(). '/?#', $base, $current);

    return $final;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// TASKS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Sets the locale according to the current language
   *
   * @param  string $language A language string to use
   * @return
   */
  public static function locale($language = false)
  {
    // If nothing was given, just use current language
    if(!$language) $language = self::current();

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
    setlocale(LC_ALL, Arrays::get($locales, $language, array('en_US.UTF8','en_US','en')));

    return setlocale(LC_ALL, 0);
  }

  /**
   * Flattens out all language string in the current language for easier export
   *
   * @return array A flattened lang array
   */
  public static function compile($output = null)
  {
    $files = glob(path('app').'language/' .static::current(). '/*');

    // Fetch the content of all the language files
    foreach ($files as $file) {
      $file = File::name($file);
      if ($file == 'pagination') continue;
      if ($file == 'validation') {
        $lang[$file] = Lang::line($file.'.custom')->get();
        $lang[$file] = Lang::line($file.'.attributes')->get();
      } else $lang[$file] = Lang::line($file)->get();
    }

    // If the website isn't localized, cancel
    if(!isset($lang)) return false;

    // Flatten the final array
    $lang = Arrays::flatten($lang);

    // Sort the array
    ksort($lang);

    // If we provided an output file, save to it
    if ($output) {
      $lang = Parse::toCSV($lang);
      \File::put(path('storage').'work'.DS.$output, $lang);
    }

    return $lang;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// ELOQUENT ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Apply the correct language constraint to an array of eager load relationships
   *
   * @return array An array of relationships
   */
  public static function eager()
  {
    $language = static::current();
    $relationships = array();

    // Get arguments
    $eager = func_get_args();
    if (sizeof($eager) == 1 and is_array($eager[0])) $eager = $eager[0];

    foreach ($eager as $r) {
      if (!String::find($r, 'lang')) $relationships[] = $r;
      else {
        $relationships[$r] = function($query) use ($language) {
          $query->where_lang($language);
        };
      }
    }

    return $relationships;
  }
}
