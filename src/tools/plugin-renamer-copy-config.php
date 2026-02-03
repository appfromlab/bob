<?php
namespace Appfromlab\Bob\Tools;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Copy the plugin-renamer-config.php to plugin root folder.
 * Rename it to .afl-plugin-renamer-config.php
 */
class PluginRenamerCopyConfig extends Command {

	protected function configure(): void {
		$this->setName( 'Copy plugin renamer config file' )
			->setDescription( 'Copy plugin renamer file to your WordPress plugin folder.' );
	}

	/**
	 * Run the plugin renamer copy config process
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( "\n<info>------ START " . __CLASS__ . "</info>\n" );

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

		$output->writeln( "\n<info>------ END " . __CLASS__ . "</info>\n" );

		return 0;
	}
}
