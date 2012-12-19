<?php
abstract class PolyglotTests extends PHPUnit_Framework_TestCase
{
  // Wrappers ------------------------------------------------------ /

  /**
   * Starts the bundle
   */
  public static function setUpBeforeClass()
  {
    Bundle::start('polyglot');
    URL::$base = 'http://test';
    Config::set('application.url', '');
    Config::set('application.asset_url', '');
    Config::set('application.languages', array('fr', 'en'));
    Config::set('application.index', '');
    Config::set('application.language', 'en');
  }
}
