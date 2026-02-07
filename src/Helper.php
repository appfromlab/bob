<?php
/**
 * Helper utilities for Bob plugin manager
 *
 * This class provides utility methods for managing WordPress plugin configuration,
 * file operations, and PHP-Scoper configuration for the Appfromlab Bob plugin manager.
 *
 * @package Appfromlab\Bob
 */

namespace Appfromlab\Bob;

/**
 * Helper class for tools
 *
 * Provides utility methods for file handling, configuration management, and plugin operations.
 */
class Helper {

	/**
	 * Find the composer.json path of the WordPress plugin
	 *
	 * Traverses up the directory tree to locate the composer.json file that contains
	 * the appfromlab/bob configuration.
	 *
	 * @return string The path to composer.json, or empty string if not found.
	 */
	public static function findComposerJsonPath() {

		$current_dir    = dirname( __DIR__ );
		$max_iterations = 6;
		$iteration      = 0;

		// Traverse up the directory tree looking for composer.json.
		while ( dirname( $current_dir ) !== $current_dir && $iteration < $max_iterations ) {

			$composer_json_path = $current_dir . DIRECTORY_SEPARATOR . 'composer.json';

			if ( file_exists( $composer_json_path ) ) {
				$composer = json_decode( file_get_contents( $composer_json_path ), true );

				// Check if this is the right composer.json with appfromlab/bob configuration.
				if ( ! empty( $composer ) && ! empty( $composer['extra']['appfromlab/bob'] ) ) {
					return $composer_json_path;
				}
			}

			// Go up one directory.
			$current_dir = dirname( $current_dir );

			++$iteration;
		}

		return '';
	}

	/**
	 * Get file and folder paths
	 *
	 * Returns an array of paths for plugin directories and files based on the
	 * composer.json configuration.
	 *
	 * @return array Associative array of path configurations.
	 */
	private static function getPaths() {

		$output = array();

		// assume this package is installed in plugin vendor folder.
		$composer_plugin_dir = dirname( __DIR__ ) . DIRECTORY_SEPARATOR;

		// find the WordPress plugin composer.json.
		$composer_json_file_path = self::findComposerJsonPath();

		if ( ! empty( $composer_json_file_path ) ) {
			$plugin_dir = dirname( $composer_json_file_path ) . DIRECTORY_SEPARATOR;

			$output = array(
				'root_dir'                   => $composer_plugin_dir,
				'template_dir'               => $composer_plugin_dir . 'template' . DIRECTORY_SEPARATOR,
				'plugin_composer_file'       => $plugin_dir . 'composer.json',
				'plugin_composer_lock_file'  => $plugin_dir . 'composer.lock',
				'plugin_dir'                 => $plugin_dir,
				'plugin_file'                => '',
				'plugin_bin_dir'             => $plugin_dir . '.bin' . DIRECTORY_SEPARATOR,
				'plugin_extra_dir'           => $plugin_dir . '.afl-extra' . DIRECTORY_SEPARATOR,
				'plugin_extra_config_dir'    => $plugin_dir . '.afl-extra' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR,
				'plugin_extra_tools_dir'     => $plugin_dir . '.afl-extra' . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR,
				'plugin_extra_readme_dir'    => $plugin_dir . '.afl-extra' . DIRECTORY_SEPARATOR . 'readme' . DIRECTORY_SEPARATOR,
				'plugin_language_dir'        => $plugin_dir . 'languages' . DIRECTORY_SEPARATOR,
				'plugin_readme_file'         => $plugin_dir . 'readme.txt',
				'plugin_vendor_dir'          => $plugin_dir . 'vendor' . DIRECTORY_SEPARATOR,
				'plugin_vendor_prefixed_dir' => $plugin_dir . 'vendor-prefixed' . DIRECTORY_SEPARATOR,
				'plugin_scoper_config_file'  => $plugin_dir . 'scoper.inc.php',
			);
		}

		return $output;
	}

