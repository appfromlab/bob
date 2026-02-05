<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Composer\Command\BaseCommand;

/**
 * Beautify the vendor-prefixed composer folder
 *
 * Force success on exit because it will fail for Github Action.
 */
class PhpcbfVendorPrefixedCommand extends BaseCommand {

	protected function configure(): void {
		$this->setName( 'afl:phpcbf-vendor-prefixed' )
			->setDescription( 'Beautify vendor-prefixed composer folder.' );
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

		// Change to parent directory to ensure correct paths.
		chdir( $config['plugin_dir'] );

		$process = new Process(
			array(
				'php',
				'vendor/bin/phpcbf',
				'--standard=.phpcs.xml',
				'vendor-prefixed/composer',
				'vendor-prefixed/autoload.php',
			)
		);

		$output->writeln( 'Running: ' . $process->getCommandLine() );

		$process->run(
			function ( $type, $buffer ) use ( $output ) {
				$output->write( $buffer );
			}
		);

		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );

		// Always exit 0 (force success because it may fail for Github Action).
		return 0;
	}
}
