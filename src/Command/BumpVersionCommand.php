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

		// Define regex patterns for version bumping.
		$regex_pattern_list = array(
			$config['paths']['plugin_file'] => array(
				'/define\(\s*[\'"]' . $config['plugin_version_constant'] . '[\'"]\s*,\s*[\'"][^\'"]*[\'"]\s*\);/',
				"define( '{$config['plugin_version_constant']}', '{$plugin_headers['Version']}' );",
			),
		);

		// Load the WordPress plugin .afl-extra/config/bump-version-pattern-config.php and merge any additional regex patterns defined there.
		$extra_pattern_list = array();

		$extra_pattern_config_file_path = $config['paths']['plugin_extra_config_dir'] . 'bump-version-pattern-config.php';

		if ( file_exists( $extra_pattern_config_file_path ) ) {
			$extra_pattern_list = include $extra_pattern_config_file_path;
		}

		$regex_pattern_list = array_merge( $regex_pattern_list, $extra_pattern_list );

		Helper::replaceFileContentWithRegex( $regex_pattern_list );

		// Run the WordPress .afl-extra/tools/bump-version-tool.php for any custom operations.
		$bump_version_extra_tool_file_path = $config['paths']['plugin_extra_tools_dir'] . 'bump-version-tool.php';

		if ( file_exists( $bump_version_extra_tool_file_path ) ) {
			include $bump_version_extra_tool_file_path;
		}

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return 0;
	}
}
