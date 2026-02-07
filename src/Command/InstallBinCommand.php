<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Composer\Command\BaseCommand;
use function chdir;
use function getcwd;
use function is_a;
use function is_dir;
use function mkdir;

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

		// Change to plugin directory to ensure correct paths.
		$previous_cwd = getcwd();
		chdir( $config['paths']['plugin_dir'] );

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

		try {

			foreach ( $commands as $command_args ) {

				if ( is_a( $command_args, BaseCommand::class ) ) {

					$output->writeln( '' );

					$return_code = $command_args->execute( $input, $output );

					if ( 0 !== $return_code ) {
						throw new \Error( 'Command failed - ' . $command_args->getName(), $return_code );
					}
				} elseif ( is_array( $command_args ) ) {

					$process = new Process( $command_args );

					$output->writeln( '<info>Process: ' . $process->getCommandLine() . '</info>' );

					$process->run(
						function ( $type, $buffer ) use ( $output ) {
							$output->write( $buffer );
						}
					);

					if ( ! $process->isSuccessful() ) {
						throw new \Error( 'Command failed - ' . $command_args->getName(), 1 );
					}
				}
			}
		} catch ( \Throwable $th ) {

			$output->writeln( '<error>' . $th->getMessage() . '</error>' );

			return $th->getCode();
		} finally {

			// Change to previous working directory.
			chdir( $previous_cwd );
		}

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return 0;
	}
}
