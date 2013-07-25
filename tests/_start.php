<?php
include __DIR__.'/../vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Support\Str;

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

    $this->app['config']     = $this->mockConfig();
    $this->app['translator'] = new Lang;

    $this->app->bind('polyglot.lang', function($app) {
      return new Polyglot\Language($app);
    });
  }

  /**
   * Clean up mocked instances
   *
   * @return void
   */
  public function tearDown()
  {
    Mockery::close();
  }

  /**
   * Get an instance from the Container
   *
   * @param  string $key
   *
   * @return object
   */
  public function __get($key)
  {
    $key = Str::snake($key);
    $key = str_replace('_', '.', $key);

    return $this->app[$key];
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// INSTANCES //////.////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Mock Config
   *
   * @return Mockery
   */
  protected function mockConfig()
  {
    $config = Mockery::mock('Illuminate\Config\Repository');
    $config->shouldReceive('get')->with('polyglot::locales')->andReturn(array('fr', 'en'));

    return $config;
  }
}
