<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Appfromlab\Bob\Command\DeleteComposerLockCommand;
use Appfromlab\Bob\Command\PluginRenamerCopyConfigCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

class FirstTimeCommand extends BaseCommand {

	protected function configure(): void {
		$this->setName( 'afl:first-time' )
			->setDescription( 'Copy plugin-renamer-config.php and delete composer.lock file.' );
	}

	/**
	 * Execute the command
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '<info>------ START ' . __CLASS__ . '</info>' );

		$commands = array(
			new PluginRenamerCopyConfigCommand(),
			new DeleteComposerLockCommand(),
		);

		foreach ( $commands as $command ) {

			$output->writeln( '' );

			$return_code = $command->execute( $input, $output );

			if ( 0 !== $return_code ) {
				$output->writeln( '<error>ERROR: Command failed - ' . $command->getName() . '</error>' );
				return 1;
			}
		}

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return 0;
	}
}
