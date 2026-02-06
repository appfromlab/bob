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

		$process = new Process(
			array(
				'php',
				$config['paths']['plugin_vendor_dir'] . 'bin/phpcbf',
				'--standard=' . $config['paths']['plugin_dir'] . '.phpcs.xml',
				$config['paths']['plugin_vendor_prefixed_dir'] . 'composer',
				$config['paths']['plugin_vendor_prefixed_dir'] . 'autoload.php',
			)
		);

		$output->writeln( 'Running: ' . $process->getCommandLine() );

		$process->run(
			function ( $type, $buffer ) use ( $output ) {
				$output->write( $buffer );
			}
		);

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		// Always exit 0 (force success because it may fail for Github Action).
		return 0;
	}
}
