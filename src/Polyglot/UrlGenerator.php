<?php
namespace Polyglot;

use Illuminate\Routing\UrlGenerator as IlluminateUrlGenerator;

/**
 * An UrlGenerator with localization capacities
 */
class UrlGenerator extends IlluminateUrlGenerator
{
  /**
   * Get the locale in an URL
   *
   * @return string
   */
  public function locale()
  {
    return $this->request->segment(1);
  }
}