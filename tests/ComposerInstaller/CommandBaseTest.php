<?php
namespace YOURLS\ComposerInstaller;

use PHPUnit\Framework\TestCase;
use Composer\Command\BaseCommand;
use YOURLS\ComposerInstaller\Commands\CommandBase;

class CommandBaseTest extends TestCase
{

    /**
     * @var OutputInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $output;

    /**
     * SetUp: create mock of OutputInterface
     */
    protected function setUp()
    {
        $this->output = $this->createMock(\Symfony\Component\Console\Output\OutputInterface::class);
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Command "omglol" failed
     */
    public function testrunInvalidCommand()
    {
        $plugin = new CommandBase();
        $test = $plugin->runComposerCommand(['command'=>'omglol'], $this->output);
    }

    /**
     * Test empty command returns 0 (ie no error)
     */
    public function testrunComposerCommand()
    {
        $plugin = new CommandBase();
        $test = $plugin->runComposerCommand([], $this->output);
        $this->assertSame(0, $test);
    }

}
