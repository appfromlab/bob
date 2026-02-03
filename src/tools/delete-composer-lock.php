<?php
namespace Appfromlab\Bob\Tools;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete composer.lock file
 */
class DeleteComposerLock extends Command {

	protected function configure(): void {
		$this->setName( 'Delete composer.lock file' )
			->setDescription( 'Delete composer.lock file in the WordPress plugin folder.' );
	}

	/**
	 * Run the delete composer lock process
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$config = Helper::getConfig();

		if ( ! Helper::safeToDelete( $config['paths']['composer.lock'], 'composer.lock', $config['paths']['plugin_dir'] ) ) {
			$output->writeln( '<warning>WARNING: Failed to delete composer.lock</warning>' );
		}

		return 0;
	}
}
