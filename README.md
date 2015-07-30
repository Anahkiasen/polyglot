# Polyglot

[![Build Status](https://travis-ci.org/Anahkiasen/polyglot.png?branch=master)](https://travis-ci.org/Anahkiasen/polyglot)
[![Latest Stable Version](https://poser.pugx.org/anahkiasen/polyglot/v/stable.png)](https://packagist.org/packages/anahkiasen/polyglot)
[![Total Downloads](https://poser.pugx.org/anahkiasen/polyglot/downloads.png)](https://packagist.org/packages/anahkiasen/polyglot)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Anahkiasen/polyglot/badges/quality-score.png?s=20d9a4be6695b7677c427eab73151c1a9d803044)](https://scrutinizer-ci.com/g/Anahkiasen/polyglot/)
[![Code Coverage](https://scrutinizer-ci.com/g/Anahkiasen/polyglot/badges/coverage.png?s=f6e022cbcf1a51f82b5d9e6fb30bd1643fc70e76)](https://scrutinizer-ci.com/g/Anahkiasen/polyglot/)

## Introduction

Polyglot is a localization helper for the Laravel framework, it's an helper class to localize your routes, models and views.

To install it, do `composer require anahkiasen/polyglot`, then add `Polyglot\PolyglotServiceProvider` to the `providers` array in `app/config/app.php`.

Publish config to your laravel app : `php artisan config:publish anahkiasen/polyglot`

## Model localization

Setting a model as polyglot will allow you to make fields speak several languages. Polyglot requires you to separate common fields from localized ones assuming the following common pattern :

*Take the example of a blog article model*

    TABLE articles
      id INT
      category_id INT
      created_at DATETIME
      updated_at DATETIME

    TABLE article_langs
      id INT
      title VARCHAR
      content TEXT
      article_id INT
      lang ENUM

From there you can either access any language easily by doing the following : `$article->fr->title`.
**Or** you can add the following parameter to your model and let Polyglot automatically translate attributes.

```php
class Article extends Polyglot
{
  protected $polyglot = ['title', 'content'];
}

// Get an automatically localized Article
$article = Article::find(4)

echo $article->fr->title // This will print out the french title
echo $article->title // This will print out the title in the current language
```

Polyglot also helps you saving localized attributes :

```php
$article->fill([
  'title'   => 'Titre',
  'content' => 'Contenu',
  'lang'    => 'fr',
])->save();

// Is the same as

$article->fr->fill([
  'title'   => 'Titre',
  'content' => 'Contenu',
])->save();
```

Globally speaking when Polyglot sees you're trying to save localized attribute on the parent model, it will automatically fetch the Lang model and save them on it instead.
If no `lang` attribute is passed, Polyglot will use the current language.

Note that, as your attributes are now split into two tables, you can Polyglot eager load the correct Lang relation with the `withLang` method.
Per example `Article::withLang()->get()` will return Articles with `fr` autoloaded if it's the current language, or `en`, according to `app.locale`.

## Routes localization

To localize your routes, you need to set the `locales` option in your config file, per example `array('fr', 'en')`. Now you may define your routes as such :

```php
Route::groupLocale(['before' => 'auth'], function() {
  Route::get('/', 'HomeController@index');
  Route::get('articles', 'ArticlesController@index');
  // etc...
});
```

Now you can access `/fr` and `/fr/articles`, or `/en` and `/en/articles` – Polyglot will recognize the locale in the URL and automatically set your app in that language.
There is also a `default` option in the config file, setting that option to a locale like `'default' => 'fr'` will make the root URLs point to that locale. So accessing `/articles` without prefixing it with a locale would render the page in french.

## Views localization

Views localization work by setting up gettext for you and providing two commands to extract translations from your views to PO files and compile those to MO files.

This is currently only possible for Twig but will soon for Blade and classic PHP files, and will require the `twig/extensions` package which adds gettext support for Twig.

To use simply configure your domain in the configuration, then run `php artisan lang:extract`.

## Locales helpers

Polyglot also provide various locale helpers hooked into the `Lang` and `URL` class you know and love :

```php
URL::locale() // Returns the locale in the current URL
URL::switchLanguage('fr') // Returns the current url with different locale

Lang::active('fr') // Check if fr is the current locale
Lang::setInternalLocale('fr') // Set both the locale with the Translator class and setlocale method
Lang::valid('fr') // Check if a locale is valid
Lang::sanitize('fr') // Returns the locale if valid, or the default locale if not
```
