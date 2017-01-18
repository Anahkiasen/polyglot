<?php

namespace Polyglot;

use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;
use Polyglot\Services\Router;
use Polyglot\Services\UrlGenerator;
use Twig_Extensions_Extension_I18n;

/**
 * Register the Polyglot package with the Laravel framework.
 */
class PolyglotServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $configPath = '../config/polyglot.php';

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->configPath = __DIR__.'/'.$this->configPath;

        $this->mergeConfigFrom($this->configPath, 'polyglot');

        // Bind services
        $this->app->singleton('polyglot.translator', 'Polyglot\Services\Lang');

        $this->app->singleton('Polyglot\Services\Router', function ($app) {
            $router = new Router($app['events'], $app);
            $router->setRoutes($app['router']->getRoutes());

            return $router;
        });

        $this->app->alias('Polyglot\Services\Router', 'polyglot.router');

        $this->app->singleton('polyglot.url', function ($app) {
            $routes = $app['polyglot.router']->getRoutes();

            return new UrlGenerator($routes, $app->rebinding('request', function ($app, $request) {
                $app['url']->setRequest($request);
            }));
        });

        // Bind extractors and compilers
        $this->app->bind('polyglot.compiler', 'Polyglot\Localization\Services\Compiler');
        $this->app->bind('polyglot.extractor', 'Polyglot\Localization\Services\Extractor');
        $this->app->bind('polyglot.extract', 'Polyglot\Localization\Commands\ExtractTranslations');
        $this->app->bind('polyglot.compile', 'Polyglot\Localization\Commands\CompileTranslations');

        $this->commands(['polyglot.extract', 'polyglot.compile']);
    }

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        
        $this->publishes([$this->configPath => config_path('polyglot.php')], 'config');

        // Swap facades if need be
        $this->swapFacades();

        // Configure gettext
        $locale = $this->app['polyglot.url']->locale();
        if ($locale) {
            $this->app['polyglot.translator']->setInternalLocale($locale);
        }

        // Add i18n Twig extension
        if ($this->app->bound('twig')) {
            $this->app['twig']->addExtension(new Twig_Extensions_Extension_I18n());
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['polyglot.translator', 'polyglot.router', 'polyglot.url'];
    }

    /**
     * Swap the facades with their Polyglot equivalent
     */
    protected function swapFacades()
    {
        $facades  = $this->app['config']->get('polyglot.facades');
        $bindings = ['Lang' => 'translator', 'Route' => 'router', 'URL' => 'url'];

        if ($facades) {
            $facades = $facades === true ? ['Lang', 'Route', 'URL'] : $facades;
            foreach ($facades as $facade) {
                $binding = $bindings[$facade];
                $facade  = 'Illuminate\Support\Facades\\'.$facade;
                $facade::swap($this->app['polyglot.'.$binding]);
            }
        }
    }
}
