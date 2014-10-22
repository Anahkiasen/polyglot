<?php
namespace Polyglot\Dummies;

use Polyglot\Polyglot;

class Article extends Polyglot
{
	/**
	 * The attributes to translate
	 *
	 * @var array
	 */
	protected $polyglot = array('name');

	/**
	 * Dummy HasOne
	 *
	 * @param string $related
	 *
	 * @return object
	 */
	public function hasOne($related, $foreignKey = null, $localKey = null)
	{
		return new $related();
	}

	public function getAgbAcceptedAttribute()
	{
		return 1;
	}
}
