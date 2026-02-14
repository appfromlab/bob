<?php
/**
 * PHP CodeSniffer Beautifier for Vendor Prefixed Command
 *
 * Applies PHP CodeSniffer beautification rules to the vendor-prefixed directory
 * to ensure consistent code formatting.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Appfromlab\Bob\Composer\BatchCommands;
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
		$this->setName( 'afl:bob:phpcbf-vendor-prefixed' )
			->setDescription( 'Beautify only the vendor-prefixed composer folder and autoload.php.' );
	}

	/**
	 * Execute the command
	 *
	 * Beautifies code in the vendor-prefixed directory using PHP CodeSniffer.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (always 0 for success).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		// Get configuration.
		$config = Helper::getConfig();

		$commands = array(
			new Process(
				array(
					'php',
					$config['paths']['plugin_vendor_dir'] . 'bin/phpcbf',
					'--standard=' . $config['paths']['plugin_dir'] . '.phpcs.xml',
					$config['paths']['plugin_vendor_prefixed_dir'] . 'composer',
					$config['paths']['plugin_vendor_prefixed_dir'] . 'autoload.php',
				),
				$config['paths']['plugin_dir'],
				array(
					'AFL_BOB_EXIT_0' => true,
				),
			),
		);

		$exit_code = BatchCommands::run( $this->getApplication(), $commands, $output );

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		// Always exit 0 (force success because it may fail for Github Action).
		return 0;
	}
}
