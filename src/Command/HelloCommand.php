<?php
/**
 * Hello Command
 *
 * A simple test command that demonstrates console output with various message types.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Display greeting with formatted output examples
 */
class HelloCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:hello' )
			->setDescription( 'Say hello' );
	}

	/**
	 * Execute the command
	 *
	 * Displays greeting and demonstrates various output message types.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		$output->writeln( 'Hello there...' );

		$output->writeln( '<info>This is an info message</info>' );
		$output->writeln( '<comment>This is a comment message</comment>' );
		$output->writeln( '<question>This is a question message</question>' );
		$output->writeln( '<warning>This is a warning message</warning>' );
		$output->writeln( '<error>This is an error message</error>' );

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return 0;
	}
}