	/**
	 * Get general plugin configuration
	 *
	 * Loads and validates the plugin configuration from composer.json extra section.
	 * Exits with error if configuration is missing or invalid.
	 *
	 * @return array Plugin configuration array with paths and settings.
	 */
	public static function getConfig() {

		// check composer.json exists.
		$paths = self::getPaths();

		if ( ! empty( $paths['plugin_composer_file'] ) && file_exists( $paths['plugin_composer_file'] ) ) {
			$composer = json_decode(
				file_get_contents( $paths['plugin_composer_file'] ),
				true
			);
		} else {
			echo "ERROR: composer.json not found.\n";
			exit( 1 );
		}

		if (
			empty( $composer )
			|| empty( $composer['extra']['appfromlab/bob']['plugin_folder_name'] )
			|| empty( $composer['extra']['appfromlab/bob']['plugin_version_constant'] )
		) {
			echo "Composer.json ['extra']['appfromlab/bob'] not setup.\n";
			exit( 1 );
		}

		$config          = $composer['extra']['appfromlab/bob'];
		$config['paths'] = $paths;

		// generate path to main plugin file.
		$config['paths']['plugin_file'] = $config['paths']['plugin_dir'] . $config['plugin_folder_name'] . '.php';

		// check if main plugin file exists.
		if ( ! file_exists( $config['paths']['plugin_file'] ) ) {
			echo "ERROR: Main plugin file not found: {$config['paths']['plugin_file']}\n";
			exit( 1 );
		}

		return $config;
	}

	/**
	 * Get the plugin header data in associative array
	 *
	 * Extracts standard WordPress plugin headers from the main plugin file.
	 *
	 * @param string $plugin_file Path to the main plugin file.
	 * @return array Plugin header data.
	 */
	public static function getPluginHeaders( $plugin_file ) {

		$default_headers = array(
			'Plugin Name'       => '',
			'Plugin URI'        => '',
			'Version'           => '',
			'Description'       => '',
			'Author'            => '',
			'Author URI'        => '',
			'Text Domain'       => '',
			'Domain Path'       => '',
			'Network'           => '',
			'Requires at least' => '',
			'Requires PHP'      => '',
			'Update URI'        => '',
			'Requires Plugins'  => '',
			'License'           => '',
			'License URI'       => '',
			'Stable Tag'        => '',
			'Tested up to'      => '',
			'Tags'              => '',
		);

		$plugin_data = self::getFileData( $plugin_file, $default_headers );

		if ( empty( $plugin_data['Stable Tag'] ) ) {
			$plugin_data['Stable Tag'] = $plugin_data['Version'];
		}

		return $plugin_data;
	}

