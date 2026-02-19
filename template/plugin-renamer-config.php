<?php
/**
 * Plugin Renamer Configuration Template
 *
 * This is a template configuration file for the plugin renamer command.
 * Copy this file to .afl-extra/config/plugin-renamer-config.php and update
 * the values to match your plugin's naming scheme.
 *
 * @package Appfromlab\Bob\Template
 *
 * @return array Configuration array with the following structure:
 *         - 'name_list': Associative array of plugin naming conventions
 *         - 'merge_tags': Additional replacement tags
 *         - 'folder_list': Folders to search for replacements
 *         - 'file_list': Specific files to search for replacements
 */

return array(
	'name_list'   => array(
		'composer_package_name'            => 'appfromlab/afl-plugin-boilerplate',
		'php_namespace_full_name'          => 'MyVendorName\\AFL_Plugin_Boilerplate', // pascal case with double backslash.
		'php_namespace_vendor_name'        => 'MyVendorName', // pascal case, no spaces, underscore allowed.
		'php_namespace_package_name'       => 'AFL_Plugin_Boilerplate', // pascal case with underscore.
		'plugin_name'                      => 'AFL Plugin Boilerplate', // pascal case with spaces.
		'plugin_constant_uppercase_prefix' => 'AFL_PLUGIN_BOILERPLATE_', // upercase with underscore and ends with underscore.
		'plugin_name_lowercase_underscore' => 'afl_plugin_boilerplate', // lowercase with underscore.
		'meta_prefix'                      => "'_aflpb_'",   // keep inner single quotes, start with underscore, lowercase and ends with underscore.
		'meta_prefix_public'               => "'aflpb_'",   // keep inner single quotes, lowercase and ends with underscore.
		'option_key'                       => "'aflpb'",   // keep inner single quotes, lowercase.
		'option_key_prefix'                => "'aflpb_'", // keep inner single quotes, lowercase and ends with underscore.
		'plugin_author_url'                => 'https://www.acme.org',
	),
	'merge_tags'  => array(
		'afl_plugin_url'               => 'https://github.com/appfromlab/afl-plugin-boilerplate/',
		'afl_plugin_short_description' => 'A WordPress plugin boilerplate.',
		'afl_plugin_author_name'       => 'Appfromlab',
		'afl_plugin_company_name'      => 'Appfromlab Pte Ltd',
		'afl_plugin_copyright'         => 'Copyright (C) 2025-2026 Appfromlab Pte Ltd.',
	),
	// folder list to perform search and replace.
	'folder_list' => array(
		'config',
		'src',
	),
	// file list to perform search and replace.
	'file_list'   => array(
		'afl-plugin-boilerplate.php',
		'composer.json',
		'readme.txt',
		'.phpcs.xml',
		'.scoper.inc.php',
	),
);
