<?php
/**
 * Zip Plugin Command
 *
 * Create a zip archive of the plugin for distribution.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Appfromlab\Bob\Composer\BatchCommands;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Execute plugin zip process
 */
class ZipPluginCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description for the Composer console.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:zip-plugin' )
			->setDescription( 'Create a zip archive of the plugin for distribution.' );
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

		// Get configuration.
		$config         = Helper::getConfig();
		$plugin_headers = Helper::getPluginHeaders( $config['paths']['plugin_file'] );

		$source_path      = $config['paths']['plugin_dir'];
		$destination_path = $config['paths']['plugin_distribution_dir'];

		$plugin_dir_name = $config['plugin_folder_name'];
		$plugin_zip_name = $config['plugin_folder_name'] . '-' . $plugin_headers['Version'] . '.zip';
		$plugin_zip_path = dirname( $config['paths']['plugin_distribution_dir'] ) . DIRECTORY_SEPARATOR . $plugin_zip_name;

		$commands = array(
			new ArrayInput( array( 'command' => 'afl:bob:dist-prepare' ) ),
			new Process(
				array(
					'zip',
					'-rq', // Recursive, quiet output.
					$plugin_zip_name,
					$plugin_dir_name . '/', // Trailing slash to ensure contents are zipped with the plugin folder as the root, not the full path.
				),
				dirname( $config['paths']['plugin_distribution_dir'] ) // Set working directory to the parent of the destination path so that the zip contains the plugin folder, not the full path.
			),
			new ArrayInput( array( 'command' => 'afl:bob:dist-clean' ) ),
		);

		$exit_code = BatchCommands::run( $this->getApplication(), $commands, $output );

		if ( 0 === $exit_code && file_exists( $plugin_zip_path ) ) {
			$output->writeln( '<info>Created plugin zip</info>: ' . $plugin_zip_path . '' );
		} else {
			$output->writeln( '<error>Failed to create plugin zip.</error>' );
		}

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
