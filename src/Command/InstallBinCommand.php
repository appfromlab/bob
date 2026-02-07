<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Appfromlab\Bob\Composer\BatchCommands;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

class InstallBinCommand extends BaseCommand {

	protected function configure(): void {
		$this->setName( 'afl:install-bin' )
			->setDescription( 'Install required bin files for local development.' );
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

		// Create .bin directory if it does not exist.
		if ( ! is_dir( $config['paths']['plugin_bin_dir'] ) ) {
			mkdir( $config['paths']['plugin_bin_dir'], 0774, true );
		}

		$commands = array(
			array( 'curl -L https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o ./.bin/wp-cli.phar' ),
			array( 'curl -L https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar.asc -o ./.bin/wp-cli.phar.asc' ),
			array( 'curl -L https://raw.githubusercontent.com/wp-cli/builds/gh-pages/wp-cli.pgp -o ./.bin/wp-cli.pgp | gpg --import ./.bin/wp-cli.pgp' ),
			array( 'gpg --verify ./.bin/wp-cli.phar.asc ./.bin/wp-cli.phar || exit 1' ),
		);

		$exit_code = BatchCommands::run( $commands, $input, $output );

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
