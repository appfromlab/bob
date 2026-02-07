<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Composer\BatchCommands;
use Appfromlab\Bob\Command\BumpVersionCommand;
use Appfromlab\Bob\Command\MakePotCommand;
use Appfromlab\Bob\Command\ReadmeGeneratorCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

		$commands = array(
			new BuildCommand(),
			new BumpVersionCommand(),
			new ReadmeGeneratorCommand(),
			new MakePotCommand(),
		);

		$exit_code = BatchCommands::run( $commands, $input, $output );

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
