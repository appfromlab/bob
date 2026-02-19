<?php
/**
 * Configuration for PHP-Scoper.
 *
 * @version 20260218000
 */

declare(strict_types=1);

// check if called from command line.
if ( in_array( php_sapi_name(), array( 'cli', 'phpdbg' ), true ) === false ) {
	throw new \Exception( 'This configuration file can only be used from the command line.' );
}

use Isolated\Symfony\Component\Finder\Finder;
use Appfromlab\Bob\Helper;

/**
 * Get PHP-Scoper configuration using appfromlab/bob.
 *
 * @return array
 */
function afl_scoper_get_config(): array {

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

	return Helper::getScoperConfig();
}

$afl_scoper_config = afl_scoper_get_config();

// You can do your own things here, e.g. collecting symbols to expose dynamically
// or files to exclude.
// However beware that this file is executed by PHP-Scoper, hence if you are using
// the PHAR it will be loaded by the PHAR. So it is highly recommended to avoid
// to auto-load any code here: it can result in a conflict or even corrupt
// the PHP-Scoper analysis.

return array(
	// The prefix configuration. If a non-null value is used, a random prefix
	// will be generated instead.
	//
	// For more see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#prefix.
	'prefix'                  => $afl_scoper_config['namespace_prefix'],

	// The base output directory for the prefixed files.
	// This will be overridden by the 'output-dir' command line option if present.
	'output-dir'              => __DIR__ . DIRECTORY_SEPARATOR . 'vendor-prefixed',

	// By default when running php-scoper add-prefix, it will prefix all relevant code found in the current working
	// directory. You can however define which files should be scoped by defining a collection of Finders in the
	// following configuration key.
	//
	// This configuration entry is completely ignored when using Box.
	//
	// For more see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#finders-and-paths.
	'finders'                 => array(
		Finder::create()
		->files()
		->in( 'vendor' )
		->name( '*.php' )
		->exclude( $afl_scoper_config['exclude_folders'] ),
	),

	// List of excluded files, i.e. files for which the content will be left untouched.
	// Paths are relative to the configuration file unless if they are already absolute
	//
	// For more see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#patchers.
	'exclude-files'           => array(
		// 'src/an-excluded-file.php',
		// ...$excludedFiles,
	),

	// PHP version (e.g. `'7.2'`) in which the PHP parser and printer will be configured into. This will affect what
	// level of code it will understand and how the code will be printed.
	// If none (or `null`) is configured, then the host version will be used.
	'php-version'             => null,

	// When scoping PHP files, there will be scenarios where some of the code being scoped indirectly references the
	// original namespace. These will include, for example, strings or string manipulations. PHP-Scoper has limited
	// support for prefixing such strings. To circumvent that, you can define patchers to manipulate the file to your
	// heart contents.
	//
	// For more see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#patchers.
	'patchers'                => $afl_scoper_config['patchers'],

	// List of symbols to consider internal i.e. to leave untouched.
	//
	// For more information see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#excluded-symbols.
	'exclude-namespaces'      => $afl_scoper_config['exclude_namespaces'],
	'exclude-classes'         => $afl_scoper_config['exclude_classes'],
	'exclude-functions'       => array(
		// 'mb_str_split',
	),
	'exclude-constants'       => array(
		// 'STDIN',
	),

	// List of symbols to expose.
	//
	// For more information see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#exposed-symbols.
	'expose-global-constants' => true,
	'expose-global-classes'   => true,
	'expose-global-functions' => true,
	'expose-namespaces'       => array(
		// 'Acme\Foo'                     // The Acme\Foo namespace (and sub-namespaces)
		// '~^PHPUnit\\\\Framework$~',    // The whole namespace PHPUnit\Framework (but not sub-namespaces)
		// '~^$~',                        // The root namespace only
		// '',                            // Any namespace
	),
	'expose-classes'          => array(),
	'expose-functions'        => array(),
	'expose-constants'        => array(),
);
