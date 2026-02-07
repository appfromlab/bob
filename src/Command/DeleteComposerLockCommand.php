<?php
/**
 * Delete Composer Lock File Command
 *
 * Removes the composer.lock file from the WordPress plugin folder.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Delete composer.lock file
 */
class DeleteComposerLockCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:delete-composer-lock' )
			->setDescription( 'Delete composer.lock file in the WordPress plugin folder.' );
	}

	/**
	 * Execute the command
	 *
	 * Deletes the composer.lock file from the plugin directory.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '<info>------ START ' . __CLASS__ . '</info>' );

		$config = Helper::getConfig();

		if ( ! Helper::safeToDelete( $config['paths']['plugin_composer_lock_file'], 'composer.lock', $config['paths']['plugin_dir'] ) ) {
			$output->writeln( '<warning>WARNING: Failed to delete composer.lock</warning>' );
		}

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return 0;
	}
}
