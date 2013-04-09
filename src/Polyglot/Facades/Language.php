<?php
namespace Polyglot;

use Illuminate\Support\Facades\Facade;

class Language extends Facade
{
  public function getFacadeAccessor()
  {
    return 'polyglot.language';
  }
}