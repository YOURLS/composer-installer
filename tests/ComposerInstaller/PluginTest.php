<?php

namespace YOURLS\ComposerInstaller;

use PHPUnit\Framework\TestCase;

use Composer\Composer;
use Composer\Config;
use Composer\Installer\InstallationManager;
use Composer\IO\NullIO;

class PluginTest extends TestCase
{
    /**
     * Test if the composer plugin loads as expected
     */
    public function testActivate()
    {
        $composer            = new Composer();
        $installationManager = new InstallationManager();
        $composer->setInstallationManager($installationManager);
        $composer->setConfig(new Config());

        $plugin = new Plugin();
        $plugin->activate($composer, new NullIO());

        $installer = $installationManager->getInstaller('yourls-plugin');
        $this->assertInstanceOf(PluginInstaller::class, $installer);
    }

    public function testCapabilities()
    {
        $plugin = new Plugin();

        $this->assertSame(
            ['Composer\Plugin\Capability\CommandProvider' => 'YOURLS\ComposerInstaller\CommandProvider'],
            $plugin->getCapabilities()
        );
    }


}
