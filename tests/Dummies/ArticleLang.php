<?php
namespace Polyglot\Dummies;

use Mockery;
use Illuminate\Database\Eloquent\Model;

class ArticleLang extends Model
{
	public function whereLang($lang)
	{
		$relation = Mockery::mock('Illuminate\Database\Eloquent\Relations\Relation');
		$relation->shouldReceive('getResults')->andReturnUsing(function () use ($relation) {
			return $relation;
		});

		$relation->name = $lang == 'en' ? 'Name' : 'Nom';

		return $relation;
	}
}
