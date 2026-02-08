<?php
/**
 * Install Binary Tools Command
 *
 * Downloads and verifies binary tools required for development,
 * including the WordPress CLI (WP-CLI) with GPG signature verification.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Appfromlab\Bob\Composer\BatchCommands;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Composer\Command\BaseCommand;

/**
 * Download and install required binary tools
 */
class InstallBinCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:install-bin' )
			->setDescription( 'Install required bin files for local development.' );
	}

	/**
	 * Execute the command
	 *
	 * Downloads and verifies binary tools for development.
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

		// Create .bin directory if it does not exist.
		if ( ! is_dir( $config['paths']['plugin_bin_dir'] ) ) {
			mkdir( $config['paths']['plugin_bin_dir'], 0774, true );
		}

		$commands = array(
			new Process( array( 'curl', '-L', 'https://github.com/wp-cli/wp-cli/releases/download/v2.12.0/wp-cli-2.12.0.phar', '-o', $config['paths']['plugin_bin_dir'] . 'wp-cli.phar' ) ),
			new Process( array( 'curl', '-L', 'https://github.com/wp-cli/wp-cli/releases/download/v2.12.0/wp-cli-2.12.0.phar.asc', '-o', $config['paths']['plugin_bin_dir'] . 'wp-cli.phar.asc' ) ),
			new Process( array( 'curl', '-L', 'https://raw.githubusercontent.com/wp-cli/builds/gh-pages/wp-cli.pgp', '-o', $config['paths']['plugin_bin_dir'] . 'wp-cli.pgp' ) ),
			new Process( array( 'gpg', '--import', $config['paths']['plugin_bin_dir'] . 'wp-cli.pgp' ) ),
			new Process( array( 'gpg', '--verify', $config['paths']['plugin_bin_dir'] . 'wp-cli.phar.asc', $config['paths']['plugin_bin_dir'] . 'wp-cli.phar' ) ),
		);

		$exit_code = BatchCommands::run( $this->getApplication(), $commands, $output );

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
