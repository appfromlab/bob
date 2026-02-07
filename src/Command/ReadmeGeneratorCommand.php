<?php
/**
 * Plugin Readme Generator Command
 *
 * Generates a WordPress plugin readme.txt file by combining plugin headers
 * with content sections from the .afl-extra/readme folder.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Plugin readme.txt generator
 *
 * It will compile a WordPress plugin readme.txt file using
 * the plugin headers data and content in .plugin-readme folder.
 */
class ReadmeGeneratorCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:readme-generator' )
			->setDescription( 'Generate plugin readme.txt based from individual files from the .afl-extra/readme folder.' );
	}

	/**
	 * Execute the command
	 *
	 * Generates the plugin readme.txt file from templates and configuration.
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

		$plugin_headers = Helper::getPluginHeaders( $config['paths']['plugin_file'] );

		$content = sprintf( '=== %1$s ===', $plugin_headers['Plugin Name'] ) . "\n";

		$must_have_headers = array(
			'Author'            => true,
			'Author URI'        => true,
			'Stable Tag'        => true,
			'Requires at least' => true,
			'Tested up to'      => true,
			'Requires PHP'      => true,
			'License'           => true,
			'License URI'       => false,
			'Tags'              => false,
		);

		foreach ( $must_have_headers as $tmp_header_name => $tmp_must_have ) {

			if ( $tmp_must_have && empty( $plugin_headers[ $tmp_header_name ] ) ) {
				$output->writeln( '<error>ERROR: Invalid plugin headers - ' . $tmp_header_name . '</error>' );
				return 1;
			}

			if ( strlen( $plugin_headers[ $tmp_header_name ] ) > 0 ) {
				$content .= sprintf(
					'%1$s: %2$s' . "\n",
					trim( $tmp_header_name ),
					trim( $plugin_headers[ $tmp_header_name ] )
				);
			}
		}

		// contributors.
		$contributors_file_path = $config['paths']['plugin_extra_readme_dir'] . 'contributors.md';

		if ( file_exists( $contributors_file_path ) ) {
			$list_of_contributors = file_get_contents( $contributors_file_path );
			$list_of_contributors = trim( $list_of_contributors );

			if ( '= EMPTY =' !== $list_of_contributors && strlen( $list_of_contributors ) > 0 ) {
				$content .= 'Contributors: ' . $list_of_contributors . "\n";
			}
		}

		// plugin short description.
		$content .= "\n" . $plugin_headers['Description'];

		$content_sections = array(
			'description',
			'installation',
			'changelog',
		);

		foreach ( $content_sections as $tmp_section ) {
			$content .= "\n\n" . '=== ' . ucfirst( $tmp_section ) . ' ===';

			$tmp_file_md = $config['paths']['plugin_extra_readme_dir'] . $tmp_section . '.md';

			if ( file_exists( $tmp_file_md ) ) {
				$tmp_section_content = file_get_contents( $tmp_file_md );
				$tmp_section_content = trim( $tmp_section_content );

				if ( '= EMPTY =' !== $tmp_section_content && strlen( $tmp_section_content ) > 0 ) {
					$content .= "\n\n" . $tmp_section_content;
				}
			}
		}

		$content .= "\n";

		if ( 'readme.txt' === basename( $config['paths']['plugin_readme_file'] ) ) {
			file_put_contents( $config['paths']['plugin_readme_file'], $content );

			$output->writeln( '<info>SUCCESS: readme.txt generated.</info>' );
		} else {
			$output->writeln( '<error>ERROR: readme.txt not found.</error>' );
		}

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return 0;
	}
}
