<?php
/**
 * Batch Commands Executor
 *
 * Executes a series of Composer commands and shell processes sequentially,
 * managing working directory context and error handling.
 *
 * @package Appfromlab\Bob\Composer
 */

namespace Appfromlab\Bob\Composer;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use function chdir;
use function getcwd;
use function is_a;

/**
 * Batch command executor
 *
 * Executes multiple commands (both Composer commands and shell processes) in sequence,
 * handling directory changes and error handling.
 */
class BatchCommands {

	/**
	 * Execute a series of commands
	 *
	 * Runs multiple commands (Composer BaseCommand instances or shell commands as arrays)
	 * in sequence with proper error handling and working directory management.
	 *
	 * @param Application           $application Instance of application.
	 * @param array<InputInterface> $commands Array of commands to execute.
	 * @param OutputInterface       $output   Output interface for displaying results.
	 * @return int Exit code (0 for success, non-zero for failure).
	 */
	public static function run( Application $application, array $commands, OutputInterface $output ): int {

		$exit_code = 0;

		// Get configuration.
		$config = Helper::getConfig();

		try {

			$application->setAutoExit( false );

			foreach ( $commands as $single_command ) {

				if ( is_a( $single_command, 'Symfony\Component\Console\Input\ArrayInput' ) ) {

					$output->writeln( '<info>Command:</info> ' . (string) $single_command );

					$exit_code = $application->run( $single_command, $output );

					if ( 0 !== $exit_code ) {
						throw new \Error( '<error>Command Failed:</error> ' . (string) $single_command, $exit_code );
					}
				} elseif ( is_a( $single_command, 'Symfony\Component\Process\Process' ) ) {

					$output->writeln( '<info>Process:</info> ' . $single_command->getCommandLine() );
					$output->writeln( '' );

					$exit_code = $single_command->run(
						function ( $type, $buffer ) use ( $output ) {
							$output->write( $buffer );
						}
					);

					$env = $single_command->getEnv();

					if ( is_array( $env ) && array_key_exists( 'AFL_BOB_FORCE_EXIT_0', $env ) ) {
						$exit_code = 0;
					} elseif ( ! $single_command->isSuccessful() ) {
						throw new \Error( '<error>Process Failed:</error> ' . $single_command->getCommandLine(), $exit_code );
					}
				} else {
					throw new \Error( '<error>Command is not of type ArrayInput or Process.</error>', 1 );
				}
			}
		} catch ( \Throwable $th ) {

			$output->writeln( $th->getMessage() );

			return $th->getCode();
		}

		return $exit_code;
	}
}
