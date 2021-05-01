<?php

namespace YOURLS\ComposerInstaller;

use Composer\Package\Link;
use Composer\Package\Package;
use Composer\Repository\InstalledArrayRepository;

/**
 * The main test suite
 */
class PluginInstallerTest extends InstallerTestCase
{

    /**
     * Const used to test case when a package requires the Composer Installer (ie a YOURLS
     * plugin with a composer.json having 'yourls/composer-installer') or not (either a
     * YOURLS plugin without this, or another regular package)
     */
    const SUPPORTED  = 1;

    /**
     * Test if the composer plugin creates a vendor dir, or add to it, when it has to. Can
     * be is the package does not support the Plugin installer, or if the package adds more
     * dependencies (ie plugin in /user/plugins/ and more libs in /includes/vendor)
     */
    const VENDOR_DIR = 2;

    /**
     * Always register the plugin installer
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->installer = new PluginInstaller($this->io, $this->composer);
    }

    /**
     * Test if type 'yourls-plugin' is supported
     */
    public function testSupports()
    {
        $this->assertTrue($this->installer->supports('yourls-plugin'));
        $this->assertFalse($this->installer->supports('amazing-plugin'));
    }

    /**
     * Test if install path is correct with a package that doesn't support the plugin installer
     */
    public function testGetInstallPathNoSupport()
    {
        $package = $this->pluginPackageFactory();
        $this->assertEquals($this->testDir . '/vendor/joecool/super-plugin', $this->installer->getInstallPath($package));
    }

    /**
     * Test if install path is correct with a package that supports the plugin installer
     */
    public function testGetInstallPathDefault()
    {
        $package = $this->pluginPackageFactory(self::SUPPORTED);
        $this->assertEquals('user/plugins/super-plugin', $this->installer->getInstallPath($package));
    }

    /**
     * Test if install path is correct with a package that does not have a 'vendor/package' name
     */
    public function testGetInstallPathNoVendor()
    {
        $package = $this->pluginPackageFactory(self::SUPPORTED, 'zomg');
        $this->assertEquals('zomg', $package->getPrettyName());
        $this->assertEquals('user/plugins/zomg', $this->installer->getInstallPath($package));
    }

    /**
     * Test installation of package that does not support the plugin installer
     */
    public function testInstallNoSupport()
    {
        $package = $this->pluginPackageFactory(self::VENDOR_DIR);
        $this->assertEquals($this->testDir . '/vendor/joecool/super-plugin', $this->installer->getInstallPath($package));
        $this->installer->install(new InstalledArrayRepository(), $package);
        $this->assertFileExists($this->testDir . '/vendor/joecool/super-plugin/plugin.php');
    }

    /**
     * Test installation of package that does support the plugin installer
     */
    public function testInstallWithoutVendorDir()
    {
        $package = $this->pluginPackageFactory(self::SUPPORTED);
        $this->assertEquals('user/plugins/super-plugin', $this->installer->getInstallPath($package));
        $this->installer->install(new InstalledArrayRepository(), $package);
        $this->assertFileExists($this->testDir . '/user/plugins/super-plugin/plugin.php');
    }

    /**
     * Test update of package that does not support the plugin installer
     */
    public function testUpdateNoSupport()
    {
        $repo = new InstalledArrayRepository();

        $initial = $this->pluginPackageFactory(self::VENDOR_DIR);
        $this->assertEquals($this->testDir . '/vendor/joecool/super-plugin', $this->installer->getInstallPath($initial));
        $this->installer->install($repo, $initial);
        $repo->addPackage($initial);
        $this->assertFileExists($this->testDir . '/vendor/joecool/super-plugin/plugin.php');
        $this->assertFileExists($this->testDir . '/vendor/joecool/super-plugin/vendor-created.txt');

        $this->filesystem->emptyDirectory($this->testDir . '/vendor/joecool/super-plugin');
        $this->assertFileNotExists($this->testDir . '/vendor/joecool/super-plugin/plugin.php');
        $this->assertFileNotExists($this->testDir . '/vendor/joecool/super-plugin/vendor-created.txt');

        $target = $this->pluginPackageFactory(self::VENDOR_DIR);
        $this->assertEquals($this->testDir . '/vendor/joecool/super-plugin', $this->installer->getInstallPath($target));
        $this->installer->update($repo, $initial, $target);
        $this->assertFileExists($this->testDir . '/vendor/joecool/super-plugin/plugin.php');
        $this->assertFileExists($this->testDir . '/vendor/joecool/super-plugin/vendor-created.txt');
    }

