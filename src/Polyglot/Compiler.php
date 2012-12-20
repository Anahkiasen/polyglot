<?php
namespace Polyglot;

use \Underscore\Types\Arrays;
use \Underscore\Types\String;
use \Underscore\Parse;
use \Lang;
use \File;

class Compiler
{
  /**
   * Flattens out all language strings in a
   * single language file for easier export
   *
   * @return array A flattened lang array
   */
  public static function export($output = null)
  {
    // Gather all language files
    $files = path('app').'language/*/*';
    $files = glob($files);

    // Create translations array
    $translations = array();
    $languages = array();

    // Iterate over the files and merge them
    foreach ($files as $file) {

      // Gather informations about the file
      $basename = String::from(basename($file))->remove('.php')->obtain();
      $language = preg_replace('/.+language\/([a-z]{2}).+/', '$1', $file);
      if (!in_array($language, $languages)) $languages[] = $language;

      // Skip some files
      if ($basename == 'pagination') continue;
      if ($basename == 'validation') {
        $translations[$language][$basename] = Lang::line($basename.'.custom')->get($language);
        $translations[$language][$basename] = Lang::line($basename.'.attributes')->get($language);
        continue;
      }

      // Add translations to the array
      $lines = Lang::line($basename)->get($language);
      foreach ($lines as $line => $trans) {
        $translations[$language][$basename][$line] = $trans;
      }
    }

    // If no translations found, cancel
    if (!$translations) return false;

    // Flatten and sort the final array
    $translations = Arrays::from($translations)
      ->flatten()->sortKeys();

    // Save to file
    if ($output) {
      $output = path('storage').'work'.DS.'localization'.DS.$output;
      File::put($output, $translations->toCSV());
    }

    return $translations->obtain();
  }

}