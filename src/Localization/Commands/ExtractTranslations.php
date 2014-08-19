<?php
namespace Polyglot\Localization\Commands;

use Polyglot\Abstracts\AbstractCommand;
use Symfony\Component\Console\Input\InputOption;

class ExtractTranslations extends AbstractCommand
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'lang:extract';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Extract translations from views';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->call('cache:clear');
		$this->call('twig:clear');

		// Gather files
		$files = $this->laravel['polyglot.extractor']->getFiles();
		$this->comment('Found '.$files->count().' files');
		if ($this->option('verbose')) {
			foreach ($files as $file) {
				$this->line('-- '.$file->getPathname());
			}
		}

		// Concatenate files

		// Generate locale files
		$this->forLocales(function ($locale) {
			$translated = $this->laravel['polyglot.extractor']->generateLocale($locale, !$this->option('no-clear'));
			$this->line('Extracted translations to <info>'.$translated.'</info>');
		});

		// Compile
		if (!$this->option('no-compile')) {
			$this->call('lang:compile');
		}
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// OPTIONS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('no-clear', null, InputOption::VALUE_NONE, 'Clear previously generated files', null),
			array('no-compile', null, InputOption::VALUE_NONE, "Don't compile PO files to MO", null),
		);
	}
}
