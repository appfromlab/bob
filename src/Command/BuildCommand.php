<?php
namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Composer\Command\BaseCommand;

class BuildCommand extends BaseCommand {

	protected function configure(): void {
		$this->setName( 'afl:build' )
			->setDescription( 'Perform build process.' );
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

		$commands = array(
			array( 'composer', 'dump-autoload', '--no-dev' ),
			new DeleteVendorPrefixedCommand(),
			new ScopeCommand(),
			new PhpcbfVendorPrefixedCommand(),
			array( 'composer', 'dump-autoload' ),
		);

		foreach ( $commands as $command_args ) {

			if ( is_a( $command_args, BaseCommand::class ) ) {

				$output->writeln( '' );

				$return_code = $command_args->execute( $input, $output );

				if ( 0 !== $return_code ) {
					$output->writeln( '<error>ERROR: Command failed - ' . $command_args->getName() . '</error>' );
					return 1;
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
					$output->writeln( '<error>ERROR: Command failed - ' . $process->getCommandLine() . '</error>' );
					return 1;
				}
			}
		}

		// Change to previous working directory.
		chdir( $previous_cwd );

		$output->writeln( '' );
		$output->writeln( '<info>------ END ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return 0;
	}
}
