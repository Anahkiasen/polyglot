<?php

namespace Polyglot\Localization\Services;

/**
 * Compiles existing PO files to MO.
 */
class Compiler extends AbstractService
{
    /**
     * Generate the files for a locale.
     *
     * @param string $locale
     * @param array  $files
     */
    public function compileLocale($locale)
    {
        // Get output directory and file
        $directory = $this->app['polyglot.translator']->getLocaleFolder($locale);
        $files = glob($directory.'/*.po');

        // Recompile MO files
        foreach ($files as $file) {
            $compiled = str_replace('po', 'mo', $file);
            $this->execf('msgfmt %s -o %s', $file, $compiled);
        }

        // Print success
        if ($this->command) {
            $this->command->line('Compiled <info>'.$compiled.'</info>');
        }
    }
}
