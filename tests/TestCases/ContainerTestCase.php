<?php
namespace Polyglot\TestCases;

use Illuminate\Container\Container;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Support\Str;
use Mockery;
use PHPUnit_Framework_TestCase;
use Polyglot\Services\UrlGenerator;

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

		$provider = new EventServiceProvider($this->app);
		$provider->register();

		// Bind mocked instances into it
		$this->app['config'] = $this->mockConfig();
		$this->app->instance('request', $this->mockRequest());
		$this->app['translation.loader'] = Mockery::mock('Illuminate\Translation\FileLoader');

		$this->app->instance('Illuminate\Container\Container', $this->app);
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
		if (in_array($key, ['translator', 'url', 'router'])) {
			$key = 'polyglot.'.$key;
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
	 * Get a new instance of UrlGenerator with a mock Request
	 *
	 * @param Request $request
	 *
	 * @return UrlGenerator
	 */
	protected function mockUrl($request)
	{
		$this->app['request'] = $request;
		$this->app['polyglot.url']->setRequest($request);

		return $this->app['polyglot.url'];
	}

	/**
	 * Mock Request
	 *
	 * @return Mockery
	 */
	protected function mockRequest($segment = 'fr')
	{
		$request = Mockery::mock('Illuminate\Http\Request');
		$request->shouldIgnoreMissing();
		$request->server = Mockery::mock('server')->shouldIgnoreMissing();
		$request->shouldReceive('getBaseUrl')->andReturn($segment.'/foobar');
		$request->shouldReceive('segment')->andReturn($segment);

		if ($this->app->bound('polyglot.url')) {
			$this->app['polyglot.url']->setRequest($request);
		}

		return $request;
	}

	/**
	 * Mock Config
	 *
	 * @return Mockery
	 */
	protected function mockConfig($options = array())
	{
		$options = array_merge(array(
			'app.locale'              => 'fr',
			'polyglot::folder'        => __DIR__.'/../_locales',
			'polyglot::domain'        => 'test',
			'polyglot::facades'       => false,
			'polyglot::default'       => 'fr',
			'polyglot::locales'       => array('fr', 'en', 'de'),
			'polyglot::model_pattern' => 'Polyglot\Dummies\{model}Lang',
		), $options);

		$config = Mockery::mock('Illuminate\Config\Repository');
		$config->shouldReceive('package');

		foreach ($options as $key => $value) {
			$config->shouldReceive('get')->with($key)->andReturn($value);
		}

		return $config;
	}
}
