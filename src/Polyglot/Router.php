<?php
namespace Polyglot;

use Closure;
use Illuminate\Routing\Router as IlluminateRouter;

/**
 * A Router with localization capacities
 */
class Router extends IlluminateRouter
{
    ////////////////////////////////////////////////////////////////////
    //////////////////////////// ROUTE GROUPS //////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Create a localized route group
     *
     * @param array   $group
     * @param Closure $callback
     *
     * @return Closure
     */
    public function groupLocale($group, $callback = null)
    {
        if ($group instanceof Closure) {
            return $this->group($this->getRoutesPrefix(), $group);
        }

        return $this->group($this->getRoutesPrefix($group), $callback);
    }

    /**
     * Get the correct route prefix to use
     *
     * @return array
     */
    public function getRoutesPrefix($group = array())
    {
        // Get locale
        $locale = $this->container['url']->locale();

        // Cancel if invalid locale in URL
        if (!$this->container['translator']->valid($locale)) {
            return $group;
        }

        // Set locale on translator
        $this->container['translator']->setLocale($locale);

        // Return group untouched if default
        if ($locale == $this->container['config']->get('polyglot::default')) {
            return $group;
        }

        // Merge prefixes if necessary
        if (isset($group['prefix'])) {
            $locale = array($locale, $group['prefix']);
        }

        return array_merge($group, array('prefix' => $locale));
    }
}
