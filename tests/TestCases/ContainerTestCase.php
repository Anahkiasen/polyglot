<?php
namespace Polyglot\TestCases;

use Mockery;
use Illuminate\Container\Container;
use Illuminate\Support\Str;
use PHPUnit_Framework_TestCase;
use Polyglot\UrlGenerator;

/**
 * Base Container-mocking class
 */
abstract class ContainerTestCase extends PHPUnit_Framework_TestCase
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
		date_default_timezone_set('Europe/London');

		// Create container
		$this->app = new Container;

		// Bind mocked instances into it
		$this->app['config'] = $this->mockConfig();
		$this->app['events'] = $this->mockEvents();
		$this->app->instance('request', $this->mockRequest());
		$this->app['translation.loader'] = Mockery::mock('Illuminate\Translation\FileLoader');
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
	 * @param string $key
	 *
	 * @return object
	 */
	public function __get($key)
	{
		if ($key == 'translator') {
			return $this->app['polyglot.translator'];
		}

		$key = Str::snake($key);
		$key = str_replace('_', '.', $key);

		return $this->app[$key];
	}

	/**
	 * Set an instance on the container
	 *
	 * @param string $key
	 * @param object $value
	 */
	public function __set($key, $value)
	{
		$this->app[$key] = $value;
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////// MOCKED INSTANCES ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Mock the events dispatcher
	 *
	 * @return Mockery
	 */
	protected function mockEvents()
	{
		return Mockery::mock('Illuminate\Events\Dispatcher');
	}

	/**
	 * Get a new instance of UrlGenerator with a mock Request
	 *
	 * @param Request $request
	 *
	 * @return UrlGenerator
	 */
	protected function mockUrl($request)
	{
		$routes = $this->app['router']->getRoutes();
		$this->app['request'] = $request;
		$this->app['url']     = new UrlGenerator($routes, $request);

		return $this->app['url'];
	}

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
		$config->shouldReceive('get')->with('polyglot::locales')->andReturn(array('fr', 'en', 'de'));
		$config->shouldReceive('get')->with('polyglot::model_pattern')->andReturn('Polyglot\Dummies\{model}Lang');
		$config->shouldReceive('package');

		return $config;
	}
}
