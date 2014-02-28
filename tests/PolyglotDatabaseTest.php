<?php
namespace Polyglot;

use Polyglot\Polyglot;
use PHPUnit_Framework_TestCase;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Lang;
use Polyglot\PolyglotServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Capsule\Manager as Capsule;

class RealArticle extends Polyglot {

	protected $table = 'articles';

}

class PolyglotDatabaseTest extends TestCases\DatabaseTestCase {

	public function setUp()
	{
		parent::setUp();
		$article = new RealArticle();
		$article->name = "test";
		$article->save();
	}

	public function testUpdateTImestampsOnSave() {
		$article = RealArticle::first();
		$start = $article->updated_at;

		sleep(1);

		$article->name = "different";
		$article->save();

		$this->assertNotEquals($start.'', $article->updated_at.'');
	}

}
