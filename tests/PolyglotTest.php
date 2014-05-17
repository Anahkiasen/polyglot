<?php
namespace Polyglot;

use Polyglot\Dummies\Article;
use Polyglot\TestCases\PolyglotTestCase;

class PolyglotTest extends PolyglotTestCase
{
	public function testCanGetTranslatedAttributes()
	{
		$article = new Article;

		$this->assertEquals('Name', $article->en->name);
		$this->assertEquals('Nom', $article->fr->name);
		$this->assertEquals('Nom', $article->de->name);
		$this->assertEquals('Nom', $article->name);
	}

	public function testPolyglotAttributesAreSet()
	{
		$article = new Article;

		$this->assertTrue(isset($article->name));
		$this->assertTrue(isset($article->agb_accepted));
	}
}
