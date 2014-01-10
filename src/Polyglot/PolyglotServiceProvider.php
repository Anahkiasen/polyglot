<?php
namespace Polyglot;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades;

/**
 * Register the Polyglot package with the Laravel framework
 */
class PolyglotServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app = $this->bindClasses($this->app);
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		Facades\Lang::swap($this->app['polyglot.translator']);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('polyglot.translator', 'router', 'url');
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// BINDINGS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Create a Polyglot container
	 *
	 * @param  Container $app
	 *
	 * @return Container
	 */
	public static function make($app = null)
	{
		if (!$app) {
			$app = new Container;
		}

		// Bind classes
		$provider = new static($app);
		$app = $provider->bindClasses($app);

		return $app;
	}

	/**
	 * Bind the Polyglot classes to a Container
	 *
	 * @param  Container $app
	 *
	 * @return Container
	 */
	public function bindClasses(Container $app)
	{
		$app['config']->package('anahkiasen/polyglot', __DIR__.'/../config');
        
		$app->singleton('polyglot.translator', function($app) {
			return new Lang($app);
		});

        $app->singleton('router', function ($app) {
			return new Router($app['events'], $app);
		});

		$app->singleton('url', function ($app) {
			$routes = $app['router']->getRoutes();

			return new UrlGenerator($routes, $app['request']);
		});

		return $app;
	}
}
