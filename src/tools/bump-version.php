<?php
namespace Appfromlab\Bob\Tools;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Bump Plugin Version
 *
 * It will update the version number in the main plugin file and readme.txt
 * based on the configuration in composer.json.
 */
class BumpVersion extends Command {

	protected function configure(): void {
		$this->setName( 'Bump Version' )
			->setDescription( 'Bump plugin version from composer.json' );
	}

	/**
	 * Execute the bump version process
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( "\n<info>------ START " . __CLASS__ . "</info>\n" );

		$config = Helper::getConfig();

		$plugin_headers = Helper::getPluginHeaders( $config['paths']['plugin_file'] );

		$output->writeln( "Bump Plugin Version to: {$plugin_headers['Version']}" );
		$output->writeln( "Plugin File: {$config['paths']['plugin_file']}" );
		$output->writeln( "Plugin Version Constant: {$config['plugin_version_constant']}\n" );

		$files_regex_pattern = array(
			$config['paths']['plugin_file'] => array(
				'/define\(\s*[\'"]' . $config['plugin_version_constant'] . '[\'"]\s*,\s*[\'"][^\'"]*[\'"]\s*\);/',
				"define( '{$config['plugin_version_constant']}', '{$plugin_headers['Version']}' );",
			),
		);

		Helper::replaceFileContentWithRegex( $files_regex_pattern, $output );

		// check for extra custom file from .afl-extra/tools folder.
		$custom_config_file_path = $config['plugin_extra_tools_dir'] . 'bump-version-extra.php';

		if ( file_exists( $custom_config_file_path ) ) {
			include_once $custom_config_file_path;
		}

		$output->writeln( "\n<info>------ END " . __CLASS__ . "</info>\n" );

		return 0;
	}
}
