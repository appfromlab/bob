<?php

namespace Appfromlab\Bob\Composer;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Appfromlab\Bob\Command\HelloCommand;

class CommandProvider implements CommandProviderCapability {

	public function getCommands() {
		return array( new HelloCommand() );
	}
}
