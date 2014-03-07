<?php
namespace Polyglot\TestCases;

use Mockery;
use Polyglot\Polyglot;
use PHPUnit_Framework_TestCase;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Lang;
use Polyglot\PolyglotServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Capsule\Manager as Capsule;


class DatabaseTestCase extends PHPUnit_Framework_TestCase {

	protected $app;
	protected $capsule;


	function __construct() {
		parent::__construct();

		date_default_timezone_set('Europe/London');

		$this->app = new Container;
		$events = new Dispatcher($this->app);

		$events = $this->mockEvents();
		$this->app['config'] = $this->mockConfig();
		$this->app['events'] = $events;
		$this->app->instance('request', $this->mockRequest());
		$this->app['translation.loader'] = \Mockery::mock('Illuminate\Translation\FileLoader');

		$this->app = PolyglotServiceProvider::make($this->app);

		// Configure facades
		Config::setFacadeApplication($this->app);
		Lang::swap($this->app['polyglot.translator']);


		$capsule = new Capsule;
		$capsule->addConnection(array('driver' => 'sqlite', 'database' => ':memory:'));

		$capsule->setEventDispatcher(new Dispatcher($this->app));

		$capsule->setAsGlobal();

		// Prepare Eloquent ORM for use
		$capsule->bootEloquent();

		// Grab a Database Instance
		$connection = $capsule->connection();

		$this->capsule = $capsule;
		$schema = Capsule::schema();
		$schema->dropIfExists('articles');
		$schema->create('articles', function ($table) {
			$table->increments('id');
			$table->string('name');
			$table->timestamps();
		});


		$schema->dropIfExists('article_langs');
		$schema->create('article_langs', function ($table) {
			$table->increments('id');
			$table->string('title');
			$table->integer('real_article_id');
			$table->string('lang');
		});
	}

	/**
	 * Clean up mocked instances
	 *
	 * @return void
	 */
	public function tearDown()
	{
		Capsule::table('articles')->truncate();
		Capsule::table('article_langs')->truncate();
		Mockery::close();
	}

	/**
	 * Mock the events dispatcher
	 *
	 * @return Mockery
	 */
	protected function mockEvents()
	{
		return \Mockery::mock('Illuminate\Events\Dispatcher');
	}

	/**
	 * Mock Request
	 *
	 * @return Mockery
	 */
	protected function mockRequest($segment = 'fr')
	{
		$request = \Mockery::mock('Symfony\Component\HttpFoundation\Request');
		$request->shouldIgnoreMissing();
		$request->server = \Mockery::mock('server')->shouldIgnoreMissing();
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
		$config = \Mockery::mock('Illuminate\Config\Repository');
		$config->shouldReceive('get')->with('app.locale')->andReturn('fr');
		$config->shouldReceive('get')->with('polyglot::default')->andReturn('fr');
		$config->shouldReceive('get')->with('polyglot::locales')->andReturn(array('fr', 'en'));
		$config->shouldReceive('get')->with('polyglot::model_pattern')->andReturn('Polyglot\Dummies\{model}Lang');
		$config->shouldReceive('package');

		return $config;
	}
}
