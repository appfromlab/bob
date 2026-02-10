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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Composer\Command\BaseCommand;

/**
 * Initialize plugin for first-time use
 */
class NewPluginCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:new-plugin' )
			->setDescription( 'Prepare project files for new plugin.' );
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

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		$commands = array(
			new ArrayInput( array( 'command' => 'afl:bob:plugin-renamer-copy-config' ) ),
			new ArrayInput( array( 'command' => 'afl:bob:delete-composer-lock' ) ),
			new ArrayInput( array( 'command' => 'afl:bob:install-wpcli' ) ),
			new ArrayInput( array( 'command' => 'afl:bob:require-dev-global' ) ),
		);

		$exit_code = BatchCommands::run( $this->getApplication(), $commands, $output );

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
