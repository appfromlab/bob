<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Copy the plugin-renamer-config.php to plugin root folder.
 * Rename it to .afl-plugin-renamer-config.php
 */
class PluginRenamerCopyConfigCommand extends BaseCommand {

	protected function configure(): void {
		$this->setName( 'afl:plugin-renamer-copy-config' )
			->setDescription( 'Copy plugin renamer file to your WordPress plugin folder.' );
	}

	/**
	 * Execute the command
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '<info>------ START ' . __CLASS__ . '</info>' );

		// Get configuration.
		$config = Helper::getConfig();

		$default_config_file_path = $config['paths']['template_dir'] . 'plugin-renamer-config.php';
		$user_config_file_path    = $config['paths']['plugin_extra_config_dir'] . 'plugin-renamer-config.php';

		if ( ! file_exists( $default_config_file_path ) ) {
			$output->writeln( '<error>ERROR: Cannot find source plugin-renamer-config.php.</error>' );
			return 1;
		}

		if ( file_exists( $user_config_file_path ) ) {
			$output->writeln( '<error>ERROR: There is existing plugin-renamer-config.php file in .afl-extra/config folder.</error>' );
			return 1;
		}

		$extra_config_folder = dirname( $user_config_file_path ) . DIRECTORY_SEPARATOR;

		if ( ! file_exists( $extra_config_folder ) ) {
			mkdir( $extra_config_folder, 0774, true );
		}

		if ( copy( $default_config_file_path, $user_config_file_path ) ) {
			$output->writeln( '<info>SUCCESS: You can now edit the .afl-extra/config/plugin-renamer-config.php</info>' );
		}

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return 0;
	}
}
