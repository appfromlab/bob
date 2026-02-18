<?php
/**
 * Bump Version Pattern Config
 *
 * This file returns an array of regex patterns for bumping version in additional files.
 * Place this file in the WordPress plugin .afl-extra/config folder.
 *
 * @version 20260218000
 */
function afl_bob_extra_bump_version_pattern_config() {

	if ( ! class_exists( 'Appfromlab\Bob\Helper' ) ) {
		require dirname( dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR . 'vendor-bin/appfromlab-bob/vendor/autoload.php';
	}

	if ( empty( $config ) ) {
		$config = \Appfromlab\Bob\Helper::getConfig();
	}

	if ( empty( $plugin_headers ) ) {
		$plugin_headers = \Appfromlab\Bob\Helper::getPluginHeaders( $config['paths']['plugin_file'] );
	}

	$regex_pattern_list = array();

	if ( ! empty( $config['paths']['plugin_dir'] ) && ! empty( $plugin_headers['Version'] ) ) {
		$additional_file_path = $config['paths']['plugin_dir'] . 'includes/class-afl-wc-utm.php';

		$regex_pattern_list[ $additional_file_path ] = array(
			'/const\s+VERSION\s*=\s*[\'"][^\'"]*[\'"]\s*;/',
			"const VERSION = '{$plugin_headers['Version']}';",
		);
	}

	return $regex_pattern_list;
}

return afl_bob_extra_bump_version_pattern_config();
