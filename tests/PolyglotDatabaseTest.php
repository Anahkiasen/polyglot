<?php
namespace Polyglot;

use Polyglot\Polyglot;
use Illuminate\Database\Eloquent\Model;

class RealArticle extends Polyglot {

	protected $guarded = array('created_at', 'updated_at');

	protected $table = 'articles';

	protected $polyglot = array('title', 'body');

}

class PolyglotDatabaseTest extends TestCases\DatabaseTestCase {

	public function testUpdateTImestampsOnSave()
	{
		$a = new RealArticle();
		$a->name = 'Start name';
		$a->title = 'Start title';
		$a->lang = 'fr';
		$a->save();

		$article = RealArticle::first();
		$start = $article->updated_at;

		sleep(1);

		$article->title = "different";
		$article->lang = 'fr';
		$article->save();

		$this->assertNotEquals($start, $article->updated_at);
	}

	public function testScopeWithLang()
	{
		// Test scope with lang
		$a = new RealArticle();
		$a->name = 'Start name';
		$a->title = 'Start title';
		$a->lang = 'fr';
		$a->save();

		$article = RealArticle::withLang('fr')->where('id', $a->id)->first();
		$array = $article->toArray();
		$this->assertEquals($article->fr->title, "Start title");

		// empty
		$article = RealArticle::withLang()->where('id', $a->id)->first();
		$array = $article->toArray();
		$this->assertEquals($article->fr->title, "Start title");
	}

	public function testLocalizeHelper()
	{
		$article = new RealArticle();
		$article->name = 'Start name';
		$article->title = 'Start title';
		$article->lang = 'fr';
		$article->save();

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
		$article = new RealArticle();
		$article->name = 'Start name';
		$article->title = 'Start title';
		$article->lang = 'fr';
		$article->save();

		$fr = $article->lang();

		$this->isInstanceOf($fr, 'Polyglot\Dummies\RealArticleLang');

		$en = $article->lang('en');

		$this->isInstanceOf($en, 'Polyglot\Dummies\RealArticleLang');
	}

	public function testIssetHelper()
	{
		$article = new RealArticle();
		$article->name = 'name';
		$article->title = 'title';
		$article->lang = 'fr';
		$article->save();

		$this->assertTrue(isset($article->fr));
		$this->assertTrue(isset($article->title));

		$this->assertEquals($article->fr->title, 'title');
	}

}
