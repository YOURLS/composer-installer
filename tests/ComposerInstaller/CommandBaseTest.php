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
    protected function setUp() : void
    {
        $this->output = $this->createMock(\Symfony\Component\Console\Output\OutputInterface::class);
    }


    /**
     * Test invalid command
     */
    public function testrunInvalidCommand()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Command "omglol" failed');
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
