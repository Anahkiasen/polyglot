<?php
namespace Polyglot;

use Illuminate\Database\Eloquent\Model;
use Underscore\Methods\ArraysMethods as Arrays;

/**
 * Abstract model that eases the localization of model
 */
class Polyglot extends Model
{

  /**
   * An array of polyglot attributes
   *
   * @var array
   */
  public static $polyglot = array();

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
   * @return string
   */
  public function __toString()
  {
    return (string) $this->name;
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
    $class = $this->getLangClass();
    foreach ($langs as $lang) {
      if (!is_object($this->$lang)) {
        $class = new $class($$lang);
        $this->$lang()->insert($class);
      } else {
        $this->$lang->fill($$lang);
        $this->$lang->save();
      }
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
