<?php
/**
 * Build Command
 *
 * Orchestrates the plugin build process including autoload dumping, vendor prefixing,
 * code beautification, and dependency resolution.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Appfromlab\Bob\Composer\BatchCommands;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Execute complete plugin build process
 */
class BuildCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description for the Composer console.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:build' )
			->setDescription( 'Perform build process.' );
	}

	/**
	 * Execute the command
	 *
	 * Performs the complete plugin build process.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success, non-zero for failure).
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
