<?php
namespace Appfromlab\Bob\Composer;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Appfromlab\Bob\Command\BuildCommand;
use Appfromlab\Bob\Command\BumpVersionCommand;
use Appfromlab\Bob\Command\DeleteComposerLockCommand;
use Appfromlab\Bob\Command\DeleteVendorPrefixedCommand;
use Appfromlab\Bob\Command\FirstTimeCommand;
use Appfromlab\Bob\Command\HelloCommand;
use Appfromlab\Bob\Command\MakePotCommand;
use Appfromlab\Bob\Command\PhpcbfVendorPrefixedCommand;
use Appfromlab\Bob\Command\PluginRenamerCommand;
use Appfromlab\Bob\Command\PluginRenamerCopyConfigCommand;
use Appfromlab\Bob\Command\ReadmeGeneratorCommand;
use Appfromlab\Bob\Command\ReleaseCommand;
use Appfromlab\Bob\Command\ScopeCommand;

class CommandProvider implements CommandProviderCapability {

	public function getCommands() {
		return array(
			new BuildCommand(),
			new BumpVersionCommand(),
			new DeleteComposerLockCommand(),
			new DeleteVendorPrefixedCommand(),
			new FirstTimeCommand(),
			new HelloCommand(),
			new MakePotCommand(),
			new PhpcbfVendorPrefixedCommand(),
			new PluginRenamerCommand(),
			new PluginRenamerCopyConfigCommand(),
			new ReadmeGeneratorCommand(),
			new ReleaseCommand(),
			new ScopeCommand(),
		);
	}
}
