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
use Composer\Command\BaseCommand;
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
	 * @param array           $commands Array of commands to execute.
	 * @param InputInterface  $input    Input interface for the commands.
	 * @param OutputInterface $output   Output interface for displaying results.
	 * @return int Exit code (0 for success, non-zero for failure).
	 */
	public static function run( array $commands, InputInterface $input, OutputInterface $output ): int {

		// Get configuration.
		$config = Helper::getConfig();

		// Change to plugin directory to ensure correct paths.
		$previous_cwd = getcwd();
		chdir( $config['paths']['plugin_dir'] );

		try {

			foreach ( $commands as $command_args ) {

				if ( is_a( $command_args, BaseCommand::class ) ) {

					$output->writeln( '' );

					$exit_code = $command_args->execute( $input, $output );

					if ( 0 !== $exit_code ) {
						throw new \Error( 'Command failed - ' . $command_args->getName(), $exit_code );
					}
				} elseif ( is_array( $command_args ) ) {

					$process = new Process( $command_args );

					$output->writeln( '<info>Process: ' . $process->getCommandLine() . '</info>' );

					$exit_code = $process->run(
						function ( $type, $buffer ) use ( $output ) {
							$output->write( $buffer );
						}
					);

					if ( ! $process->isSuccessful() ) {
						throw new \Error( 'Command failed - ' . $process->getCommandLine(), $exit_code );
					}
				}
			}
		} catch ( \Throwable $th ) {

			$output->writeln( '<error>' . $th->getMessage() . '</error>' );

			return $th->getCode();
		} finally {
			// Change to previous working directory.
			chdir( $previous_cwd );
		}

		return 0;
	}
}
