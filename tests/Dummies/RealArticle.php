<?php
namespace Polyglot\Dummies;

use Polyglot\Polyglot;

class RealArticle extends Polyglot
{
	protected $guarded = array('created_at', 'updated_at');

	protected $table = 'articles';

	protected $polyglot = array('title', 'body');
}
