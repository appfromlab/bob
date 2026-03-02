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
			)
			->addOption(
				'dist',
				null,
				InputOption::VALUE_NONE,
				'Prepare the plugin folder for distribution by copying necessary files to a dist/ directory after building.'
			)
			->addOption(
				'verify-version',
				null,
				InputOption::VALUE_OPTIONAL,
				'Verify the plugin version before building.'
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
	 * @throws \RuntimeException If a runtime exception occurs during the build process.
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		// Get configuration.
		$config = Helper::getConfig();

		try {
			if ( $input->getOption( 'verify-version' ) ) {

				if ( ! Helper::verify_plugin_version( $input->getOption( 'verify-version' ) ) ) {
					$output->writeln( 'Verify Plugin Version: ' . $input->getOption( 'verify-version' ) );
					$output->writeln( 'Current Plugin Version: ' . Helper::get_plugin_version() );
					throw new \RuntimeException( 'Failed plugin version verification.' );
				}
			}

			$commands = array(
				new ArrayInput( array( 'command' => 'afl:bob:scope' ) ),
			);

			if ( $input->getOption( 'dist' ) ) {
				$commands[] = new ArrayInput( array( 'command' => 'afl:bob:dist-prepare' ) );
			} elseif ( $input->getOption( 'zip' ) ) {
				$commands[] = new ArrayInput( array( 'command' => 'afl:bob:zip-plugin' ) );
			}

			$exit_code = BatchCommands::run( $this->getApplication(), $commands, $output );
		} catch ( \RuntimeException $th ) {
			$output->writeln( '<error>' . $th->getMessage() . '</error>' );
			$exit_code = 1;
		}

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
