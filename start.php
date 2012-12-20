<?php

/*
|---------------------------------------------------------------------
| Loading Polyglot and Underscore
|---------------------------------------------------------------------
*/

include 'vendor/autoload.php';

Autoloader::namespaces(array(
  'Polyglot' => Bundle::path('polyglot') . 'src' .DS. 'Polyglot',
));

/*
|---------------------------------------------------------------------
| Starting Polyglot
|---------------------------------------------------------------------
*/

Polyglot\Language::locale();

/*
|---------------------------------------------------------------------
| Backup of the Language files
|---------------------------------------------------------------------
*/

$test = Polyglot\Compiler::export();
dd($test);