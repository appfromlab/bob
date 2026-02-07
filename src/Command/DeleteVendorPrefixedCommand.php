<?php
/**
 * Delete Vendor Prefixed Directory Command
 *
 * Removes the vendor-prefixed directory from the WordPress plugin folder.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Delete vendor-prefixed directory
 */
class DeleteVendorPrefixedCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:delete-vendor-prefixed' )
			->setDescription( 'Delete vendor-prefixed folder in the WordPress plugin folder.' );
	}

	/**
	 * Execute the command
	 *
	 * Deletes the vendor-prefixed directory from the plugin.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		$config = Helper::getConfig();

		if ( file_exists( $config['paths']['plugin_vendor_prefixed_dir'] ) ) {

			if ( ! Helper::safeToDelete( $config['paths']['plugin_vendor_prefixed_dir'], 'vendor-prefixed', $config['paths']['plugin_vendor_prefixed_dir'] ) ) {
				$output->writeln( '<warning>WARNING: vendor-prefixed folder cannot be deleted.</warning>' );
			}
		}

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		// can continue.
		return 0;
	}
}
