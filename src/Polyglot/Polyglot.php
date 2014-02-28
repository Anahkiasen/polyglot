<?php
namespace Polyglot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang as LangFacade;

/**
 * Abstract model that eases the localization of model
 */
abstract class Polyglot extends Model
{
	/**
	 * An array of polyglot attributes
	 *
	 * @var array
	 */
	protected $polyglot = array();

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		static::saving(function ($model) {

			// Cancel if not localized
			$hasPolyglotAttributes = $model->getPolyglotAttributes();
			$hasPolyglotAttributes = empty($hasPolyglotAttributes);
			if ($hasPolyglotAttributes) {
				return true;
			}

			// Get the model's attributes
			$attributes = $model->getAttributes();
			$translated = array();

			// Extract polyglot attributes
			foreach ($attributes as $key => $value) {
				if (in_array($key, $model->getPolyglotAttributes())) {
					unset($attributes[$key]);
					$translated[$key] = $value;
				}
			}

			// If no localized attributes, continue
			if (empty($translated)) {
				return true;
			}

			// Get the current lang and Lang model
			$lang      = array_get($translated, 'lang', LangFacade::getLocale());
			$langModel = $model->$lang;
			$translated['lang'] = $lang;

			// Save original model
			$model = $model->newInstance($attributes, $model->exists);

			if ( ! $model->exists) {
				$model->save();
			}

			// If no Lang model, create one
			if (!$langModel) {
				$langModel = $model->getLangClass();
				$langModel = new $langModel($translated);
				$model->translations()->save($langModel);
			}

			$langModel->fill($translated);

			if ($model->exists && $model->timestamps && $langModel->getDirty()) {
				$time = $model->freshTimestamp();
				$model->setUpdatedAt($time);
			}

			$model->save();
			$langModel->save();

			return false;
		});
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////// RELATIONSHIPS /////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Reroutes functions to the language in use
	 *
	 * @param  string  $lang A language to use
	 *
	 * @return HasOne
	 */
	public function lang($lang = null)
	{
		if (!$lang) {
			$lang = LangFacade::getLocale();
		}

		return $this->$lang();
	}

	/**
	 * Get all translations
	 *
	 * @return Collection
	 */
	public function translations()
	{
		return $this->hasMany($this->getLangClass());
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// ATTRIBUTES //////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the polyglot attributes
	 *
	 * @return array
	 */
	public function getPolyglotAttributes()
	{
		return array_merge($this->polyglot, array('lang'));
	}

	/**
	 * Handle polyglot dynamic method calls for locale relations.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		// If the model supports the locale, load it
		if (in_array($method, $this->getLocales())) {
			return $this->hasOne($this->getLangClass())->whereLang($method);
		}

		return parent::__call($method, $parameters);
	}

	/**
	 * Checks if a field isset while taking into account localized attributes
	 *
	 * @param string $key The key
	 *
	 * @return boolean
	 */
	public function __isset($key)
	{
		if ($this->polyglot) {
			if (in_array($key, $this->getPolyglotAttributes())) {
				return true;
			}
		}

		return parent::__isset($key);
	}

	/**
	 * Get a localized attribute
	 *
	 * @param string $key The attribute
	 *
	 * @return mixed
	 */
	public function __get($key)
	{
		// If the relation has been loaded already, return it
		if (array_key_exists($key, $this->relations)) {
			return $this->relations[$key];
		}

		// If the model supports the locale, load and return it
		if (in_array($key, $this->getLocales())) {
			$relation = $this->hasOne($this->getLangClass())->whereLang($key);
			return $this->relations[$key] = $relation->getResults();
		}

		// If the attribute is set to be automatically localized
		if ($this->polyglot) {
			if (in_array($key, $this->polyglot)) {
				$lang = LangFacade::getLocale();

				return $this->$lang ? $this->$lang->$key : null;
			}
		}

		return parent::__get($key);
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////// PUBLIC HELPERS /////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Localize a model with an array of lang arrays
	 *
	 * @param  array $localization An array in the form [field][lang][value]
	 */
	public function localize($localization)
	{
		if (!$localization) {
			return false;
		}

		$langs = array_keys($localization[key($localization)]);

		// Build lang arrays
		foreach ($localization as $key => $value) {
			foreach ($langs as $lang) {
				${$lang}[$key] = array_get($value, $lang);
				${$lang}['lang'] = $lang;
			}
		}

		// Update
		$class = $this->getLangClass();
		foreach ($langs as $lang) {
			if (!is_object($this->$lang)) {
				$class = new $class($$lang);
				$this->$lang()->insert($class);
			} else {
				$this->$lang->fill($$lang);
				$this->$lang->save();
			}
		}
	}

	/**
	 * Localize a "with" method
	 *
	 * @param Query  $query
	 * @param string $relations,...
	 *
	 * @return Query
	 */
	public function scopeWithLang()
	{
		$relations = func_get_args();
		$query     = array_shift($relations);

		if (empty($relations)) {
			$relations = array(LangFacade::getLocale());
		}

		return $query->with($relations);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// HELPERS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the Lang class corresponding to the current model
	 *
	 * @return string
	 */
	public function getLangClass()
	{
		$pattern = Config::get('polyglot::model_pattern');

		// Get class name
		$model = get_called_class();
		$model = class_basename($model);

		return str_replace('{model}', $model, $pattern);
	}

	/**
	 * Get an array of supported locales
	 *
	 * @return array
	 */
	protected function getLocales()
	{
		return Config::get('polyglot::locales');
	}
}
