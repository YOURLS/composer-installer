<?php
/**
 * YOURLS Composer Installer
 */

namespace YOURLS\ComposerInstaller;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\Capable;

/**
 * YOURLS Composer Installer Plugin
 *
 * This class activates the plugin installer and registers the class that will add
 * custom commands
 *
 * @package   YOURLS\ComposerInstaller
 * @author    Ozh <ozh@ozh.org>
 * @link      https://github.com/yourls/composer-installer/
 * @license   MIT
 */
class Plugin implements PluginInterface, Capable
{
    /**
     * Register plugin installer with Composer
     *
     * @param Composer    $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new PluginInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);
    }

    public function getCapabilities()
    {
        return array(
            'Composer\Plugin\Capability\CommandProvider' => 'YOURLS\ComposerInstaller\CommandProvider',
        );
    }

    /**
     * Remove any hooks from Composer
     *
     * @codeCoverageIgnore
     *
     * @param \Composer\Composer $composer
     * @param \Composer\IO\IOInterface $io
     * @return void
     */
    public function deactivate(Composer $composer, IOInterface $io)
    {
        // nothing to do
    }

    /**
     * Prepare the plugin to be uninstalled
     *
     * @codeCoverageIgnore
     *
     * @param Composer $composer
     * @param IOInterface $io
     * @return void
     */
    public function uninstall(Composer $composer, IOInterface $io)
    {
        // nothing to do
    }
}
