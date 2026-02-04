<?php
namespace Appfromlab\Bob\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
	 * Run the phpcbf vendor-prefixed process
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		\passthru(
			'php vendor/bin/phpcbf --standard=.phpcs.xml vendor-prefixed/composer vendor-prefixed/autoload.php',
			$exit_code
		);

		// Always exit 0.
		return 0;
	}
}
