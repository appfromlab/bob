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
	 * Get list of folders excluded from PHP-Scoper
	 *
	 * Reads the composer installed.php file and extracts package names that are
	 * marked as dev requirements, which should be excluded from scoping.
	 *
	 * @return array List of package names to exclude from scoping.
	 */
	public static function getExcludedFolders() {
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
	public static function getExcludedNamespaces() {

		$excluded_namespaces = array(
			'Composer\\',
		);

		$config = Helper::getConfig();

		if ( is_array( $config['php_scoper']['excluded_namespaces'] ) && ! empty( $config['php_scoper']['excluded_namespaces'] ) ) {
			$excluded_namespaces = array_merge( $excluded_namespaces, $config['php_scoper']['excluded_namespaces'] );
		}

		return $excluded_namespaces;
	}
}
