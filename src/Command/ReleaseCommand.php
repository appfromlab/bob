<?php
/**
 * Plugin Release Command
 *
 * Orchestrates the complete plugin release process including building,
 * bumping version, generating readme, and creating language files.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Composer\BatchCommands;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Execute complete release workflow
 */
class ReleaseCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:release' )
			->setDescription( 'Perform the release process which builds the code, bump version, generate readme.txt and make-pot.' );
	}

	/**
	 * Execute the command
	 *
	 * Executes the complete release workflow.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success, non-zero for failure).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		$commands = array(
			new ArrayInput( array( 'command' => 'afl:bob:build' ) ),
			new ArrayInput( array( 'command' => 'afl:bob:bump-version' ) ),
			new ArrayInput( array( 'command' => 'afl:bob:readme-generator' ) ),
			new ArrayInput( array( 'command' => 'afl:bob:make-pot' ) ),
		);

		$exit_code = BatchCommands::run( $this->getApplication(), $commands, $output );

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
