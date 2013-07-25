<?php
namespace Polyglot;

use Illuminate\Support\ServiceProvider;

/**
 * Register the Polyglot package with the Laravel framework
 */
class PolyglotServiceProvider extends ServiceProvider
{
  /**
   * Register classes
   */
  public function register()
  {
    $this->app['config']->package('anahkiasen/polyglot', __DIR__.'/../config');

    $this->app->bind('polyglot.lang', function($app) {
      return new Language($app);
    });
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return array('polyglot');
  }
}
