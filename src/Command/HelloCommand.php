<?php
namespace Appfromlab\Bob\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

class HelloCommand extends BaseCommand {

	protected function configure(): void {
		$this->setName( 'afl:hello' )
			->setDescription( 'Say hello' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '<info>------ START ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		$output->writeln( 'Hello there...' );

		$output->writeln( '<info>This is an info message</info>' );
		$output->writeln( '<comment>This is a comment message</comment>' );
		$output->writeln( '<question>This is a question message</question>' );
		$output->writeln( '<warning>This is a warning message</warning>' );
		$output->writeln( '<error>This is an error message</error>' );

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );

		return 0;
	}
}
