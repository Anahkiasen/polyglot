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
    $this->app->bind('polyglot.language', function($app) {
      return new Language;
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