    /**
     * Test update of package that does not support the plugin installer
     */
    public function testUpdateWithoutVendorDir()
    {
        $repo = new InstalledArrayRepository();

        $initial = $this->pluginPackageFactory(self::SUPPORTED);
        $this->assertEquals('user/plugins/super-plugin', $this->installer->getInstallPath($initial));
        $this->installer->install($repo, $initial);
        $repo->addPackage($initial);
        $this->assertFileExists($this->testDir . '/user/plugins/super-plugin/plugin.php');
        $this->assertFileNotExists($this->testDir . '/user/plugins/super-plugin/vendor-created.txt');

        $this->filesystem->emptyDirectory($this->testDir . '/user/plugins/super-plugin');
        $this->assertFileNotExists($this->testDir . '/user/plugins/super-plugin/plugin.php');
        $this->assertFileNotExists($this->testDir . '/user/plugins/super-plugin/vendor-created.txt');

        $target = $this->pluginPackageFactory(self::SUPPORTED);
        $this->assertEquals('user/plugins/super-plugin', $this->installer->getInstallPath($target));
        $this->installer->update($repo, $initial, $target);
        $this->assertFileExists($this->testDir . '/user/plugins/super-plugin/plugin.php');
        $this->assertFileNotExists($this->testDir . '/user/plugins/super-plugin/vendor-created.txt');
    }

    public function testUpdateVendorDir()
    {
        $repo = new InstalledArrayRepository();

        $initial = $this->pluginPackageFactory(self::SUPPORTED | self::VENDOR_DIR);
        $this->assertEquals('user/plugins/super-plugin', $this->installer->getInstallPath($initial));
        $this->installer->install($repo, $initial);
        $repo->addPackage($initial);
        $this->assertFileExists($this->testDir . '/user/plugins/super-plugin/plugin.php');
        $this->assertFileExists($this->testDir . '/user/plugins/super-plugin/vendor-created.txt');

        $this->filesystem->emptyDirectory($this->testDir . '/user/plugins/super-plugin');
        $this->assertFileNotExists($this->testDir . '/user/plugins/super-plugin/plugin.php');
        $this->assertFileNotExists($this->testDir . '/user/plugins/super-plugin/vendor-created.txt');

        $target = $this->pluginPackageFactory(self::SUPPORTED | self::VENDOR_DIR);
        $this->assertEquals('user/plugins/super-plugin', $this->installer->getInstallPath($target));
        $this->installer->update($repo, $initial, $target);
        $this->assertFileExists($this->testDir . '/user/plugins/super-plugin/plugin.php');
        $this->assertFileExists($this->testDir . '/user/plugins/super-plugin/vendor-created.txt');
    }

    /**
     * Creates a dummy plugin package
     *
     * @param  int     $flags Combination of self::SUPPORTED and self::VENDOR_DIR
     * @param  string  $name  Custom package name of the plugin package, eg joecool/super-plugin
     * @return Package
     */
    protected function pluginPackageFactory(int $flags = 0, string $name='joecool/super-plugin'): Package
    {
        $package = new Package($name, '1.0.0.0', '1.0.0');
        $package->setType('yourls-plugin');
        $package->setInstallationSource('dist');
        $package->setDistType('mock');

        // This package is a YOURLS plugin that supports the YOURLS Installer
        if ($flags & self::SUPPORTED) {
            $package->setRequires([
                new Link($name, 'yourls/composer-installer')
            ]);
        }

        // This package will create/add to a vendor dir
        if ($flags & self::VENDOR_DIR) {
            $package->setExtra([
                'with-vendor-dir' => true
            ]);
        }

        return $package;
    }
}
