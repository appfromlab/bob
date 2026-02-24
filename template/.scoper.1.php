<?php
/**
 * Configuration for PHP-Scoper Stage 1.
 *
 * @version 20260225000
 */

declare(strict_types=1);

// check if called from command line.
if ( in_array( php_sapi_name(), array( 'cli', 'phpdbg' ), true ) === false ) {
	throw new \Exception( 'This configuration file can only be used from the command line.' );
}

use Appfromlab\Bob\HelperScoper;

/**
 * Get PHP-Scoper configuration Stage 1 using appfromlab/bob.
 *
 * @return array
 */
function afl_scoper_get_config_stage_1(): array {

	$afl_bob_helper_path_list = array(
		__DIR__ . '/vendor-bin/appfromlab-bob/vendor/appfromlab/bob/src/Helper.php',
		__DIR__ . '/vendor/appfromlab/bob/src/Helper.php',
	);

	foreach ( $afl_bob_helper_path_list as $afl_bob_helper_path ) {
		if ( file_exists( $afl_bob_helper_path ) ) {
			require $afl_bob_helper_path;
			require dirname( $afl_bob_helper_path ) . DIRECTORY_SEPARATOR . 'HelperScoper.php';
			break;
		}
	}

	if ( ! class_exists( 'Appfromlab\Bob\Helper', false ) ) {
		throw new \Exception( 'The Appfromlab\Bob\Helper class not found. Make sure appfromlab/bob is installed.' );
	}

	return HelperScoper::getConfigStage1();
}

// You can do your own things here, e.g. collecting symbols to expose dynamically
// or files to exclude.
// However beware that this file is executed by PHP-Scoper, hence if you are using
// the PHAR it will be loaded by the PHAR. So it is highly recommended to avoid
// to auto-load any code here: it can result in a conflict or even corrupt
// the PHP-Scoper analysis.

return afl_scoper_get_config_stage_1();
