<?php
/**
 * Lint Command
 *
 * Formats staged PHP files with PHPCBF then checks them with PHPCS.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Appfromlab\Bob\Composer\BatchCommands;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Composer\Command\BaseCommand;

/**
 * Format and lint staged PHP files
 *
 * First formats staged PHP files with PHPCBF, then checks them with PHPCS.
 */
class LintCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description for the Composer console.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:lint' )
			->setDescription( 'Format and check staged PHP files with PHPCBF and PHPCS.' );
	}

	/**
	 * Execute the command
	 *
	 * Retrieves staged PHP files, formats them with PHPCBF, then checks them with PHPCS.
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

		// Get staged PHP files.
		$staged_files = $this->getStagedPhpFiles( $config['paths']['plugin_dir'], $output );

		if ( empty( $staged_files ) ) {
			$output->writeln( '<info>No staged PHP files found.</info>' );
			$output->writeln( '' );
			$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
			$output->writeln( '' );
			return 0;
		}

		$output->writeln( '<info>Staged PHP files:</info>' );
		foreach ( $staged_files as $file ) {
			$output->writeln( '  ' . $file );
		}
		$output->writeln( '' );

		// Resolve binary paths from the plugin's vendor directory.
		$phpcbf_binary  = $config['paths']['plugin_vendor_dir'] . 'bin' . DIRECTORY_SEPARATOR . 'phpcbf';
		$phpcs_binary   = $config['paths']['plugin_vendor_dir'] . 'bin' . DIRECTORY_SEPARATOR . 'phpcs';
		$phpcs_standard = '--standard=' . $config['paths']['plugin_dir'] . '.phpcs.xml';

		// Step 1: Format staged PHP files with PHPCBF.
		$phpcbf_args = array_merge(
			array( 'php', $phpcbf_binary, $phpcs_standard ),
			$staged_files
		);

		// Step 2: Check staged PHP files with PHPCS.
		$phpcs_args = array_merge(
			array( 'php', $phpcs_binary, $phpcs_standard ),
			$staged_files
		);

		$commands = array(
			// AFL_BOB_FORCE_EXIT_0 forces BatchCommands to treat the exit code as 0.
			// PHPCBF exits with 1 when it successfully applies fixes, which would
			// otherwise stop the pipeline. We always continue to the PHPCS check.
			new Process(
				$phpcbf_args,
				$config['paths']['plugin_dir'],
				array( 'AFL_BOB_FORCE_EXIT_0' => true )
			),
			new Process(
				$phpcs_args,
				$config['paths']['plugin_dir']
			),
		);

		$exit_code = BatchCommands::run( $this->getApplication(), $commands, $output );

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}

	/**
	 * Get list of staged PHP files
	 *
	 * Retrieves all staged PHP files using git diff --cached, supporting
	 * Linux, macOS and Windows (PowerShell).
	 *
	 * @param string          $working_dir The working directory to run git from.
	 * @param OutputInterface $output      The output interface.
	 * @return array Array of absolute file paths for staged PHP files.
	 */
	private function getStagedPhpFiles( string $working_dir, OutputInterface $output ): array {

		$process = new Process(
			array( 'git', 'diff', '--cached', '--name-only', '--diff-filter=ACM' ),
			$working_dir
		);

		$process->run();

		if ( ! $process->isSuccessful() ) {
			$output->writeln( '<error>Failed to get staged files: ' . $process->getErrorOutput() . '</error>' );
			return array();
		}

		$raw = trim( $process->getOutput() );

		if ( empty( $raw ) ) {
			return array();
		}

		$php_files = array();

		foreach ( explode( "\n", $raw ) as $file ) {
			$file = trim( $file );

			if ( ! empty( $file ) && pathinfo( $file, PATHINFO_EXTENSION ) === 'php' ) {
				$php_files[] = $working_dir . $file;
			}
		}

		return $php_files;
	}
}
