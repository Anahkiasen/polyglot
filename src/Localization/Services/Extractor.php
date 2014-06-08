<?php
namespace Polyglot\Localization\Services;

use Polyglot\Localization\Exceptions\ExtractionException;

/**
 * Extract translations form Twig files to PO files
 */
class Extractor extends AbstractService
{
	/**
	 * Get the views translations are in
	 *
	 * @return array
	 */
	public function getViews()
	{
		// Crawl files
		$files = app_path('views');
		$files = glob($files.'/{*/*,*/*/*,*}.twig', GLOB_BRACE);

		return $files;
	}

	/**
	 * Get the path to a locale's PO file
	 *
	 * @param string $locale
	 *
	 * @return string
	 */
	public function getLocaleFile($locale)
	{
		$directory  = $this->app['polyglot.translator']->getLocaleFolder($locale);
		$translated = $directory.'/' .$this->app['polyglot.translator']->getDomain(). '.po';

		return $translated;
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// EXTRACTION ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Generate the files for a locale
	 *
	 * @param string  $locale
	 * @param boolean $clean
	 *
	 * @return string
	 */
	public function generateLocale($locale, $clean = true)
	{
		$directory  = $this->app['polyglot.translator']->getLocaleFolder($locale);
		$translated = $this->getLocaleFile($locale);

		// Clean previous files
		if ($clean) {
			$this->app['files']->makeDirectory($directory, 0755, true, true);
		}

		// Run command
		$this->extract($directory);

		// Set headers on files
		$this->setHeaders($translated, $locale);

		return $translated;
	}

	/**
	 * Set headers in a PO file
	 *
	 * @param string $file
	 */
	public function setHeaders($file, $locale)
	{
		// Replace headers in file
		$contents = $this->app['files']->get($file);
		$contents = strtr($contents, array(
			'charset=CHARSET'                     => 'charset='.$this->app['polyglot.translator']->getEncoding(),
			'Language: '                          => 'Language: ' .$locale,
			'Language-Team: LANGUAGE <LL@li.org>' => 'Language-Team: Madewithlove <maxime@madewithlove.be>',
		));

		// Save content
		$this->app['files']->put($file, $contents);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////// TWIG EXTRACTOR ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the TwigExtractor instance
	 *
	 * @param array $directory
	 *
	 * @return TwigExtractor
	 */
	protected function extract($directory)
	{
		// Build arguments
		$domain    = $this->app['polyglot.translator']->getDomain();
		$arguments = sprintf('--sort-output --default-domain="%s" --language="PHP" --no-location --package-name="%s" --from-code=UTF-8 --force-po -p %s', $domain, ucfirst($domain), $directory);
		$arguments = explode(' ', $arguments);

		// Create temporary folder (bug fix for poEdit in Unix-like OS)
		foreach ($arguments as $arg) {
			if (strpos($arg, "/var/folders") !== false) {
				if (file_exists(dirname($arg)) === false) {
					mkdir(dirname($arg), 0777, true);
				}
			}
		}

		// Build cached templates and add them to arguments
		foreach ($this->getViews() as $file) {
			$this->app['twig']->loadTemplate($file);
			$arguments[] = '"' .$this->app['twig']->getCacheFilename($file). '"';
		}

		return $this->runGettext($arguments);
	}

	/**
	 * Run gettext with the specified arguments
	 *
	 * @param array $arguments
	 *
	 * @return integer
	 */
	protected function runGettext($arguments)
	{
		// Build command
    $command  = 'xgettext';
    $command .= ' '.join(' ', $arguments);

    $status = 0;
    $output = system($command, $status);
    if ($status !== 0) {
      throw new ExtractionException(sprintf(
        'Gettext command "%s" failed with error code %s and output: %s',
        $command,
        $status,
        $output
      ));
    }

    return $output;
	}
}
