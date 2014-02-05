<?php

/*
|---------------------------------------------------------------------
| Loading Polyglot and Underscore
|---------------------------------------------------------------------
*/

// include 'vendor/autoload.php';

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

$language = Cache::remember('language', function() {
  return Polyglot\Compiler::export();
}, 60 * 24 * 30);
