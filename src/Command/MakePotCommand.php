<?php
/**
 * Make POT File Command
 *
 * Generates a WordPress plugin POT (Portable Object Template) language file
 * for internationalization/translation purposes using WP-CLI.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Appfromlab\Bob\Composer\BatchCommands;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Make Language POT File
 */
class MakePotCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:make-pot' )
			->setDescription( 'Generate the plugin language POT file' );
	}

	/**
	 * Execute the command
	 *
	 * Generates the plugin POT language file using WP-CLI.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success, 1 on error).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '<info>------ START ' . __CLASS__ . '</info>' );

		// Get configuration.
		$config = Helper::getConfig();

		// Validate paths and configuration.
		if ( ! file_exists( $config['paths']['plugin_bin_dir'] . 'wp-cli.phar' ) ) {
			$output->writeln( '<error>ERROR: WP-CLI PHAR file not found at ./.bin/wp-cli.phar</error>' );
			return 1;
		}

		if ( ! is_dir( $config['paths']['plugin_language_dir'] ) ) {
			$output->writeln( '<error>ERROR: languages directory not found.</error>' );
			return 1;
		}

		if ( empty( $config['plugin_folder_name'] ) ) {
			$output->writeln( '<error>ERROR: Plugin Folder Name not setup in composer.json.</error>' );
			return 1;
		}

		$commands = array(
			array(
				'php',
				$config['paths']['plugin_bin_dir'] . 'wp-cli.phar',
				'i18n',
				'make-pot',
				$config['paths']['plugin_dir'],
				$config['paths']['plugin_dir'] . 'languages/' . $config['plugin_folder_name'] . '.pot',
			),
		);

		$exit_code = BatchCommands::run( $commands, $input, $output );

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
