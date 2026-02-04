<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Make Language POT File
 */
class MakePotCommand extends BaseCommand {

	protected function configure(): void {
		$this->setName( 'afl:make-pot' )
			->setDescription( 'Generate the plugin language POT file' );
	}

	/**
	 * Run the make pot process
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '<info>------ START ' . __CLASS__ . '</info>' );

		// Get configuration from composer.json.
		$config = Helper::getConfig();

		// Change to parent directory to ensure correct paths.
		chdir( $config['plugin_dir'] );

		// Validate paths and configuration.
		if ( ! file_exists( $config['plugin_bin_dir'] . 'wp-cli.phar' ) ) {
			$output->writeln( '<error>ERROR: WP-CLI PHAR file not found at ./.bin/wp-cli.phar</error>' );
			return 1;
		}

		if ( ! is_dir( $config['plugin_language_dir'] ) ) {
			$output->writeln( '<error>ERROR: languages directory not found.</error>' );
			return 1;
		}

		if ( empty( $config['plugin_folder_name'] ) ) {
			$output->writeln( '<error>ERROR: Plugin Folder Name not setup in composer.json.</error>' );
			return 1;
		}

		// Build command with escaped arguments.
		$command = 'php ' . escapeshellarg( $config['plugin_bin_dir'] . 'wp-cli.phar' ) . ' i18n make-pot . languages/' . escapeshellarg( $config['plugin_folder_name'] . '.pot' );

		$output->writeln( 'Command: ' . $command );

		$command_output = array();
		$return_code    = 0;

		exec( escapeshellcmd( $command ), $command_output, $return_code );

		foreach ( $command_output as $line ) {
			$output->writeln( $line );
		}

		if ( 0 !== $return_code ) {
			$output->writeln( '<error>ERROR: Failed to generate POT file (exit code: ' . $return_code . ')</error>' );
			return $return_code;
		}

		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );

		return 0;
	}
}
