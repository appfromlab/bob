<?php
namespace Appfromlab\Bob\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\Capable;

class BobPlugin implements PluginInterface, Capable {

	public function activate( Composer $composer, IOInterface $io ) {
	}

	public function deactivate( Composer $composer, IOInterface $io ): void {
	}

	public function uninstall( Composer $composer, IOInterface $io ): void {
	}

	public function getCapabilities() {
		return array(
			'Composer\Plugin\Capability\CommandProvider' => 'Appfromlab\Bob\Composer\CommandProvider',
		);
	}
}
