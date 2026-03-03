<?php
/**
 * Github Setup Environment Variables Command
 *
 * Sets up GitHub Actions environment variables for the plugin including
 * folder name, version number, and distribution directory path.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Setup GitHub environment variables for use in GitHub Actions
 */
class GithubSetupEnvCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description for the Composer console.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:github-setup-env' )
			->setDescription( 'Setup GitHub Actions environment variables: AFL_PLUGIN_FOLDER_NAME, AFL_PLUGIN_VERSION_NUMBER, AFL_PLUGIN_DIST_DIR.' );
	}

	/**
	 * Execute the command
	 *
	 * Writes plugin folder name, version number, and distribution directory path
	 * to the GitHub Actions environment file ($GITHUB_ENV).
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success, non-zero for failure).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		$exit_code = 0;

		try {
			// Get configuration.
			$config         = Helper::getConfig();
			$plugin_headers = Helper::getPluginHeaders( $config['paths']['plugin_file'] );

			$plugin_folder_name    = $config['plugin_folder_name'];
			$plugin_version_number = $plugin_headers['Version'];
			$plugin_dist_dir       = $config['paths']['plugin_distribution_dir'];

			$github_env = getenv( 'GITHUB_ENV' );

			if ( empty( $github_env ) ) {
				throw new \RuntimeException( 'GITHUB_ENV environment variable is not set. This command must be run inside a GitHub Actions workflow.' );
			}

			$env_entries = array(
				'AFL_PLUGIN_FOLDER_NAME'    => $plugin_folder_name,
				'AFL_PLUGIN_VERSION_NUMBER' => $plugin_version_number,
				'AFL_PLUGIN_DIST_DIR'       => $plugin_dist_dir,
			);

			$file_handle = fopen( $github_env, 'a' );

			if ( false === $file_handle ) {
				throw new \RuntimeException( 'Failed to open GITHUB_ENV file: ' . $github_env );
			}

			try {
				foreach ( $env_entries as $name => $value ) {
					fwrite( $file_handle, $name . '=' . $value . "\n" );
					$output->writeln( $name . '=' . $value );
				}
			} finally {
				fclose( $file_handle );
			}

			$output->writeln( '' );
			$output->writeln( '<info>GitHub environment variables written to:</info> ' . $github_env );
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
