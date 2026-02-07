<?php
/**
 * Plugin Renamer Command
 *
 * Renames all occurrences of plugin identifiers (namespace, prefixes, constants, etc.)
 * throughout the plugin codebase based on configuration file specifications.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Rename a plugin
 */
class PluginRenamerCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:plugin-renamer' )
			->setDescription( 'Rename a plugin using the plugin renamer config file.' );
	}

	/**
	 * Execute the command
	 *
	 * Renames all plugin identifiers according to the configuration file.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success, 1 on error).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		// Get configuration.
		$config = Helper::getConfig();

		$default_config_file_path = $config['paths']['template_dir'] . 'plugin-renamer-config.php';
		$user_config_file_path    = $config['paths']['plugin_extra_config_dir'] . 'plugin-renamer-config.php';

		if ( ! file_exists( $default_config_file_path ) ) {
			$output->writeln( '<error>ERROR: Default config not found (' . $default_config_file_path . ')</error>' );
			return 1;
		}

		if ( ! file_exists( $user_config_file_path ) ) {
			$output->writeln( '<error>ERROR: User config not found (' . $user_config_file_path . ')</error>' );
			return 1;
		}

		$default_config = include $default_config_file_path;
		$new_config     = include $user_config_file_path;

		// Validate config file.
		if ( empty( $default_config['folder_list'] ) ) {
			$output->writeln( '<error>ERROR: Config folder list is empty.</error>' );
			return 1;
		}

		if ( empty( $default_config['name_list'] ) || empty( $new_config['name_list'] ) ) {
			$output->writeln( '<error>ERROR: Config name list is empty.</error>' );
			return 1;
		}

		if ( empty( $default_config['merge_tags'] ) || empty( $new_config['merge_tags'] ) ) {
			$output->writeln( '<error>ERROR: Config merge_tags list is empty.</error>' );
			return 1;
		}

		// Auto create name for dash.
		$new_config['name_list']['plugin_name_lowercase_dashes'] = str_replace( '_', '-', $new_config['name_list']['plugin_name_lowercase_underscore'] );

		// Check missing folder_list in user config.
		if ( empty( $new_config['folder_list'] ) ) {
			$new_config['folder_list'] = $default_config['folder_list'];
		}

		// Check missing file_list in user config.
		if ( empty( $new_config['file_list'] ) ) {
			$new_config['file_list'] = $default_config['file_list'];
		}

		$list_of_file_to_rename = array();

		// Add all php files into list.
		foreach ( $new_config['folder_list'] as $tmp_folder_name ) {
			$tmp_folder_full_path = $config['paths']['plugin_dir'] . $tmp_folder_name . DIRECTORY_SEPARATOR;

			if ( ! is_dir( $tmp_folder_full_path ) ) {
				$output->writeln( '<error>ERROR: Folder not found: ' . $tmp_folder_full_path . '</error>' );
				return 1;
			}

			$iterator = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator( $tmp_folder_full_path ),
				\RecursiveIteratorIterator::SELF_FIRST
			);

			foreach ( $iterator as $file ) {
				if ( $file->isFile() && $file->getExtension() === 'php' ) {
					$list_of_file_to_rename[] = $file->getRealPath();
				}
			}
		}

		// Add files from file_list.
		foreach ( $new_config['file_list'] as $tmp_file_path ) {

			$tmp_file_full_path = $config['paths']['plugin_dir'] . $tmp_file_path;

			if ( file_exists( $tmp_file_full_path ) ) {
				$list_of_file_to_rename[] = $tmp_file_full_path;
			}
		}

		// Loop through all files and rename the content of the file with the new config.
		$files_replacements = array();

		foreach ( $list_of_file_to_rename as $file ) {
			$files_replacements[ $file ] = array();

			foreach ( $default_config['name_list'] as $key => $default_value ) {
				$new_value = $new_config['name_list'][ $key ];

				if ( $default_value !== $new_value ) {
					$files_replacements[ $file ][] = array(
						$default_value,
						$new_value,
					);
				}
			}

			foreach ( $new_config['merge_tags'] as $merge_tag_key => $merge_tag_value ) {

				if ( is_string( $merge_tag_key ) && '' !== $merge_tag_key && is_string( $merge_tag_value ) && '' !== $merge_tag_value ) {
					$files_replacements[ $file ][] = array(
						'[' . $merge_tag_key . ']',
						$merge_tag_value,
					);
				}
			}
		}

		// Apply replacements to all files.
		$has_any_file_updated = false;

		foreach ( $files_replacements as $file => $replacements ) {
			if ( empty( $replacements ) ) {
				continue;
			}

			$existing_content = file_get_contents( $file );
			$updated_content  = $existing_content;

			foreach ( $replacements as [ $old_value, $new_value ] ) {
				$count           = 0;
				$updated_content = str_replace( $old_value, $new_value, $updated_content, $count );

				if ( $count > 0 ) {
					$output->writeln( "Replaced '{$old_value}' with '{$new_value}' ({$count} occurrence(s))" );
				}
			}

			if ( $updated_content !== $existing_content ) {

				file_put_contents( $file, $updated_content );

				$output->writeln( "File Updated: {$file}" );

				$has_any_file_updated = true;
			}
		}

		if ( ! $has_any_file_updated ) {
			$output->writeln( '<error>ERROR: No files was renamed.</error>' );
		}

		// delete language pot file.
		$current_pot_file_path = $config['paths']['plugin_language_dir'] . 'afl-plugin-boilerplate.pot';

		Helper::safeToDelete( $current_pot_file_path, 'afl-plugin-boilerplate.pot', $config['paths']['plugin_language_dir'] );

		// finally rename main plugin file.
		$new_plugin_file_name = basename( $config['paths']['plugin_dir'] );

		$boilerplate_plugin_file_path = $config['paths']['plugin_dir'] . 'afl-plugin-boilerplate.php';
		$new_plugin_file_path         = $config['paths']['plugin_dir'] . $new_plugin_file_name . '.php';

		if ( ! file_exists( $boilerplate_plugin_file_path ) ) {
			$output->writeln( '<error>ERROR: Cannot find default main plugin file afl-plugin-boilerplate.php</error>' );
			return 1;
		}

		rename( $boilerplate_plugin_file_path, $new_plugin_file_path );

		$output->writeln( "Main plugin file was renamed to {$new_plugin_file_name}.php" );

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return 0;
	}
}
