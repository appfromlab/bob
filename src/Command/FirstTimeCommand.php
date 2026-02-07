<?php
/**
 * First Time Command
 *
 * Performs initial setup for a new plugin installation by copying configuration
 * file and deleting the composer.lock file.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Composer\BatchCommands;
use Appfromlab\Bob\Command\DeleteComposerLockCommand;
use Appfromlab\Bob\Command\PluginRenamerCopyConfigCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Initialize plugin for first-time use
 */
class FirstTimeCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:first-time' )
			->setDescription( 'Copy plugin-renamer-config.php and delete composer.lock file.' );
	}

	/**
	 * Execute the command
	 *
	 * Performs first-time setup for a new plugin installation.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success, non-zero for failure).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '<info>------ START ' . __CLASS__ . '</info>' );

		$commands = array(
			new PluginRenamerCopyConfigCommand(),
			new DeleteComposerLockCommand(),
		);

		$exit_code = BatchCommands::run( $commands, $input, $output );

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
