<?php
/**
 * Plugin Release Command
 *
 * Orchestrates the complete plugin release process including building,
 * bumping version, generating readme, and creating language files.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Composer\BatchCommands;
use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Execute complete release workflow
 */
class ReleaseCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:release' )
			->setDescription( 'Perform the release process which builds the code, bump version, generate readme.txt and make-pot.' )
			->addOption( 'version', null, InputOption::VALUE_REQUIRED, 'The new version number (e.g. 1.2.3). Must be higher than the current plugin version.' );
	}

	/**
	 * Execute the command
	 *
	 * Executes the complete release workflow.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success, non-zero for failure).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		$new_version = $input->getOption( 'version' );

		if ( ! empty( $new_version ) ) {

			// Validate format: must be a semantic version string.
			if ( ! preg_match( '/^\d+\.\d+\.\d+$/', $new_version ) ) {
				$output->writeln( '<error>ERROR: Invalid version format. Expected X.Y.Z (e.g. 1.2.3).</error>' );
				return 1;
			}

			$config         = Helper::getConfig();
			$plugin_headers = Helper::getPluginHeaders( $config['paths']['plugin_file'] );
			$current_version = $plugin_headers['Version'];

			// Ensure current version could be read.
			if ( empty( $current_version ) ) {
				$output->writeln( '<error>ERROR: Could not determine current plugin version from plugin header.</error>' );
				return 1;
			}

			// Ensure new version is strictly greater than current version.
			if ( version_compare( $new_version, $current_version, '<=' ) ) {
				$output->writeln( "<error>ERROR: Version {$new_version} is not higher than the current plugin version {$current_version}.</error>" );
				return 1;
			}

			// Replace the Version header in the plugin file.
			$plugin_file      = $config['paths']['plugin_file'];
			$existing_content = file_get_contents( $plugin_file );

			if ( false === $existing_content ) {
				$output->writeln( "<error>ERROR: Could not read plugin file: {$plugin_file}</error>" );
				return 1;
			}

			$updated_content  = preg_replace(
				'/^([ \t\/*#@]*Version:[ \t]*)[\d.]+/mi',
				'${1}' . $new_version,
				$existing_content,
				-1,
				$count
			);

			if ( 0 === $count ) {
				$output->writeln( "<error>ERROR: Could not find Version header in plugin file: {$plugin_file}</error>" );
				return 1;
			}

			if ( false === file_put_contents( $plugin_file, $updated_content ) ) {
				$output->writeln( "<error>ERROR: Failed to write updated version to plugin file: {$plugin_file}</error>" );
				return 1;
			}

			$output->writeln( "Plugin version updated to: {$new_version}" );
		}

		$commands = array(
			new ArrayInput( array( 'command' => 'afl:bob:build' ) ),
			new ArrayInput( array( 'command' => 'afl:bob:bump-version' ) ),
			new ArrayInput( array( 'command' => 'afl:bob:readme-generator' ) ),
			new ArrayInput( array( 'command' => 'afl:bob:make-pot' ) ),
		);

		$exit_code = BatchCommands::run( $this->getApplication(), $commands, $output );

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
