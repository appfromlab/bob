<?php
/**
 * Bob Command Provider
 *
 * Registers and provides all available Bob commands to the Composer plugin system.
 *
 * @package Appfromlab\Bob\Composer
 */

namespace Appfromlab\Bob\Composer;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Appfromlab\Bob\Command\BuildCommand;
use Appfromlab\Bob\Command\BumpVersionCommand;
use Appfromlab\Bob\Command\DeleteComposerLockCommand;
use Appfromlab\Bob\Command\DeleteVendorPrefixedCommand;
use Appfromlab\Bob\Command\FirstTimeCommand;
use Appfromlab\Bob\Command\HelloCommand;
use Appfromlab\Bob\Command\InstallBinCommand;
use Appfromlab\Bob\Command\MakePotCommand;
use Appfromlab\Bob\Command\PhpcbfCommand;
use Appfromlab\Bob\Command\PhpcbfVendorPrefixedCommand;
use Appfromlab\Bob\Command\PluginRenamerCommand;
use Appfromlab\Bob\Command\PluginRenamerCopyConfigCommand;
use Appfromlab\Bob\Command\ReadmeGeneratorCommand;
use Appfromlab\Bob\Command\ReleaseCommand;
use Appfromlab\Bob\Command\RequireDevGlobalCommand;
use Appfromlab\Bob\Command\ScopeCommand;

/**
 * Command provider for registering Bob commands
 *
 * Implements the Composer command provider capability to register all available commands.
 */
class CommandProvider implements CommandProviderCapability {

	/**
	 * Get available commands
	 *
	 * Returns all registered Bob commands for use in the Composer command factory.
	 *
	 * @return array Array of command instances.
	 */
	public function getCommands() {
		return array(
			new BuildCommand(),
			new BumpVersionCommand(),
			new DeleteComposerLockCommand(),
			new DeleteVendorPrefixedCommand(),
			new FirstTimeCommand(),
			new HelloCommand(),
			new InstallBinCommand(),
			new MakePotCommand(),
			new PhpcbfCommand(),
			new PhpcbfVendorPrefixedCommand(),
			new PluginRenamerCommand(),
			new PluginRenamerCopyConfigCommand(),
			new ReadmeGeneratorCommand(),
			new ReleaseCommand(),
			new RequireDevGlobalCommand(),
			new ScopeCommand(),
		);
	}
}
