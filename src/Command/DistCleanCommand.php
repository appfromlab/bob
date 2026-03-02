<?php
/**
 * Clean Distribution Command
 *
 * Cleans the distribution folder by deleting existing files and folders.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Clean Distribution Command
 *
 * Cleans the distribution folder by deleting existing files and folders.
 */
class DistCleanCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description for the Composer console.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:dist-clean' )
			->setDescription( 'Clean the distribution folder by deleting existing files and folders.' );
	}

	/**
	 * Execute the command
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success, non-zero for failure).
	 * @throws \RuntimeException If a runtime exception occurs during the cleaning process.
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		$exit_code = 0;

		// Get configuration.
		$config = Helper::getConfig();

		$destination_path = $config['paths']['plugin_distribution_dir'];
		$plugin_dir_name  = $config['plugin_folder_name'];

		try {

			// Delete the distribution folder.
			if ( Helper::safeToDelete( $destination_path, $plugin_dir_name, $destination_path ) ) {
				$output->writeln( 'Deleted existing ../.afl-dist/<plugin_name>/ folder.' );
			} elseif ( file_exists( $destination_path ) ) {
				throw new \RuntimeException( 'Failed to delete ../.afl-dist/<plugin_name>/ folder.' );
			}
		} catch ( \RuntimeException $th ) {
			$exit_code = 1;
			$output->writeln( '<error>' . $th->getMessage() . '</error>' );
		}

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
