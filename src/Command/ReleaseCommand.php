<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Composer\Command\BaseCommand;

class ReleaseCommand extends BaseCommand {

	protected function configure(): void {
		$this->setName( 'afl:release' )
			->setDescription( 'Perform the release process which builds the code, bump version, generate readme.txt and make-pot.' );
	}

	/**
	 * Execute the command
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '<info>------ START ' . __CLASS__ . '</info>' );

		$config = Helper::getConfig();

		$commands = array(
			array( 'composer', 'afl:build' ),
			array( 'composer', 'afl:bump-version' ),
			array( 'composer', 'afl:readme-generator' ),
			array( 'composer', 'afl:make-pot' ),
		);

		foreach ( $commands as $command_args ) {
			$process = new Process( $command_args );

			$output->writeln( '<info>Running: ' . $process->getCommandLine() . '</info>' );

			$process->run(
				function ( $type, $buffer ) use ( $output ) {
					$output->write( $buffer );
				}
			);

			if ( ! $process->isSuccessful() ) {
				$output->writeln( '<error>ERROR: Command failed - ' . $process->getCommandLine() . '</error>' );
				return 1;
			}
		}

		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );

		return 0;
	}
}
