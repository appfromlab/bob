<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Bump Plugin Version
 *
 * It will update the version number in the main plugin file and readme.txt
 * based on the configuration in composer.json.
 */
class BumpVersionCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:bump-version' )
			->setDescription( 'Bump plugin version using value from plugin header.' );
	}

	/**
	 * Execute the command
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		// Get configuration.
		$config = Helper::getConfig();

		$plugin_headers = Helper::getPluginHeaders( $config['paths']['plugin_file'] );

		$output->writeln( "Bump Plugin Version to: {$plugin_headers['Version']}" );
		$output->writeln( "Plugin File: {$config['paths']['plugin_file']}" );
		$output->writeln( "Plugin Version Constant: {$config['plugin_version_constant']}" );

		$files_regex_pattern = array(
			$config['paths']['plugin_file'] => array(
				'/define\(\s*[\'"]' . $config['plugin_version_constant'] . '[\'"]\s*,\s*[\'"][^\'"]*[\'"]\s*\);/',
				"define( '{$config['plugin_version_constant']}', '{$plugin_headers['Version']}' );",
			),
		);

		Helper::replaceFileContentWithRegex( $files_regex_pattern, $output );

		// check for extra custom file from .afl-extra/tools folder.
		$custom_config_file_path = $config['paths']['plugin_extra_tools_dir'] . 'bump-version-extra.php';

		if ( file_exists( $custom_config_file_path ) ) {
			include $custom_config_file_path;
		}

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return 0;
	}
}
