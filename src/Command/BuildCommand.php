<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Appfromlab\Bob\Composer\BatchCommands;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

class BuildCommand extends BaseCommand {

	protected function configure(): void {
		$this->setName( 'afl:build' )
			->setDescription( 'Perform build process.' );
	}

	/**
	 * Execute the command
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '<info>------ START ' . __CLASS__ . '</info>' );

		// Get configuration.
		$config = Helper::getConfig();

		$commands = array(
			array( 'composer dump-autoload --no-dev' ),
			new DeleteVendorPrefixedCommand(),
			new ScopeCommand(),
			new PhpcbfVendorPrefixedCommand(),
			array( 'composer dump-autoload' ),
		);

		$exit_code = BatchCommands::run( $commands, $input, $output );

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
