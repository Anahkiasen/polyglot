<?php
namespace Polyglot\Dummies;

use Illuminate\Database\Eloquent\Model;

class RealArticleLang extends Model
{
	protected $guarded = array('created_at', 'updated_at');

	protected $table = 'article_lang';

	public $timestamps = false;

}