	/**
	 * Extract File Header Data based on headers given
	 *
	 * Reads the first 8KB of a file and extracts header data based on the provided
	 * header keys using regex pattern matching.
	 *
	 * @param string $file      Path to the file to read.
	 * @param array  $headers   Associative array of headers to extract.
	 * @return array Associative array with extracted header data.
	 */
	public static function getFileData( $file, $headers ) {

		// Pull only the first 8 KB of the file in.
		$file_data = file_get_contents( $file, false, null, 0, 8 * 1024 );

		if ( false === $file_data ) {
			$file_data = '';
		}

		// Make sure we catch CR-only line endings.
		$file_data = str_replace( "\r", "\n", $file_data );

		if ( empty( $headers ) ) {
			$headers = array();
		}

		foreach ( $headers as $field => $field_value ) {
			if ( preg_match( '/^(?:[ \t]*<\?php)?[ \t\/*#@]*' . preg_quote( $field, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match[1] ) {
				$headers[ $field ] = trim( $match[1] );
			} else {
				$headers[ $field ] = '';
			}
		}

		return $headers;
	}

	/**
	 * Replace a file content based on regex pattern
	 *
	 * Performs regex-based find and replace on one or more files and outputs the results.
	 * Exits with error if pattern match fails.
	 *
	 * @param array $files_regex_pattern Associative array with format:
	 *                                   'file_path' => array( 'regex_pattern', 'replacement_value' )
	 * @return void
	 */
	public static function replaceFileContentWithRegex( $files_regex_pattern ) {

		foreach ( $files_regex_pattern as $file => $replacements ) {
			if ( ! file_exists( $file ) ) {
				echo "ERROR: File not found: {$file}\n";
				exit( 1 );
			}

			$existing_content = file_get_contents( $file );
			$updated_content  = $existing_content;

			if ( ! is_array( $replacements[0] ) ) {
				$replacements = array( $replacements );
			}

			foreach ( $replacements as [$pattern, $replacement] ) {
				$updated_content = preg_replace( $pattern, $replacement, $updated_content, -1, $count );

				if ( $count === 0 ) {
					echo "ERROR: No match for pattern in {$file}: {$pattern}\n";
					exit( 1 );
				} else {
					echo $replacement . "\n";
				}
			}

			if ( $updated_content !== $existing_content ) {
				file_put_contents( $file, $updated_content );

				echo "File Updated: {$file}\n\n";
			}
		}
	}

	/**
	 * Safe file deletion check with security validation
	 *
	 * @param string $file_path Full file path to check.
	 * @param string $base_file_name File name to verify against.
	 * @param string $allowed_dir Optional: restrict deletion to this directory (prevents root drive deletion).
	 * @return bool True only if file is safe to delete
	 */
	public static function safeToDelete( $file_path, $base_file_name = 'xxx.php', $allowed_dir = '' ) {

		// Basic validation.
		if ( empty( $allowed_dir ) || ! file_exists( $file_path ) || basename( $file_path ) !== $base_file_name ) {
			return false;
		}

		// Prevent path traversal.
		if ( false !== strpos( $file_path, '..' ) ) {
			return false;
		}

		// Resolve real path to prevent symlink attacks.
		$real_path = realpath( $file_path );

		if ( false === $real_path ) {
			return false;
		}

		// If allowed_dir is specified, verify file is within it.
		if ( ! empty( $allowed_dir ) ) {
			$allowed_real_path = realpath( $allowed_dir );

			if ( false === $allowed_real_path ) {
				return false;
			}

			// Ensure the file is within the allowed directory.
			if ( 0 !== strpos( $real_path, $allowed_real_path ) ) {
				return false;
			}
		}

		// Prevent deletion of files in root directory.
		$parent_dir = dirname( $real_path );

		// Windows root drives: C:\, D:\, etc.
		if ( preg_match( '/^[a-z]:\\$/i', $parent_dir ) ) {
			return false;
		}

		// Unix/Linux/macOS root directory ( / ).
		if ( '/' === $parent_dir ) {
			return false;
		}

		$command = '';

		if ( 'Windows' === PHP_OS_FAMILY ) {

			if ( is_dir( $real_path ) ) {
				$command = 'rmdir /s /q ' . escapeshellarg( $real_path );
			} else {
				$command = 'del ' . escapeshellarg( $real_path );
			}
		} elseif ( 'Darwin' === PHP_OS_FAMILY || 'Linux' === PHP_OS_FAMILY ) {

			$command = 'rm -rf ' . escapeshellarg( $real_path );
		}

		$exit_code = 0;

		if ( ! empty( $command ) ) {
			passthru(
				escapeshellcmd( $command ),
				$exit_code
			);
		}

		return $exit_code ? false : true;
	}

	/**
	 * Get PHP-Scoper configuration
	 *
	 * Retrieves configuration for PHP-Scoper including list of folders to exclude.
	 *
	 * @return array Scoper configuration array.
	 */
	public static function getScoperConfig() {

		$config = array(
			'excluded_folders' => self::getScoperExcludedFolders(),
		);

		return $config;
	}

	/**
	 * Get list of folders excluded from PHP-Scoper
	 *
	 * Reads the composer installed.php file and extracts package names that are
	 * marked as dev requirements, which should be excluded from scoping.
	 *
	 * @return array List of package names to exclude from scoping.
	 */
	public static function getScoperExcludedFolders() {
		$exclude_folders = array();

		$config = self::getConfig();

		// get packages from installed.php.
		$composer_installed_file_path = $config['paths']['plugin_vendor_dir'] . 'composer/installed.php';

		try {
			if ( file_exists( $composer_installed_file_path ) ) {

				$installed_packages = include $composer_installed_file_path;

				if ( empty( $installed_packages['root']['name'] ) || ! isset( $installed_packages['versions'] ) ) {
					throw new \Exception( 'ERROR: Cannot validate /vendor/composer/installed.php.' );
				}

				if ( is_array( $installed_packages['versions'] ) ) {
					foreach ( $installed_packages['versions'] as $package_index => $package ) {

						if ( ! empty( $package['dev_requirement'] ) ) {
							$exclude_folders[] = $package_index;
						}
					}
				}
			} else {
				throw new \Exception( 'ERROR: /vendor/composer/installed.php not found.' );
			}
		} catch ( \Throwable $th ) {
			echo $th->getMessage() . "\n";
			exit( 1 );
		}

		return $exclude_folders;
	}
}
