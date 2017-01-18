<?php

namespace Polyglot\Dummies;

use Illuminate\Database\Eloquent\Model;
use Polyglot\Polyglot;

class Article extends Model
{
    use Polyglot;
    
    /**
     * The attributes to translate.
     *
     * @var array
     */
    protected $polyglot = ['name'];

    /**
     * Dummy HasOne.
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
