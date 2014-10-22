<?php
namespace Polyglot;

use Illuminate\Support\Facades\Lang;

class PolyglotObserver
{
	/**
	 * Save separately the model attributes and polyglot ones
	 *
	 * @param Polyglot $model
	 *
	 * @return boolean|null
	 */
	public function saving(Polyglot $model)
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
		$lang               = array_get($translated, 'lang', Lang::getLocale());
		$langModel          = $model->$lang;
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
	 * @param Polyglot $model
	 *
	 * @return array
	 */
	protected function extractTranslatedAttributes(Polyglot &$model)
	{
		$attributes = $model->getAttributes();
		$polyglot   = $model->getPolyglotAttributes();

		$translated = array();
		foreach ($attributes as $key => $value) {
			if (in_array($key, $polyglot)) {
				$translated[$key] = $value;
				unset($model[$key]);
			}
		}

		return $translated;
	}
}
