<?php
namespace Polyglot\TestCases;

use Polyglot\Polyglot;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\Capsule\Manager as Capsule;

class DatabaseTestCase extends PolyglotTestCase
{
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

		$capsule->setEventDispatcher($this->app['events']);
		$capsule->setAsGlobal();

		// Prepare Eloquent ORM for use
		$capsule->bootEloquent();

		$schema = $capsule->schema();
		$schema->dropIfExists('articles');
		$schema->create('articles', function ($table) {
			$table->increments('id');
			$table->string('name');
			$table->timestamps();
		});

		$schema->dropIfExists('article_lang');
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
