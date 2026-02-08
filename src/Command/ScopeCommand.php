<?php
/**
 * PHP-Scoper Scope Command
 *
 * Runs PHP-Scoper to prefix all dependencies in the vendor directory
 * to avoid conflicts with plugins that may have the same dependencies.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Appfromlab\Bob\Composer\BatchCommands;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Composer\Command\BaseCommand;

/**
 * Execute PHP-Scoper on vendor folder
 */
class ScopeCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:scope' )
			->setDescription( 'Perform php-scoper on vendor folder.' );
	}

	/**
	 * Execute the command
	 *
	 * Runs PHP-Scoper to prefix vendor dependencies.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		// Get configuration.
		$config = Helper::getConfig();

		$commands = array(
			new Process(
				array(
					'php',
					$config['paths']['plugin_vendor_dir'] . 'bin/php-scoper',
					'add-prefix',
					'--config=' . $config['paths']['plugin_dir'] . '.scoper.inc.php',
				),
			),
		);

		$exit_code = BatchCommands::run( $this->getApplication(), $commands, $output );

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
