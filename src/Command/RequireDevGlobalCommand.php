<?php
/**
 * Require Development Global Composer Packages Command
 *
 * Installs global Composer development packages required for plugin development,
 * including WordPress coding standards and PHP-Scoper.
 *
 * @package Appfromlab\Bob\Command
 */

namespace Appfromlab\Bob\Command;

use Appfromlab\Bob\Composer\BatchCommands;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Install global development composer packages
 */
class RequireDevGlobalCommand extends BaseCommand {

	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	/**
	 * Configure the command
	 *
	 * Sets the command name and description.
	 *
	 * @return void
	 */
	protected function configure(): void {
		$this->setName( 'afl:bob:require-dev-global' )
			->setDescription( 'Add global composer packages for development.' );
	}

	/**
	 * Execute the command
	 *
	 * Installs required global development composer packages.
	 *
	 * @param InputInterface  $input  The input interface.
	 * @param OutputInterface $output The output interface.
	 * @return int Exit code (0 for success, non-zero for failure).
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {

		$output->writeln( '' );
		$output->writeln( '<info>------ [START] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		$commands = array(
			new ArrayInput(
				array(
					'command' => 'global',
					array( 'config', 'allow-plugins.dealerdirect/phpcodesniffer-composer-installer', 'true' ),
				)
			),
			new ArrayInput(
				array(
					'command' => 'global',
					array( 'require', '--dev', 'wp-coding-standards/wpcs:~3.0' ),
				)
			),
			new ArrayInput(
				array(
					'command' => 'global',
					array( 'require', '--dev', 'humbug/php-scoper:0.18.*' ),
				)
			),
		);

		$exit_code = BatchCommands::run( $this->getApplication(), $commands, $output );

		$output->writeln( '' );
		$output->writeln( '<info>--- [END] ' . __CLASS__ . '</info>' );
		$output->writeln( '' );

		return $exit_code;
	}
}
