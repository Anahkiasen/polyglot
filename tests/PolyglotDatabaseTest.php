<?php
namespace Polyglot;

use Polyglot\Polyglot;
use Illuminate\Database\Eloquent\Model;

class RealArticle extends Polyglot {

	protected $guarded = array('created_at', 'updated_at');

	protected $table = 'articles';

	protected $polyglot = array('title', 'body');

}


class PolyglotDatabaseTest extends TestCases\DatabaseTestCase {

	public function setUp()
	{
		parent::setUp();
	}

	public function testHasSimpleApi() {
		$article = new RealArticle(array(
			"name" => "Some name",
			"title" => "Some title",
			"lang" => "fr"
		));

		$this->assertTrue($article->save());

		$article = new RealArticle;
		$article->fill(array(
			"title" => "Some title",
			"lang" => "fr"
		));

		$saved = false;
		try {
			$saved = $article->save();
		} catch (\Illuminate\Database\QueryException $e) {

		}

		$this->assertFalse($saved);
	}

	public function testUpdateTImestampsOnSave() {
		$article = new RealArticle();
		$article->name = "test";
		$article->save();

		$article = RealArticle::first();
		$start = $article->updated_at;

		sleep(1);

		$article->name = "different";
		$article->save();

		$this->assertNotEquals($start.'', $article->updated_at.'');
	}
}
