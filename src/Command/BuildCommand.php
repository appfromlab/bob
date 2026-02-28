<?php
/**
 * Build Command
 *
 * Orchestrates the plugin build process including autoload dumping, vendor prefixing,
 * code beautification, and dependency resolution.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Appfromlab\Bob\Composer\BatchCommands;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Execute complete plugin build process
 */
class BuildCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description for the Composer console.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:build' )
			->setDescription( 'Generate optimized composer autoloader files and perform PHP-Scoper on vendor folder.' );
	}

	/**
	 * Execute the command
	 *
	 * Performs the complete plugin build process including autoload dumping, vendor prefixing,
	 * code beautification, and dependency resolution.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success, non-zero for failure).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		// Get configuration.
		$config = Helper::getConfig();

		$commands = array(
			new ArrayInput( array( 'command' => 'afl:bob:delete-scoper-build' ) ),
			new ArrayInput( array( 'command' => 'afl:bob:delete-vendor-prefixed' ) ),
			new ArrayInput( array( 'command' => 'afl:bob:scope', '--config' => $config['paths']['plugin_dir'] . '.scoper.1.php' ) ),
			new Process(
				// go into .afl-scoper-build dir and dump autoload there to generate optimized autoload files for the prefixed vendor.
				array(
					'composer',
					'dump-autoload',
					'--no-dev',
					'--no-scripts',
				),
				// set current working directory.
				$config['paths']['plugin_scoper_build_dir']
			),
			new ArrayInput( array( 'command' => 'afl:bob:scope', '--config' => $config['paths']['plugin_dir'] . '.scoper.2.php' ) ),
			new Process(
				// copy the prefixed plugin code from .afl-scoper-build/vendor-prefixed to plugin dir, overwriting existing files.
				array(
					'rsync',
					'-a',
					$config['paths']['plugin_scoper_build_dir'] . 'vendor-prefixed' . DIRECTORY_SEPARATOR,
					$config['paths']['plugin_dir'] . 'vendor-prefixed' . DIRECTORY_SEPARATOR,
				),
				// change current working directory to plugin dir.
				$config['paths']['plugin_dir']
			),
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
