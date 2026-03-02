<?php
/**
 * Prepare Distribution Command
 *
 * Prepares the plugin folder for distribution by copying necessary files to a dist/ directory.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Prepare Distribution Command
 *
 * Prepares the plugin folder for distribution by copying necessary files to a dist/ directory.
 */
class DistPrepareCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description for the Composer console.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:dist-prepare' )
			->setDescription( 'Prepare the plugin folder for distribution by copying necessary files to a dist/ directory.' );
	}

	/**
	 * Execute the command
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

		// Get configuration.
		$config         = Helper::getConfig();
		$plugin_headers = Helper::getPluginHeaders( $config['paths']['plugin_file'] );

		$source_path      = $config['paths']['plugin_dir'];
		$destination_path = $config['paths']['plugin_distribution_dir'];

		$plugin_dir_name = $config['plugin_folder_name'];
		$plugin_zip_name = $config['plugin_folder_name'] . '-' . $plugin_headers['Version'] . '.zip';
		$plugin_zip_path = dirname( $config['paths']['plugin_distribution_dir'] ) . DIRECTORY_SEPARATOR . $plugin_zip_name;

		try {

			// Delete zip file if it already exists to avoid confusion with old zip files.
			if ( file_exists( $plugin_zip_path ) ) {
				unlink( $plugin_zip_path );
				$output->writeln( 'Deleted existing ../.afl-dist/<plugin_name>.zip' );
			}

			// Delete distribution folder if it exists to ensure a clean slate for zipping.
			if ( Helper::safeToDelete( $destination_path, $plugin_dir_name, $destination_path ) ) {
				$output->writeln( 'Deleted existing ../.afl-dist/<plugin_name>/ folder.' );
			} elseif ( file_exists( $destination_path ) ) {
				throw new \Exception( 'Failed to delete existing distribution folder.' );
			}

			// Copy plugin folder to distribution folder for zipping.
			if ( Helper::copyDirectory(
				$source_path,
				$destination_path,
				array(
					'exclude_from' => $config['paths']['plugin_distribution_ignore_file'],
				)
			) ) {
				$output->writeln( 'Copied plugin folder to ../.afl-dist/<plugin_name>/ folder.' );
				$exit_code = 0;
			} else {
				$output->writeln( '<error>Failed to copy plugin folder to ../.afl-dist/<plugin_name>/ folder.</error>' );
				$exit_code = 1;
			}
		} catch ( \Throwable $th ) {
			$exit_code = 1;
			$output->writeln( '<error>' . $th->getMessage() . '</error>' );
		}

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
