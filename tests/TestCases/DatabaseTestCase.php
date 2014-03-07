<?php
namespace Polyglot\TestCases;

use Polyglot\Polyglot;
use PHPUnit_Framework_TestCase;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Lang;
use Polyglot\PolyglotServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Capsule\Manager as Capsule;


class DatabaseTestCase extends PHPUnit_Framework_TestCase {

	protected static $app;
	protected static $capsule;

	public static function setUpBeforeClass()
	{
		date_default_timezone_set('Europe/London');

		self::$app = new Container;
		$events = new Dispatcher(self::$app);

		$events = self::mockEvents();
		self::$app['config'] = self::mockConfig();
		self::$app['events'] = $events;
		self::$app->instance('request', self::mockRequest());
		self::$app['translation.loader'] = \Mockery::mock('Illuminate\Translation\FileLoader');

		self::$app = PolyglotServiceProvider::make(self::$app);

		// Configure facades
		Config::setFacadeApplication(self::$app);
		Lang::swap(self::$app['polyglot.translator']);


		$capsule = new Capsule;
		$capsule->addConnection(array('driver' => 'sqlite', 'database' => ':memory:'));

		$capsule->setEventDispatcher(new Dispatcher(self::$app));

		$capsule->setAsGlobal();

		// Prepare Eloquent ORM for use
		$capsule->bootEloquent();


		// Grab a Database Instance
		$connection = $capsule->connection();

		self::$capsule = $capsule;
	}


	public function setUp()
	{
		self::migrate();
	}

	protected static function migrate () {
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
	 * Mock the events dispatcher
	 *
	 * @return Mockery
	 */
	protected static function mockEvents()
	{
		return \Mockery::mock('Illuminate\Events\Dispatcher');
	}

	/**
	 * Mock Request
	 *
	 * @return Mockery
	 */
	protected static function mockRequest($segment = 'fr')
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
	protected static function mockConfig()
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
