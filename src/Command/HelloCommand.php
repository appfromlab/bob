<?php
namespace Appfromlab\Bob\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

class HelloCommand extends BaseCommand {

	protected function configure(): void {
		$this->setName( 'afl-bob:hello' )
			->setDescription( 'Say hello' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( 'Hello there...' );

		return 0;
	}
}
