<?php
namespace Polyglot\Abstracts;

use Illuminate\Console\Command;

abstract class AbstractCommand extends Command
{
	/**
	 * Execute something for all locales
	 *
	 * @param Callable $closure
	 *
	 * @return void
	 */
	protected function forLocales(Callable $closure)
	{
		$locales = $this->laravel['polyglot.translator']->getAvailable();
		foreach ($locales as $locale) {
			$closure($locale);
		}
	}
}
