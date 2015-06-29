<?php

namespace Polyglot\Localization\Commands;

use Polyglot\Abstracts\AbstractCommand;

class CompileTranslations extends AbstractCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lang:compile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile translations';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $compiler = $this->laravel['polyglot.compiler'];
        $compiler->setCommand($this);

        $this->forLocales([$compiler, 'compileLocale']);
    }
}
