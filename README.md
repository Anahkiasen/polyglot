Polyglot
========

Polyglot is a localization helper for the Laravel framework.
It's an helper class but mostly it's a model you can extend.

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
  public static $polyglot = array('title', 'content');
}

// Get an automatically localized Article
$article = Article::find(4)
echo $article->title // This will print out the title in the current language
```
