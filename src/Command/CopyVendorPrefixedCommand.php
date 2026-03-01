<?php
/**
 * Copy Vendor Prefixed Directory Command
 *
 * Copies the vendor-prefixed directory from the scoper build folder to the plugin directory.
 * Uses PHP built-in functions for cross-platform support (Windows, macOS, Linux).
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Copy vendor-prefixed directory from scoper build to plugin directory
 */
class CopyVendorPrefixedCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:copy-vendor-prefixed' )
			->setDescription( 'Copy vendor-prefixed folder from scoper build directory to plugin directory.' );
	}

	/**
	 * Execute the command
	 *
	 * Copies the vendor-prefixed directory from the scoper build folder to the plugin directory
	 * using PHP built-in functions to ensure cross-platform compatibility.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success, non-zero for failure).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		$config = Helper::getConfig();

		$src  = $config['paths']['plugin_scoper_build_dir'] . 'vendor-prefixed';
		$dest = $config['paths']['plugin_vendor_prefixed_dir'];

		$output->writeln( "Copying: {$src}" );
		$output->writeln( "To:      {$dest}" );

		if ( ! Helper::copyDirectory( $src, $dest ) ) {
			$output->writeln( '<error>ERROR: Failed to copy vendor-prefixed directory.</error>' );
			return 1;
		}

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return 0;
	}
}
