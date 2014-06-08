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

class DatabaseTestCase extends PolyglotTestCase
{
	/**
	 * The Database Capsule
	 *
	 * @var Capsule
	 */
	protected $capsule;

	/**
	 * Set up the tests
	 */
	public function setUp()
	{
		parent::setUp();

		$this->createCapsule();
	}

	/**
	 * Create the database Capsule
	 *
	 * @return void
	 */
	protected function createCapsule()
	{
		$capsule = new Capsule;
		$capsule->addConnection(array(
			'driver'   => 'sqlite',
			'database' => ':memory:'
		));

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
		$schema->create('article_lang', function ($table) {
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
		parent::tearDown();

		Capsule::table('articles')->truncate();
		Capsule::table('article_lang')->truncate();
	}
}
