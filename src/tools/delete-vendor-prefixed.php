<?php
namespace Appfromlab\Bob\Tools;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete vendor-prefixed directory
 */
class DeleteVendorPrefixed extends Command {

	protected function configure(): void {
		$this->setName( 'Delete vendor-prefixed folder' )
			->setDescription( 'Delete vendor-prefixed folder in the WordPress plugin folder.' );
	}

	/**
	 * Run the delete vendor-prefixed process
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$config = Helper::getConfig();

		if ( file_exists( $config['paths']['plugin_vendor_prefixed_dir'] ) ) {
			$php_scoper_config = include $config['paths']['plugin_vendor_prefixed_dir'];

			if ( ! Helper::safeToDelete( $config['paths']['vendor_prefixed_dir'], $php_scoper_config['output-dir'], $config['paths']['vendor_prefixed_dir'] ) ) {
				$output->writeln( '<warning>WARNING: vendor-prefixed folder cannot be deleted.</warning>' );
			}
		}

		// can continue.
		return 0;
	}
}
