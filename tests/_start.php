<?php
include __DIR__.'/../vendor/autoload.php';
include __DIR__.'/Dummies/Lang.php';

use Illuminate\Container\Container;

abstract class PolyglotTests extends PHPUnit_Framework_TestCase
{
  /**
   * The current IoC Container
   *
   * @var Container
   */
  protected $app;

  /**
   * Set up the tests
   */
  public function setUp()
  {
    $this->app = new Container;

    $this->app['config'] = $this->getConfig();
    $this->app['lang']   = new Lang;

    $this->app->bind('polyglot.lang', function($app) {
      return new Polyglot\Language($app);
    });
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// INSTANCES //////.////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get a mock of Config
   *
   * @return Mockery
   */
  protected function getConfig()
  {
    $config = Mockery::mock('Illuminate\Config\Repository');
    $config->shouldReceive('get')->with('app.languages')->andReturn(array('fr', 'en'));

    return $config;
  }
}
