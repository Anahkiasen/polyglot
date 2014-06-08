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
		$provider = new PolyglotServiceProvider($this->app);
		$provider->register();
		$provider->boot();

		$this->app['translator'] = $this->app['polyglot.translator'];

		// Configure facades
		Config::setFacadeApplication($this->app);
	}
}
