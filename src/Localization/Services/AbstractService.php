<?php
namespace Polyglot\Services;

use Illuminate\Console\Command;
use Illuminate\Container\Container;

abstract class AbstractService
{
	/**
	 * The Container
	 *
	 * @var Container
	 */
	protected $app;

	/**
	 * The command executing compilation
	 *
	 * @var Command
	 */
	protected $command;

	/**
	 * Build a new Compiler
	 *
	 * @param Container $app
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	/**
	 * Set the Command executing the compilation
	 *
	 * @param Command $command
	 */
	public function setCommand(Command $command)
	{
		$this->command = $command;
	}

	/**
	 * Sprintf and execute a command
	 *
	 * @param string $message
	 * @param string $parameters...
	 *
	 * @return void
	 */
	protected function execf()
	{
		$arguments = func_get_args();
		$message   = array_pull($arguments, 0);
		$command   = vsprintf($message, $arguments);

		return exec($command);
	}
}
