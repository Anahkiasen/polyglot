Polyglot
========

Polyglot is a localization helper for the Laravel framework.
It's an helper class but mostly it's a model you can extend.

Setting a model as polyglot will allow you to make fields speak several languages. Polyglot required you to separate common fields from localized ones following the following common pattern :

*Take the example of a blog article model*

    TABLE articles
      id INT

    TABLE article_lang
      id INT
      title VARCHAR
      content TEXT
      article_id INT

From there you can either access any language easily by doing the following : `$article->fr->title`.
**Or** you can add the following parameter to your model and let Polyglot automatically translate attributes.

```php
class Article
{
  public static $polyglot = array('title', 'content');
}

// Get an automatically localized Article
$article = Article::find(4)
echo $article->title // This will print out the title in the current language
```