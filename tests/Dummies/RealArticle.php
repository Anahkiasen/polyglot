<?php

namespace Polyglot\Dummies;

use Polyglot\Polyglot;

class RealArticle extends Polyglot
{
    /**
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * @var string
     */
    protected $table = 'articles';

    /**
     * @var array
     */
    protected $polyglot = ['title', 'body'];
}
