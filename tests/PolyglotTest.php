<?php
class PolyglotTest extends PolyglotTests
{
	public function testCanGetTranslatedAttributes()
	{
		$article = new Article;

		$this->assertEquals('Name', $article->en->name);
		$this->assertEquals('Nom', $article->fr->name);
		$this->assertEquals('Nom', $article->name);
	}

	public function testPolyglotAttributesAreSet()
	{
		$article = new Article;

		$this->assertTrue(isset($article->name));
		$this->assertTrue(isset($article->agb_accepted));
	}
}
