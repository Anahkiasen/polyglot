<?php
namespace Polyglot\TestCases;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Polyglot\PolyglotServiceProvider;

abstract class PolyglotTestCase extends ContainerTestCase
{
	/**
	 * Set up the tests
	 */
	public function setUp()
	{
		parent::setUp();

		// Bind Polyglot classes
		$this->app = PolyglotServiceProvider::make($this->app);

		// Configure facades
		Config::setFacadeApplication($this->app);
		Lang::swap($this->app['polyglot.translator']);
	}
}
