<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
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

		// Change to plugin directory to ensure correct paths.
		$previous_cwd = getcwd();
		chdir( $config['paths']['plugin_dir'] );

		$process = new Process(
			array(
				'php',
				$config['paths']['plugin_vendor_dir'] . 'bin/php-scoper',
				'add-prefix',
				'--config=' . $config['paths']['plugin_dir'] . '.scoper.inc.php',
			)
		);

		$output->writeln( 'Running: ' . $process->getCommandLine() );

		$process->run(
			function ( $type, $buffer ) use ( $output ) {
				$output->write( $buffer );
			}
		);

		if ( ! $process->isSuccessful() ) {
			$output->writeln( '<error>ERROR: Failed to run php-scoper</error>' );
			return 1;
		}

		// Change to previous working directory.
		chdir( $previous_cwd );

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return 0;
	}
}
