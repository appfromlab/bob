<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Appfromlab\Bob\Composer\BatchCommands;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

class ScopeCommand extends BaseCommand {

	protected function configure(): void {
		$this->setName( 'afl:scope' )
			->setDescription( 'Perform php-scoper on vendor folder.' );
	}

	/**
	 * Execute the command
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '<info>------ START ' . __CLASS__ . '</info>' );

		// Get configuration.
		$config = Helper::getConfig();

		$commands = array(
			array(
				'php',
				$config['paths']['plugin_vendor_dir'] . 'bin/php-scoper',
				'add-prefix',
				'--config=' . $config['paths']['plugin_dir'] . '.scoper.inc.php',
			),
		);

		$exit_code = BatchCommands::run( $commands, $input, $output );

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
