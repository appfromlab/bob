<?php
/**
 * HelperScoper.php
 *
 * @package Appfromlab\Bob
 */

namespace Appfromlab\Bob;

use Appfromlab\Bob\Helper;

/**
 * Helper class for tools
 *
 * Provides utility methods for file handling, configuration management, and plugin operations.
 */
class HelperScoper {

	/**
	 * Get the namespace prefix for PHP-Scoper
	 *
	 * Retrieves the namespace prefix from the plugin configuration, or returns a default value if not set.
	 *
	 * @return string The namespace prefix to use for scoping.
	 */
	public static function getNamespacePrefix() {
		$config = Helper::getConfig();

		return $config['php_scoper']['namespace_prefix'] ?? 'AFL_Bob\\Vendor';
	}

	/**
	 * Get list of folders excluded from PHP-Scoper
	 *
	 * Reads the composer installed.php file and extracts package names that are
	 * marked as dev requirements, which should be excluded from scoping.
	 *
	 * @return array List of package names to exclude from scoping.
	 */
	public static function getExcludeFolders() {
		$exclude_folders = array();

		$config = Helper::getConfig();

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

	/**
	 * Get list of namespaces excluded from PHP-Scoper
	 *
	 * Combines a default list of namespaces to exclude with any additional namespaces
	 * specified in the plugin configuration.
	 *
	 * @return array List of namespaces to exclude from scoping.
	 */
	public static function getExcludeNamespaces() {

		$excluded_namespaces = array();

		$config = Helper::getConfig();

		if ( ! empty( $config['php_scoper']['exclude_namespaces'] ) && is_array( $config['php_scoper']['exclude_namespaces'] ) ) {
			$excluded_namespaces = array_merge( $excluded_namespaces, $config['php_scoper']['exclude_namespaces'] );
		}

		return $excluded_namespaces;
	}

	/**
	 * Get list of patchers for PHP-Scoper
	 *
	 * Defines a list of callback functions that can modify the content of files during the scoping process.
	 * This allows for custom adjustments to be made to specific files, such as fixing class references in autoload files.
	 *
	 * @return array List of patcher callback functions.
	 */
	public static function getPatchers(){

		$config = Helper::getConfig();

		return array(
			function (string $filePath, string $prefix, string $content) use ( $config ): string {
				if ( $config['paths']['plugin_vendor_dir'] . 'composer/autoload_real.php' === $filePath ) {
					$content = str_replace(
						"'Composer\\Autoload\\ClassLoader'",
						"'" . $config['php_scoper']['namespace_prefix'] . "\\Composer\\Autoload\\ClassLoader'",
						$content
					);
				}

				return $content;
			},
		);
	}

	/**
	 * Get list of classes excluded from PHP-Scoper
	 *
	 * Combines a default list of classes to exclude with any additional classes
	 * specified in the plugin configuration.
	 *
	 * @return array List of classes to exclude from scoping.
	 */
	public static function getExcludeClasses() {
		$exclude_classes =  array(
			'Composer\Semver\VersionParser'
		);

		$config = Helper::getConfig();

		if ( ! empty( $config['php_scoper']['exclude_classes'] ) && is_array( $config['php_scoper']['exclude_classes'] ) ) {
			$exclude_classes = array_merge( $exclude_classes, $config['php_scoper']['exclude_classes'] );
		}

		return $exclude_classes;
	}
}
