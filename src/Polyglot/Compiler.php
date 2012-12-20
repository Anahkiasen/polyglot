<?php
namespace Polyglot;

use \File;
use \Lang;
use \Underscore\Parse;
use \Underscore\Types\Arrays;
use \Underscore\Types\String;

class Compiler
{
  /**
   * Flattens out all language strings in a
   * single language file for easier export
   *
   * @param  string $output A folder to export the file to
   *
   * @return array A flattened lang array
   */
  public static function export()
  {
    // Get all translations, cancel if empty
    $translations = static::getTranslations();
    if (!$translations) return false;

    // Flatten and sort the final array
    $translations = Arrays::from($translations)->flatten()->sortKeys();

    // Save to file
    $output = path('storage').'work'.DS.'languages.csv';
    File::put($output, $translations->toCSV());

    return $translations->obtain();
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get an extended array of the available language files
   *
   * @return array
   */
  private static function getLanguageFiles()
  {
    // Gather all language files
    $files = path('app').'language/*/*';
    $files = glob($files);

    // Format the array with additional informations
    $files = Arrays::each($files, function($file) {
      $name = String::from(basename($file))->remove('.php')->obtain();
      $language = preg_replace('/.+language\/([a-z]{2}).+/', '$1', $file);

      return array(
        'path'     => $file,
        'name'     => $name,
        'language' => $language,
      );
    });

    return $files;
  }

  /**
   * Get all translations as one unique array
   *
   * @return array
   */
  private static function getTranslations()
  {
    // Create translations array
    $files = static::getLanguageFiles();
    $translations = array();

    // Iterate over the files and merge them
    foreach ($files as $file) {
      extract($file);

      // Skip some files
      if ($name == 'pagination') continue;
      if ($name == 'validation') {
        $translations[$language][$name] = Lang::line($name.'.custom')->get($language);
        $translations[$language][$name] = Lang::line($name.'.attributes')->get($language);
        continue;
      }

      // Add translations to the array
      $lines = Lang::line($name)->get($language);
      foreach ($lines as $line => $trans) {
        $translations[$language][$name][$line] = $trans;
      }
    }

    return $translations;
  }
}