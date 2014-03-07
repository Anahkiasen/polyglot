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

	public function setUp()
	{
		parent::setUp();
		$this->capsule->getConnection()->listen(function($sql, $b) {
			print_r($sql.json_encode($b)."\n");
		});
	}

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
		$article->save();

		$this->assertNotEquals($start, $article->updated_at);


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

		// lang
		$article->lang();
		$article->lang('fr');

		// isset
		isset($article->fr);
		isset($article->title);

		$article->title;

		// localize
		$this->assertFalse($article->localize(array()));

		$article->localize(array(
			'title' => array(
				'fr' => 'fr title',
				'en' => 'en title'
			)
		));
	}

	// public function testScopeWithLang()
	// {
	// }

}
