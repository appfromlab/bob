<?php
/**
 * Bob Composer Plugin
 *
 * Implements the Composer plugin interface to register the Bob command provider.
 *
 * @package Appfromlab\Bob\Composer
 */

namespace Appfromlab\Bob\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\Capable;

/**
 * Composer plugin for registering Bob commands
 *
 * Integrates with Composer to provide custom commands for managing WordPress plugins.
 */
class BobPlugin implements PluginInterface, Capable {

	/**
	 * Activate the plugin
	 *
	 * Called when the plugin is activated by Composer.
	 *
	 * @param Composer    $composer The Composer instance.
	 * @param IOInterface $io       The Composer IO interface.
	 * @return void
	 */
	public function activate( Composer $composer, IOInterface $io ) {
	}

	/**
	 * Deactivate the plugin
	 *
	 * Called when the plugin is deactivated by Composer.
	 *
	 * @param Composer    $composer The Composer instance.
	 * @param IOInterface $io       The Composer IO interface.
	 * @return void
	 */
	public function deactivate( Composer $composer, IOInterface $io ): void {
	}

	/**
	 * Uninstall the plugin
	 *
	 * Called when the plugin is uninstalled from Composer.
	 *
	 * @param Composer    $composer The Composer instance.
	 * @param IOInterface $io       The Composer IO interface.
	 * @return void
	 */
	public function uninstall( Composer $composer, IOInterface $io ): void {
	}

	/**
	 * Get plugin capabilities
	 *
	 * Returns the capabilities provided by this plugin, including the command provider.
	 *
	 * @return array Associative array of capabilities.
	 */
	public function getCapabilities() {
		return array(
			'Composer\Plugin\Capability\CommandProvider' => 'Appfromlab\Bob\Composer\CommandProvider',
		);
	}
}
