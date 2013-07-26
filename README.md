Polyglot
========

Polyglot is a localization helper for the Laravel framework, it's an helper class but mostly it's a model you can extend.

Setting a model as polyglot will allow you to make fields speak several languages. Polyglot requires you to separate common fields from localized ones assuming the following common pattern :

*Take the example of a blog article model*

    TABLE articles
      id INT
      category_id INT
      created_at DATETIME
      updated_at DATETIME

    TABLE article_lang
      id INT
      title VARCHAR
      content TEXT
      article_id INT
      lang ENUM

From there you can either access any language easily by doing the following : `$article->fr->title`.
**Or** you can add the following parameter to your model and let Polyglot automatically translate attributes.

```php
class Article
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