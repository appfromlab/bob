<?php
/**
 * PHP-Scoper Scope Command
 *
 * Runs PHP-Scoper to prefix all dependencies in the vendor directory
 * to avoid conflicts with plugins that may have the same dependencies.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Appfromlab\Bob\HelperScoper;
use Appfromlab\Bob\Composer\BatchCommands;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Composer\Command\BaseCommand;

/**
 * Execute PHP-Scoper on vendor folder
 */
class ScopeCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:scope' )
		->setDescription( 'Perform php-scoper on vendor folder.' );
	}

	/**
	 * Execute the command
	 *
	 * Runs PHP-Scoper to prefix vendor dependencies.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		$commands = array(
			new ArrayInput( array( 'command' => 'afl:bob:delete-scoper-build' ) ),
			new ArrayInput( array( 'command' => 'afl:bob:delete-vendor-prefixed' ) ),
		);

		BatchCommands::run( $this->getApplication(), $commands, $output );

		// Get configuration.
		$config = Helper::getConfig();

		$scoper_bin_path      = $config['paths']['plugin_vendor_dir'] . 'bin/php-scoper';
		$scoper_config_1_path = $config['paths']['plugin_dir'] . '.scoper.1.php';
		$scoper_config_2_path = $config['paths']['plugin_dir'] . '.scoper.2.php';

		// Copy files into .afl-scoper-build for scoping.
		$source_path            = $config['paths']['plugin_dir'];
		$destination_path       = $config['paths']['plugin_scoper_build_dir'];
		$exclude_from_file_path = $config['paths']['plugin_scoper_ignore_file'];

		if ( ! file_exists( $scoper_bin_path ) ) {
			$output->writeln( '<error>PHP-Scoper binary not found at ' . $scoper_bin_path . '</error>' );
			return 1;
		}

		if ( ! file_exists( $scoper_config_1_path ) ) {
			$output->writeln( '<error>PHP-Scoper Stage 1 config file not found at ' . $scoper_config_1_path . '</error>' );
			return 1;
		}

		if ( ! file_exists( $scoper_config_2_path ) ) {
			$output->writeln( '<error>PHP-Scoper Stage 2 config file not found at ' . $scoper_config_2_path . '</error>' );
			return 1;
		}

		if ( ! file_exists( $exclude_from_file_path ) ) {
			$output->writeln( '<error>.scoperignore file not found at ' . $exclude_from_file_path . '</error>' );
			return 1;
		}

		if ( Helper::copyDirectory(
			$source_path,
			$destination_path,
			array(
				'cwd'          => $config['paths']['plugin_dir'],
				'exclude_from' => $exclude_from_file_path,
			)
		) ) {
			$output->writeln( 'Files copied to ../.afl-scoper-build folder.' );
		} else {
			$output->writeln( '<error>Failed to copy files to ../.afl-scoper-build folder.</error>' );
			return 1;
		}

		$commands = array(
			new Process(
				// Run PHP-Scoper with Stage 1 configuration to prefix vendor dependencies.
				array(
					'php',
					$scoper_bin_path,
					'add-prefix',
					'--config=' . $scoper_config_1_path,
				),
				// set current working directory.
				$config['paths']['plugin_dir']
			),
			new Process(
				// go into .afl-scoper-build dir and dump autoload there to generate new optimized autoload files for the prefixed vendor.
				array(
					'composer',
					'dump-autoload',
					'--no-dev',
					'--no-scripts',
				),
				// set current working directory.
				$config['paths']['plugin_scoper_build_dir']
			),
			new Process(
				// Run PHP-Scoper with Stage 2 configuration to prefix vendor/composer folder.
				array(
					'php',
					$scoper_bin_path,
					'add-prefix',
					'--config=' . $scoper_config_2_path,
				),
				// set current working directory.
				$config['paths']['plugin_dir']
			),
		);

		$exit_code = BatchCommands::run( $this->getApplication(), $commands, $output );

		if ( 0 !== $exit_code ) {
			$output->writeln( '<error>Failed to process PHP-Scoper.</error>' );
			return $exit_code;
		}

		// Copy the scoped vendor files from .afl-scoper-build/<plugin_name>/vendor-prefixed/ to the plugin directory.
		$source_vendor_prefixed_path      = $config['paths']['plugin_scoper_build_dir'] . 'vendor-prefixed' . DIRECTORY_SEPARATOR;
		$destination_vendor_prefixed_path = $config['paths']['plugin_dir'] . 'vendor-prefixed' . DIRECTORY_SEPARATOR;

		if ( Helper::copyDirectory(
			$source_vendor_prefixed_path,
			$destination_vendor_prefixed_path,
			array(
				'cwd' => $config['paths']['plugin_dir'],
			)
		) ) {
			$output->writeln( 'Files copied to ./vendor-prefixed folder.' );
		} else {
			$output->writeln( '<error>Failed to copy files to ./vendor-prefixed folder.</error>' );
			return 1;
		}

		// Run PHPCBF on vendor-prefixed folder and clean up build directories after scoping.
		$commands = array(
			new ArrayInput( array( 'command' => 'afl:bob:phpcbf-vendor-prefixed' ) ),
			new ArrayInput( array( 'command' => 'afl:bob:delete-scoper-build' ) ),
		);

		$exit_code = BatchCommands::run( $this->getApplication(), $commands, $output );

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
