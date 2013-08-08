<?php
use Polyglot\Polyglot;

class Article extends Polyglot
{
	protected $polyglot = array('name');

	/**
	 * Dummy HasOne
	 *
	 * @param  string  $related
	 *
	 * @return object
	 */
	public function hasOne($related, $foreignKey = NULL)
	{
		return new $related;
	}
}