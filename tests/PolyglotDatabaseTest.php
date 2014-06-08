<?php
namespace Polyglot;

use Polyglot\Dummies\RealArticle;
use Polyglot\TestCases\DatabaseTestCase;

class PolyglotDatabaseTest extends DatabaseTestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->app['config'] = $this->mockConfig(['polyglot::fallback' => null]);
	}

	public function testUpdateTimestampsOnSave()
	{
		$article = $this->createArticle();

		$article = RealArticle::first();
		$start = $article->updated_at;

		sleep(1);

		$article->title = 'different';
		$article->lang  = 'fr';
		$article->save();

		$this->assertNotEquals($start, $article->updated_at);
	}

	public function testScopeWithLang()
	{
		// Test scope with lang
		$article = $this->createArticle();

		$article = RealArticle::withLang('fr')->where('id', $article->id)->first();
		$array = $article->toArray();
		$this->assertEquals($article->fr->title, "Start title");

		// empty
		$article = RealArticle::withLang()->where('id', $article->id)->first();
		$array = $article->toArray();
		$this->assertEquals($article->fr->title, "Start title");
	}

	public function testLocalizeHelper()
	{
		$article = $this->createArticle();

		$this->assertFalse($article->localize(array()));

		$article->localize(array(
			'title' => array(
				'fr' => 'fr title',
				'en' => 'en title'
			)
		));

		$this->assertEquals($article->fr->title, 'fr title');
	}

	public function testLangHelper()
	{
		$article = $this->createArticle();

		$fr = $article->lang();
		$this->isInstanceOf($fr, 'Polyglot\Dummies\RealArticleLang');

		$en = $article->lang('en');
		$this->isInstanceOf($en, 'Polyglot\Dummies\RealArticleLang');
	}

	public function testIssetHelper()
	{
		$article = $this->createArticle();

		$this->assertTrue(isset($article->fr));
		$this->assertTrue(isset($article->title));

		$this->assertEquals($article->fr->title, 'Start title');
	}

	protected function createArticle()
	{
		$article = new RealArticle;
		$article->name  = 'Start name';
		$article->title = 'Start title';
		$article->lang  = 'fr';
		$article->save();

		return $article;
	}
}
