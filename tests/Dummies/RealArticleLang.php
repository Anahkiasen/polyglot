<?php

namespace Polyglot\Dummies;

use Illuminate\Database\Eloquent\Model;

class RealArticleLang extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['title', 'real_article_id', 'lang'];

    /**
     * @var string
     */
    protected $table = 'article_lang';

    /**
     * @var bool
     */
    public $timestamps = false;
}
