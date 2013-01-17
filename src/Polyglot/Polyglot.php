<?php
/**
 * Polyglot
 *
 * Abstract model that eases the localization of model
 */
namespace Polyglot;

use \Eloquent;
use \Underscore\Types\Arrays;

class Polyglot extends Eloquent
{
  public static $polyglot = false;

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// RELATIONSHIPS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Reroutes functions to the language in use
   *
   * @param  string  $lang A language to use
   * @return Has_One
   */
  public function lang($lang = null)
  {
    if(!$lang) $lang = Language::current();

    return $this->$lang();
  }

  public function fr()
  {
    return $this->has_one($this->getLangClass())->where_lang('fr');
  }

  public function en()
  {
    return $this->has_one($this->getLangClass())->where_lang('en');
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// ATTRIBUTES //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Checks if a field isset while taking into account localized attributes
   *
   * @param string $key The key
   * @return boolean
   */
  public function __isset($key)
  {
    if(static::$polyglot and Language::valid($key)) return true;

    return parent::__isset($key);
  }

  /**
   * Get a localized attribute
   *
   * @param string $key The attribute
   * @return mixed
   */
  public function __get($key)
  {
    // If the attribute is set to be automatically localized
    if (static::$polyglot) {
      if (in_array($key, static::$polyglot)) {
        return $this->lang ? $this->lang->$key : null;
      }
    }

    return parent::__get($key);
  }

  /**
   * Default __toString state
   *
   * @return string [description]
   */
  public function __toString()
  {
    return $this->name;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// PUBLIC HELPERS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Localize a model with an array of lang arrays
   *
   * @param  array $localization An array in the form [field][lang][value]
   */
  public function localize($localization)
  {
    if(!$localization) return false;

    $langs = array_keys($localization[key($localization)]);

    // Build lang arrays
    foreach ($localization as $key => $value) {
      foreach ($langs as $lang) {
        ${$lang}[$key] = array_get($value, $lang);
        ${$lang}['lang'] = $lang;
      }
    }

    // Update
    foreach ($langs as $lang) {
      $this->$lang->fill($$lang);
      $this->$lang->save();
    }
  }

  /**
   * Localize a "with" method
   *
   * @return Query
   */
  public static function with_lang()
  {
    // Localize
    $eager = call_user_func_array(array('\Polyglot\Language', 'eager'), func_get_args());

    return static::with($eager);
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// HELPERS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the Lang class corresponding to the current model
   *
   * @return string
   */
  private function getLangClass()
  {
    $class = get_called_class();
    if (class_exists($class.'Lang')) return $class.'Lang';

    $class = str_replace('\\', '/', $class);
    $class = basename($class);

    return '\\'.$class.'Lang';
  }

}
