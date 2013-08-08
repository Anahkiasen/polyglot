<?php
include __DIR__.'/../vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Polyglot\PolyglotServiceProvider;

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
		$this->app['config']  = $this->mockConfig();
		$this->app->instance('request', $this->mockRequest());
		$this->app['translation.loader'] = Mockery::mock('Illuminate\Translation\FileLoader');

		Lang::setFacadeApplication($this->app);
		Config::setFacadeApplication($this->app);

		$this->app = PolyglotServiceProvider::make($this->app);
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
	 * Mock Request
	 *
	 * @return Mockery
	 */
	protected function mockRequest($segment = 'fr')
	{
		$request = Mockery::mock('Symfony\Component\HttpFoundation\Request');
		$request->shouldIgnoreMissing();
		$request->server = Mockery::mock('server')->shouldIgnoreMissing();
		$request->shouldReceive('getBaseUrl')->andReturn($segment.'/foobar');
		$request->shouldReceive('segment')->andReturn($segment);

		return $request;
	}

	/**
	 * Mock Config
	 *
	 * @return Mockery
	 */
	protected function mockConfig()
	{
		$config = Mockery::mock('Illuminate\Config\Repository');
		$config->shouldReceive('get')->with('app.locale')->andReturn('fr');
		$config->shouldReceive('get')->with('polyglot::default')->andReturn('fr');
		$config->shouldReceive('get')->with('polyglot::locales')->andReturn(array('fr', 'en'));
		$config->shouldReceive('get')->with('polyglot::model_pattern')->andReturn('{model}Lang');
		$config->shouldReceive('package');

		return $config;
	}
}
