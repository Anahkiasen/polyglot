<?php

namespace Polyglot\Dummies;

use Illuminate\Database\Eloquent\Model;
use Polyglot\Polyglot;

class RealArticle extends Model
{
    use Polyglot;
    
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
