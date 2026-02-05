<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Delete composer.lock file
 */
class DeleteComposerLockCommand extends BaseCommand {

	protected function configure(): void {
		$this->setName( 'afl:delete-composer-lock' )
			->setDescription( 'Delete composer.lock file in the WordPress plugin folder.' );
	}

	/**
	 * Execute the command
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$config = Helper::getConfig();

		if ( ! Helper::safeToDelete( $config['paths']['plugin_composer_lock_file'], 'composer.lock', $config['paths']['plugin_dir'] ) ) {
			$output->writeln( '<warning>WARNING: Failed to delete composer.lock</warning>' );
		}

		return 0;
	}
}
