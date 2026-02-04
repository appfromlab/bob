<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
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
	 * Execute the command
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

		// Build command with arguments.
		$process = new Process(
			array(
				'php',
				$config['plugin_bin_dir'] . 'wp-cli.phar',
				'i18n',
				'make-pot',
				'.',
				'languages/' . $config['plugin_folder_name'] . '.pot',
			)
		);

		$output->writeln( 'Running: ' . $process->getCommandLine() );

		$process->run(
			function ( $type, $buffer ) use ( $output ) {
				$output->write( $buffer );
			}
		);

		if ( ! $process->isSuccessful() ) {
			$output->writeln( '<error>ERROR: Failed to generate POT file</error>' );
			return 1;
		}

		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );

		return 0;
	}
}
