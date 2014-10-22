<?php
namespace Polyglot\Dummies;

use Polyglot\Polyglot;

class RealArticle extends Polyglot
{
	/**
	 * @type array
	 */
	protected $fillable = ['name'];

	/**
	 * @type string
	 */
	protected $table = 'articles';

	/**
	 * @type array
	 */
	protected $polyglot = ['title', 'body'];
}
