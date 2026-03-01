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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
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
		$this->setName( 'afl:bob:build' )
			->setDescription( 'Generate optimized composer autoloader files and perform PHP-Scoper on vendor folder.' )
			->addOption(
				'zip',
				null,
				InputOption::VALUE_NONE,
				'Also create a zip archive of the plugin for distribution after building.'
			);
	}

	/**
	 * Execute the command
	 *
	 * Performs the complete plugin build process including autoload dumping, vendor prefixing,
	 * code beautification, and dependency resolution.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success, non-zero for failure).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		// Get configuration.
		$config = Helper::getConfig();

		$commands = array(
			new ArrayInput( array( 'command' => 'afl:bob:scope' ) ),
		);

		if ( $input->getOption( 'zip' ) ) {
			$commands[] = new ArrayInput( array( 'command' => 'afl:bob:zip-plugin' ) );
		}

		$exit_code = BatchCommands::run( $this->getApplication(), $commands, $output );

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
