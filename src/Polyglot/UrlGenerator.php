<?php
namespace Polyglot;

use Illuminate\Routing\UrlGenerator as IlluminateUrlGenerator;

/**
 * An UrlGenerator with localization capacities
 */
class UrlGenerator extends IlluminateUrlGenerator
{
	/**
	 * Get the locale in an URL
	 *
	 * @return string
	 */
	public function locale()
	{
		return $this->request->segment(1);
	}

	/**
	 * Generate a absolute URL to the given language
	 *
	 * @param  string  $language
	 * @param  mixed   $parameters
	 * @param  bool    $secure
	 * @return string
	 */
	public function language($language, $parameters = array(), $secure = null)
	{
		return $this->to($language, $parameters, $secure);
	}

	/**
	 * Generate a absolute URL to the same page in another language
	 *
	 * @param  string  $language
	 * @param  mixed   $parameters
	 * @param  bool    $secure
	 * @return string
	 */
	public function switchLanguage($language, $parameters = array(), $secure = null)
	{
		$current = $this->request->getPathInfo();
		$current = preg_replace('#^/([a-z]{2})?$#', null, $current);
		$current = preg_replace('#^/?([a-z]{2}/)?#', null, $current);

		return $this->to($language.'/'.$current, $parameters, $secure);
	}
}
