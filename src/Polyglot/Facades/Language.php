<?php
namespace Polyglot\Facades;

use Illuminate\Support\Facades\Facade;

class Language extends Facade
{
  public static function getFacadeAccessor()
  {
    return 'polyglot.lang';
  }
}
