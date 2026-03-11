<?php
/**
 * Plugin Renamer Configuration Template
 *
 * This is a template configuration file for the plugin renamer command.
 * Copy this file to .afl-extra/config/plugin-renamer-config.php and update
 * the values to match your plugin's naming scheme.
 *
 * Important:
 * - The keys in the 'name_list' array are used as placeholders in the plugin renamer process. Do not change the array keys, only update the values.
 * - The 'merge_tags' array is used for additional replacements that are not part of the main naming scheme. You can add any custom tags you want to replace in your plugin files.
 * - Make sure to keep the format of the values consistent with the expected naming conventions (e.g., PascalCase for class names, lowercase with underscores for constants, etc.) to ensure the plugin renamer works correctly.
 *
 * For meta_prefix, meta_prefix_public, option_key and option_key_prefix:
 * - Use the abbreviation of your plugin name (e.g., 'aflpb' for 'AFL Plugin Boilerplate') and follow the specified format to avoid conflicts with other plugins and to maintain a consistent naming convention.
 * - Make sure that the abbreviation is having a minimum of 5 characters and maximum of 12 characters. If the abbreaviation is too short, use an appropriate word from the plugin name to make it minimum 5 characters.
 * - The meta_prefix is typically used for custom field keys in the database, so it should start with an underscore and end with an underscore to indicate that it's a private key.
 * - The meta_prefix_public can be used for public-facing keys that don't start with an underscore.
 * - The option_key and option_key_prefix are used for WordPress options and should follow a similar convention to avoid conflicts.
 *
 * For folder_list and file_list:
 * - The 'folder_list' and 'file_list' arrays specify where the plugin renamer should look for the placeholders.
 * - Never change the names in the 'file_list' and 'folder_list' array but you can add names to the list.
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
		'php_namespace_vendor_name'        => 'MyVendorName', // pascal case, no spaces, no symbols.
		'php_namespace_package_name'       => 'AFL_Plugin_Boilerplate',  // pascal case with underscore.
		'plugin_name'                      => 'AFL Plugin Boilerplate',  // pascal case with spaces.
		'plugin_constant_uppercase_prefix' => 'AFL_PLUGIN_BOILERPLATE_', // uppercase with underscore and ends with underscore.
		'plugin_name_lowercase_underscore' => 'afl_plugin_boilerplate',  // lowercase with underscore.
		'meta_prefix'                      => "'_aflpb_'", // keep inner single quotes, start with underscore, lowercase and ends with underscore.
		'meta_prefix_public'               => "'aflpb_'",  // keep inner single quotes, lowercase and ends with underscore.
		'option_key'                       => "'aflpb'",   // keep inner single quotes, lowercase.
		'option_key_prefix'                => "'aflpb_'",  // keep inner single quotes, lowercase and ends with underscore.
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
		'.phpcs.xml',
		'AGENTS.md',
	),
);
