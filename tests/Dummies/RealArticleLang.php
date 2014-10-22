<?php
namespace Polyglot\Dummies;

use Illuminate\Database\Eloquent\Model;

class RealArticleLang extends Model
{
	/**
	 * @type array
	 */
	protected $fillable = ['title', 'real_article_id', 'lang'];

	/**
	 * @type string
	 */
	protected $table = 'article_lang';

	/**
	 * @type bool
	 */
	public $timestamps = false;
}
