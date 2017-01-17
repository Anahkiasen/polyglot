<?php

namespace Polyglot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class PolyglotObserver
{
    /**
     * Save separately the model attributes and polyglot ones.
     *
     * @param Model $model
     *
     * @return bool|null
     */
    public function saving(Model $model)
    {
        // Extract polyglot attributes
        $translated = $this->extractTranslatedAttributes($model);

        // If no localized attributes, continue
        if (empty($translated)) {
            return true;
        }

        // Save new model
        if (!$model->exists) {
            $model->save();
        }

        // Get the current lang and Lang model
        $lang = array_get($translated, 'lang', Lang::getLocale());
        $langModel = $model->$lang;
        $translated['lang'] = $lang;

        // If no Lang model or the fallback was returned, create a new one
        if (!$langModel || ($langModel->lang !== $lang)) {
            $langModel = $model->getLangClass();
            $langModel = new $langModel($translated);
            $model->translations()->save($langModel);
            $model->setRelation($lang, $langModel);
        }

        $langModel->fill($translated);

        // Save and update model timestamp
        if ($model->exists && $model->timestamps && $langModel->getDirty()) {
            $time = $model->freshTimestamp();
            $model->setUpdatedAt($time);
        }

        if ($model->save() && $langModel->save()) {
            return true;
        }
    }

    /**
     * @param Model $model
     *
     * @return array
     */
    protected function extractTranslatedAttributes(Model &$model)
    {
        $attributes = $model->getAttributes();
        $polyglot = $model->getPolyglotAttributes();

        $translated = [];
        foreach ($attributes as $key => $value) {
            if (in_array($key, $polyglot, true)) {
                $translated[$key] = $value;
                unset($model[$key]);
            }
        }

        return $translated;
    }
}
