<?php
namespace Polyglot;

use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;
use Polyglot\Services\Router;
use Polyglot\Services\UrlGenerator;
use Twig_Extensions_Extension_I18n;

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
		$this->app['config']->package('anahkiasen/polyglot', __DIR__.'/config');

		// Bind services
		$this->app->singleton('polyglot.translator', 'Polyglot\Services\Lang');

		$this->app->singleton('polyglot.router', function ($app) {
			return new Router($app['events'], $app);
		});

		$this->app->singleton('polyglot.url', function ($app) {
			$routes = $app['polyglot.router']->getRoutes();

			return new UrlGenerator($routes, $app->rebinding('request', function ($app, $request) {
				$app['url']->setRequest($request);
			}));
		});

		// Bind extractors and compilers
		$this->app->bind('polyglot.compiler',  'Polyglot\Localization\Services\Compiler');
		$this->app->bind('polyglot.extractor', 'Polyglot\Localization\Services\Extractor');
		$this->app->bind('polyglot.extract',   'Polyglot\Localization\Commands\ExtractTranslations');
		$this->app->bind('polyglot.compile',   'Polyglot\Localization\Commands\CompileTranslations');

		$this->commands(['polyglot.extract', 'polyglot.compile']);
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Swap facades if need be
		if ($this->app['config']->get('polyglot::facades')) {
			Facades\Lang::swap($this->app['polyglot.translator']);
			Facades\Route::swap($this->app['polyglot.router']);
			Facades\URL::swap($this->app['polyglot.url']);
		}

		// Configure gettext
		$locale = $this->app['polyglot.url']->locale();
		$this->app['polyglot.translator']->setInternalLocale($locale);

		// Add i18n Twig extension
		if ($this->app->bound('twig')) {
			$this->app['twig']->addExtension(new Twig_Extensions_Extension_I18n);
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('polyglot.translator', 'polyglot.router', 'polyglot.url');
	}
}
