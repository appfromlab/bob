<?php
/**
 * Configuration for PHP-Scoper
 *
 * @version 20260115
 */
declare(strict_types=1);

$afl_composer_installed_file_path = __DIR__ . 'vendor/composer/installed.php';
$afl_exclude_folders              = array();

// find dev packages folders to exclude from scoping
if ( file_exists( $afl_composer_installed_file_path ) ) {
	$afl_composer_installed_packages = include $afl_composer_installed_file_path;

	if ( ! empty( $afl_composer_installed_packages['versions'] ) && is_array( $afl_composer_installed_packages['versions'] ) ) {
		foreach ( $afl_composer_installed_packages['versions'] as $package_index => $package ) {
			if ( ! empty( $package['dev_requirement'] ) ) {
				$afl_exclude_folders[] = $package_index;
			}
		}
	}
}

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
	'prefix'                  => 'MyVendorName\\AFL_Plugin_Boilerplate\\Vendor',

	// The base output directory for the prefixed files.
	// This will be overridden by the 'output-dir' command line option if present.
	'output-dir'              => 'vendor-prefixed',

	// By default when running php-scoper add-prefix, it will prefix all relevant code found in the current working
	// directory. You can however define which files should be scoped by defining a collection of Finders in the
	// following configuration key.
	//
	// This configuration entry is completely ignored when using Box.
	//
	// For more see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#finders-and-paths.
	'finders'                 => array(
		Isolated\Symfony\Component\Finder\Finder::create()
		->files()
		->in( 'vendor' )
		->name( '*.php' )
		->exclude( $afl_exclude_folders ),
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
	'patchers'                => array(),

	// List of symbols to consider internal i.e. to leave untouched.
	//
	// For more information see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#excluded-symbols.
	'exclude-namespaces'      => array(),
	'exclude-classes'         => array(
		// 'ReflectionClassConstant',
	),
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
